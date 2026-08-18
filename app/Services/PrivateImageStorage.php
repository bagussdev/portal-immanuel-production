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

        $extension = strtolower(
            $file->getClientOriginalExtension()
                ?: $file->guessExtension()
                ?: 'jpg'
        );

        $path = $file->storeAs(
            $directory,
            Str::uuid() . '.' . $extension,
            'local'
        );

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
