<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrivateImageStorage
{
    public function store(UploadedFile $file, string $directory, int $rotation = 0, array $crop = []): array
    {
        $directory = trim($directory, '/');
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';
        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg'),
        };
        $path = $directory.'/'.Str::uuid().'.'.$extension;

        $image = $this->loadImage($file->getRealPath(), $mimeType);
        if ($image) {
            $image = $this->orient($image, $file);
            $image = $this->rotateImage($image, $rotation, $mimeType);
            if ($crop) {
                $originalPath = $this->originalPath($path);
                Storage::disk('local')->makeDirectory(dirname($originalPath));
                if (! $this->saveImage($image, Storage::disk('local')->path($originalPath), $mimeType)) {
                    imagedestroy($image);
                    throw new \RuntimeException('Gambar asli tidak dapat disimpan.');
                }
                $image = $this->cropAndResize($image, $crop, $mimeType);
            }
            Storage::disk('local')->makeDirectory($directory);
            if (! $this->saveImage($image, Storage::disk('local')->path($path), $mimeType)) {
                imagedestroy($image);
                throw new \RuntimeException('Gambar tidak dapat disimpan.');
            }
            imagedestroy($image);
        } else {
            $path = $file->storeAs($directory, basename($path), 'local');
            if ($rotation % 360 !== 0 || $crop) {
                Storage::disk('local')->delete($path);
                throw new \RuntimeException('Gambar tidak dapat diproses pada server ini.');
            }
        }

        return [
            'path' => $path,
            'mime_type' => $mimeType,
            'size_bytes' => Storage::disk('local')->size($path),
        ];
    }

    public function rotateStored(string $path, int $rotation): bool
    {
        $rotation = (($rotation % 360) + 360) % 360;
        if ($rotation === 0) {
            return true;
        }

        $originalPath = $this->ensureOriginal($path);
        if (! $originalPath) {
            return false;
        }

        foreach (array_unique([$path, $originalPath]) as $targetPath) {
            if (! $this->rotateStoredFile($targetPath, $rotation)) {
                return false;
            }
        }

        return true;
    }

    public function cropStored(string $path, array $crop): bool
    {
        $disk = Storage::disk('local');
        if (! $disk->exists($path)) {
            return false;
        }

        $originalPath = $this->ensureOriginal($path);
        if (! $originalPath) {
            return false;
        }

        $sourcePath = $disk->path($originalPath);
        $absolutePath = $disk->path($path);
        $mimeType = mime_content_type($sourcePath) ?: 'application/octet-stream';
        $image = $this->loadImage($sourcePath, $mimeType);
        if (! $image) {
            return false;
        }

        $image = $this->cropAndResize($image, $crop, $mimeType);
        $saved = $this->saveImage($image, $absolutePath, $mimeType);
        imagedestroy($image);

        return $saved;
    }

    public function deleteStored(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('local')->delete([$path, $this->originalPath($path)]);
    }

    private function loadImage(string $path, string $mimeType): \GdImage|false
    {
        return match ($mimeType) {
            'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : false,
            'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : false,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
    }

    private function rotateStoredFile(string $path, int $rotation): bool
    {
        $disk = Storage::disk('local');
        if (! $disk->exists($path)) {
            return false;
        }

        $absolutePath = $disk->path($path);
        $mimeType = mime_content_type($absolutePath) ?: 'application/octet-stream';
        $image = $this->loadImage($absolutePath, $mimeType);
        if (! $image) {
            return false;
        }

        $image = $this->rotateImage($image, $rotation, $mimeType);
        $saved = $this->saveImage($image, $absolutePath, $mimeType);
        imagedestroy($image);

        return $saved;
    }

    private function ensureOriginal(string $path): ?string
    {
        $disk = Storage::disk('local');
        if (! $disk->exists($path)) {
            return null;
        }

        $originalPath = $this->originalPath($path);
        if ($disk->exists($originalPath)) {
            return $originalPath;
        }

        $disk->makeDirectory(dirname($originalPath));

        return $disk->copy($path, $originalPath) ? $originalPath : null;
    }

    private function originalPath(string $path): string
    {
        $directory = str_replace('\\', '/', dirname($path));

        return trim($directory, './').'/originals/'.basename($path);
    }

    private function rotateImage(\GdImage $image, int $rotation, string $mimeType): \GdImage
    {
        $rotation = (($rotation % 360) + 360) % 360;
        if ($rotation === 0) {
            return $image;
        }

        $background = $mimeType === 'image/jpeg'
            ? imagecolorallocate($image, 255, 255, 255)
            : imagecolorallocatealpha($image, 0, 0, 0, 127);
        $rotated = imagerotate($image, -$rotation, $background);
        if ($rotated === false) {
            return $image;
        }

        if ($mimeType !== 'image/jpeg') {
            imagealphablending($rotated, false);
            imagesavealpha($rotated, true);
        }
        imagedestroy($image);

        return $rotated;
    }

    private function saveImage(\GdImage $image, string $path, string $mimeType): bool
    {
        return match ($mimeType) {
            'image/jpeg' => imagejpeg($image, $path, 90),
            'image/png' => imagepng($image, $path, 6),
            'image/webp' => function_exists('imagewebp') && imagewebp($image, $path, 88),
            default => false,
        };
    }

    private function cropAndResize(\GdImage $image, array $crop, string $mimeType): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $aspectWidth = max(1, (float) ($crop['aspect_width'] ?? 1));
        $aspectHeight = max(1, (float) ($crop['aspect_height'] ?? 1));
        $aspect = $aspectWidth / $aspectHeight;
        $zoom = min(max((float) ($crop['zoom'] ?? 1), 1), 4);
        $positionX = min(max((float) ($crop['x'] ?? 50), 0), 100) / 100;
        $positionY = min(max((float) ($crop['y'] ?? 50), 0), 100) / 100;

        if ($width / $height > $aspect) {
            $baseHeight = $height;
            $baseWidth = $height * $aspect;
        } else {
            $baseWidth = $width;
            $baseHeight = $width / $aspect;
        }

        $cropWidth = max(1, min($width, (int) round($baseWidth / $zoom)));
        $cropHeight = max(1, min($height, (int) round($baseHeight / $zoom)));
        $cropX = (int) round(($width - $cropWidth) * $positionX);
        $cropY = (int) round(($height - $cropHeight) * $positionY);
        $cropped = imagecrop($image, [
            'x' => $cropX,
            'y' => $cropY,
            'width' => $cropWidth,
            'height' => $cropHeight,
        ]);
        if ($cropped === false) {
            return $image;
        }

        $outputWidth = max(1, (int) ($crop['output_width'] ?? $cropWidth));
        $outputHeight = max(1, (int) ($crop['output_height'] ?? $cropHeight));
        $canvas = imagecreatetruecolor($outputWidth, $outputHeight);
        if ($mimeType === 'image/jpeg') {
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);
        } else {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefill($canvas, 0, 0, $transparent);
        }

        imagecopyresampled(
            $canvas,
            $cropped,
            0,
            0,
            0,
            0,
            $outputWidth,
            $outputHeight,
            imagesx($cropped),
            imagesy($cropped),
        );
        imagedestroy($cropped);
        imagedestroy($image);

        return $canvas;
    }

    private function orient(\GdImage $image, UploadedFile $file): \GdImage
    {
        if ($file->getMimeType() !== 'image/jpeg' || ! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($file->getRealPath());
        $orientation = (int) ($exif['Orientation'] ?? 1);
        $angle = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        if ($rotated === false) {
            return $image;
        }
        imagedestroy($image);

        return $rotated;
    }
}
