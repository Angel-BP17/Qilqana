<?php

namespace App\Services\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageService
{
    /**
     * Store and optimize an image.
     *
     * @return string Path of the stored file
     */
    public function storeAndOptimize(
        UploadedFile $file,
        string $folder,
        ?int $quality = 50,
        ?string $customName = null,
        int $maxWidth = 1600
    ): string {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $customName
            ? "{$customName}.{$extension}"
            : $file->hashName();

        $path = $file->storeAs($folder, $filename, 'local');
        $absolutePath = Storage::disk('local')->path($path);

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $manager = new ImageManager(['driver' => 'gd']);
            $image = $manager->make($absolutePath);

            if ($image->width() > $maxWidth || $image->height() > $maxWidth) {
                $image->resize($maxWidth, $maxWidth, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $finalQuality = $quality ?? ($extension === 'png' ? 8 : 50);
            $image->save($absolutePath, $finalQuality);
        }

        return $path;
    }

    /**
     * Delete a file from storage if it exists.
     */
    public function deleteIfExists(?string $path): void
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
