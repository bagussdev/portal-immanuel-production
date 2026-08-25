<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrivateImageStorage
{
    public function store(UploadedFile $file, string $directory, int $rotation = 0): array
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
            Storage::disk('local')->makeDirectory($directory);
            if (! $this->saveImage($image, Storage::disk('local')->path($path), $mimeType)) {
                imagedestroy($image);
                throw new \RuntimeException('Gambar tidak dapat disimpan.');
            }
            imagedestroy($image);
        } else {
            $path = $file->storeAs($directory, basename($path), 'local');
            if ($rotation % 360 !== 0) {
                Storage::disk('local')->delete($path);
                throw new \RuntimeException('Gambar tidak dapat diputar pada server ini.');
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

    private function loadImage(string $path, string $mimeType): \GdImage|false
    {
        return match ($mimeType) {
            'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : false,
            'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : false,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
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
