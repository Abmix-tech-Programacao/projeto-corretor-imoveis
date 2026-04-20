<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesBrokerPhoto
{
    private function storeBrokerPhoto(UploadedFile $file): string
    {
        $path = $file->store('brokers/photos', 'public');

        return Storage::url($path);
    }

    private function deleteBrokerPhotoIfInternal(?string $path): void
    {
        if (! $path || ! Str::startsWith($path, '/storage/')) {
            return;
        }

        $storagePath = Str::after($path, '/storage/');
        Storage::disk('public')->delete($storagePath);
    }
}
