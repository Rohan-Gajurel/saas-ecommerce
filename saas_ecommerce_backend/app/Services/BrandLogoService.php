<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class BrandLogoService
{
    public function storeResizedLogo(UploadedFile $logo, ?int $businessId): string
    {
        $folder = $businessId ? "brands/{$businessId}/logos" : 'brands/logos';
        $extension = strtolower((string) $logo->extension());
        $manager = ImageManager::gd();

        try {
            $image = $manager
                ->read($logo->getRealPath())
                ->resize(120, 80);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Unable to process uploaded logo image.', previous: $e);
        }

        $encoder = match ($extension) {
            'png' => new PngEncoder(),
            'webp' => new WebpEncoder(82),
            default => new JpegEncoder(85),
        };

        if (!in_array($extension, ['png', 'webp', 'jpg', 'jpeg'], true)) {
            $extension = 'jpg';
        }

        if ($extension === 'png') {
            $fileExtension = 'png';
        } elseif ($extension === 'webp') {
            $fileExtension = 'webp';
        } else {
            $fileExtension = 'jpg';
        }

        $path = $folder . '/' . Str::uuid() . '.' . $fileExtension;
        Storage::disk('public')->put($path, (string) $image->encode($encoder));

        return $path;
    }

    public function delete(?string $logoPath): void
    {
        if (!$logoPath) {
            return;
        }

        Storage::disk('public')->delete($logoPath);
    }
}
