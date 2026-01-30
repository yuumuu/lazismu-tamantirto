<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Models\MediaLibrary;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class MediaService
{
    private const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    private const ALLOWED_VIDEO_TYPES = ['mp4', 'webm', 'mov'];

    private const ALLOWED_DOCUMENT_TYPES = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

    private const MAX_IMAGE_WIDTH = 1920;

    private const WEBP_QUALITY = 85;

    /**
     * Upload a file to the media library.
     */
    public function upload(UploadedFile $file, ?User $user, ?string $altText = null): MediaLibrary
    {
        $fileType = $this->determineFileType($file);
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Generate unique filename
        $filename = $this->generateFilename($extension);

        // Process and store file
        $path = $this->storeFile($file, $fileType, $filename);

        // Get file dimensions if image
        [$width, $height] = $this->getImageDimensions($file, $fileType);

        return MediaLibrary::create([
            'filename' => $filename,
            'original_name' => $originalName,
            'file_path' => $path,
            'file_type' => $fileType,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'alt_text' => $altText,
            'uploaded_by' => $user?->id,
        ]);
    }

    /**
     * Determine the file type.
     */
    private function determineFileType(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, self::ALLOWED_IMAGE_TYPES)) {
            return 'image';
        }

        if (in_array($extension, self::ALLOWED_VIDEO_TYPES)) {
            return 'video';
        }

        if (in_array($extension, self::ALLOWED_DOCUMENT_TYPES)) {
            return 'document';
        }

        throw new \InvalidArgumentException('Tipe file tidak didukung.');
    }

    /**
     * Generate a unique filename.
     */
    private function generateFilename(string $extension): string
    {
        return Str::ulid().'.'.$extension;
    }

    /**
     * Store the file with optimization.
     */
    private function storeFile(UploadedFile $file, string $fileType, string $filename): string
    {
        $directory = "media/{$fileType}s";

        if ($fileType === 'image') {
            return $this->storeOptimizedImage($file, $directory, $filename);
        }

        return $file->storeAs($directory, $filename, 'public');
    }

    /**
     * Store optimized image with WebP conversion.
     */
    private function storeOptimizedImage(UploadedFile $file, string $directory, string $filename): string
    {
        $image = Image::read($file);

        // Resize if too large
        if ($image->width() > self::MAX_IMAGE_WIDTH) {
            $image->scale(width: self::MAX_IMAGE_WIDTH);
        }

        // Convert to WebP for better compression
        $webpFilename = pathinfo($filename, PATHINFO_FILENAME).'.webp';
        $path = "{$directory}/{$webpFilename}";

        $encoded = $image->toWebp(self::WEBP_QUALITY);

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Get image dimensions.
     */
    private function getImageDimensions(UploadedFile $file, string $fileType): array
    {
        if ($fileType !== 'image') {
            return [null, null];
        }

        $image = Image::read($file);

        return [$image->width(), $image->height()];
    }

    /**
     * Delete a media file.
     */
    public function delete(MediaLibrary $media): bool
    {
        // Delete file from storage
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        // Delete database record
        return $media->deleteOrFail();
    }

    /**
     * Get media statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total_files' => MediaLibrary::count(),
            'total_images' => MediaLibrary::images()->count(),
            'total_videos' => MediaLibrary::videos()->count(),
            'total_documents' => MediaLibrary::documents()->count(),
            'total_size' => MediaLibrary::sum('file_size'),
        ];
    }
}
