<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileUploadService
{
    private const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];

    private const DOCUMENT_MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
    ];

    public function store(UploadedFile $file, string $directory): array
    {
        $type = $this->detectType($file);
        $targetDirectory = public_path('uploads/'.$directory);

        if (! File::exists($targetDirectory)) {
            File::makeDirectory($targetDirectory, 0755, true);
        }

        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'bin';
        $filename = Str::uuid()->toString().'.'.$extension;
        $file->move($targetDirectory, $filename);

        return [
            'file_name' => $file->getClientOriginalName(),
            'file_path' => trim($directory.'/'.$filename, '/'),
            'file_type' => $type,
        ];
    }

    public function storeAvatar(UploadedFile $file, int $userId): string
    {
        $this->guardMimeType($file->getMimeType(), self::IMAGE_MIME_TYPES);

        $targetDirectory = public_path('uploads/avatars');

        if (! File::exists($targetDirectory)) {
            File::makeDirectory($targetDirectory, 0755, true);
        }

        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
        $filename = 'user-'.$userId.'-'.Str::uuid()->toString().'.'.$extension;
        $file->move($targetDirectory, $filename);

        return 'avatars/'.$filename;
    }

    public function remove(?string $relativePath): void
    {
        if (! $relativePath || in_array($relativePath, AvatarDefaults::reservedPaths(), true)) {
            return;
        }

        $absolutePath = public_path('uploads/'.$relativePath);

        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }

    private function detectType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();

        if (in_array($mimeType, self::IMAGE_MIME_TYPES, true)) {
            return 'image';
        }

        $this->guardMimeType($mimeType, self::DOCUMENT_MIME_TYPES);

        return 'document';
    }

    private function guardMimeType(?string $mimeType, array $allowedMimeTypes): void
    {
        abort_unless($mimeType && in_array($mimeType, $allowedMimeTypes, true), 422, 'Unsupported file type.');
    }
}
