<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;

class ImageService
{
    private static function manager(): ImageManager
    {
        return new ImageManager(new Driver());
    }

    public static function store(UploadedFile $file, string $directory, int $maxWidth = 1400, int $quality = 82): string
    {
        if ($file->getMimeType() === 'image/gif') {
            return $file->store($directory, 'public');
        }

        $image = self::manager()->decode($file->getContent());
        $image->scaleDown(width: $maxWidth);

        $path = $directory . '/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($path, (string) $image->encode(new JpegEncoder($quality)));

        return $path;
    }

    public static function storeAvatar(UploadedFile $file): string
    {
        $image = self::manager()->decode($file->getContent());
        $image->cover(400, 400);

        $path = 'avatars/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($path, (string) $image->encode(new JpegEncoder(85)));

        return $path;
    }

    public static function storeBanner(UploadedFile $file): string
    {
        $image = self::manager()->decode($file->getContent());
        $image->scaleDown(width: 1500);

        $path = 'banners/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($path, (string) $image->encode(new JpegEncoder(80)));

        return $path;
    }
}
