<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Psr\Log\LoggerInterface;

class ProductImageService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function storeResizedImage(UploadedFile $image, ?int $businessId): string
    {
        $folder = $businessId ? "brands/{$businessId}/images" : 'brands/images';
        $originalName = $image->getClientOriginalName();
        $extension = strtolower((string) $image->extension());

        // Validate extension
        if (!in_array($extension, ['png', 'webp', 'jpg', 'jpeg', 'gif', 'svg'], true)) {
            throw new \RuntimeException("Unsupported image format: {$extension}");
        }

        try {
            $filePath = $image->getRealPath();
            if (!file_exists($filePath)) {
                throw new \RuntimeException("Uploaded file not found at path: {$filePath}");
            }

            // SVG files are vector graphics and cannot be resized. Store them as-is.
            if ($extension === 'svg') {
                $path = $folder . '/' . Str::uuid() . '.svg';
                Storage::disk('public')->putFileAs(
                    $folder,
                    $image,
                    basename($path)
                );
                return $path;
            }

            // Process raster images (PNG, JPG, WebP, GIF)
            $manager = ImageManager::gd();
            $imageInstance = $manager->read($filePath);
            $imageInstance->resize(120, 80);

            $encoder = match ($extension) {
                'png' => new PngEncoder(),
                'webp' => new WebpEncoder(82),
                default => new JpegEncoder(85),
            };

            if (!in_array($extension, ['png', 'webp', 'jpg', 'jpeg', 'gif'], true)) {
                $extension = 'jpg';
            }

            $path = $folder . '/' . Str::uuid() . '.' . $extension;
            $encodedImage = (string) $imageInstance->encode($encoder);

            if (empty($encodedImage)) {
                throw new \RuntimeException("Failed to encode image: {$originalName}");
            }

            Storage::disk('public')->put($path, $encodedImage);

            return $path;
        } catch (\Throwable $e) {
            $this->logger->error('Image processing failed', [
                'file' => $originalName,
                'extension' => $extension,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \RuntimeException(
                "Unable to process uploaded image '{$originalName}': " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    public function delete(?string $imagePath): void
    {
        if (!$imagePath) {
            return;
        }

        try {
            Storage::disk('public')->delete($imagePath);
        } catch (\Throwable $e) {
            $this->logger->warning('Failed to delete image', [
                'path' => $imagePath,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
