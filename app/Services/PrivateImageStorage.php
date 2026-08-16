<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrivateImageStorage
{
    public function store(UploadedFile $file, string $directory): array
    {
        $directory = trim($directory, '/');
        $source = $file->getRealPath();

        if (function_exists('imagecreatefromstring') && is_string($source)) {
            $image = @imagecreatefromstring((string) file_get_contents($source));
            if ($image !== false) {
                $image = $this->orient($image, $file);
                $width = imagesx($image);
                $height = imagesy($image);
                $scale = min(1, 2000 / max($width, $height));
                $targetWidth = max(1, (int) round($width * $scale));
                $targetHeight = max(1, (int) round($height * $scale));
                $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
                $white = imagecolorallocate($canvas, 255, 255, 255);
                imagefill($canvas, 0, 0, $white);
                imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

                ob_start();
                imagejpeg($canvas, null, 82);
                $contents = (string) ob_get_clean();
                imagedestroy($canvas);
                imagedestroy($image);

                $path = $directory.'/'.Str::uuid().'.jpg';
                Storage::disk('local')->put($path, $contents);

                return [
                    'path' => $path,
                    'mime_type' => 'image/jpeg',
                    'size_bytes' => Storage::disk('local')->size($path),
                ];
            }
        }

        $extension = strtolower($file->guessExtension() ?: 'jpg');
        $path = $file->storeAs($directory, Str::uuid().'.'.$extension, 'local');

        return [
            'path' => $path,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size_bytes' => Storage::disk('local')->size($path),
        ];
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
