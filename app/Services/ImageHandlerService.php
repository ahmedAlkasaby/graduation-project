<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageHandlerService
{
    public function disk(): Filesystem
    {
        return Storage::disk('uploads');
    }

    public function exists(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        return $this->disk()->exists($this->extractStoragePath($path));
    }

    /** Public URL for a stored path. */
    public function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return $this->disk()->url($this->extractStoragePath($path));
    }

    /** Store an uploaded image file and return its public path. */
    public function storeFile(\Illuminate\Http\UploadedFile $file, string $folder, int $width = 800, int $height = 600): string
    {
        $folder = $this->normaliseFolder($folder);

        $fileName = uniqid().'_'.time().'.'.$file->getClientOriginalExtension();

        $path = $this->disk()->putFileAs($folder, $file, $fileName, 'public');

        return $this->buildPath($path);
    }

    /** Store a raw file (svg, documents) without any processing. */
    public function storeRaw(\Illuminate\Http\UploadedFile $file, string $folder): string
    {
        $folder = $this->normaliseFolder($folder);

        $fileName = uniqid().'_'.time().'.'.$file->getClientOriginalExtension();

        $path = $this->disk()->putFileAs($folder, $file, $fileName, 'public');

        return $this->buildPath($path);
    }

    /** Ensure the folder lives under uploads/ so buildPath and the disk agree. */
    protected function normaliseFolder(string $folder): string
    {
        $folder = trim($folder, '/');

        return str_starts_with($folder, 'uploads/') || $folder === 'uploads'
            ? $folder
            : 'uploads/'.$folder;
    }

    public function uploadImage(
        string $folder,
        Request $request,
        int $width = 800,
        int $height = 600,
        string $fileName = 'image'
    ): ?string {
        if (!$request->hasFile($fileName)) {
            return null;
        }

        $file = $request->file($fileName);

        $extension = $file->getClientOriginalExtension();

        $imageName = uniqid() . '_' . time() . '.' . $extension;

        $path = $this->disk()->putFileAs(
            $folder,
            $file,
            $imageName,
            'public'
        );

        return $this->buildPath($path);
    }

    public function deleteImage(array|string|null $path): void
    {
        if (!$path) {
            return;
        }

        if (is_array($path)) {
            foreach ($path as $item) {
                $this->deleteSingleImage($item);
            }

            return;
        }

        $this->deleteSingleImage($path);
    }

    protected function deleteSingleImage(?string $imagePath): void
    {
        if (!$imagePath) {
            return;
        }

        $path = $this->extractStoragePath($imagePath);

        if (!$path) {
            return;
        }

        if ($this->disk()->exists($path)) {
            $this->disk()->delete($path);
        }
    }

    public function editImage(
        Request $request,
        $model,
        string $folder,
        int $width = 800,
        int $height = 600,
        string $field = 'image'
    ): ?string {
        $currentImage = $model->$field ?? null;

        if (!$request->hasFile($field)) {
            return $currentImage;
        }

        if ($currentImage) {
            $this->deleteImage($currentImage);
        }

        return $this->uploadImage(
            $folder,
            $request,
            $width,
            $height,
            $field
        );
    }

    public function handleImage(
        Request $request,
        $model,
        string $folder,
        string $field = 'image'
    ): ?string {
        $currentImage = $model->$field ?? null;

        if (
            $request->input("{$field}_removed") == '1'
            && !$request->hasFile($field)
        ) {
            $this->deleteImage($currentImage);

            return null;
        }

        if ($request->hasFile($field)) {
            if ($currentImage) {
                $this->deleteImage($currentImage);
            }

            return $this->uploadImage(
                $folder,
                $request,
                800,
                600,
                $field
            );
        }

        return $currentImage;
    }

    public function syncImagesByName(
        $relation,
        array $attachments,
        string $column = 'image'
    ): void {
        $existingModels = $relation->get();

        $existingPaths = $existingModels
            ->pluck($column)
            ->toArray();

        foreach ($existingModels as $model) {
            if (!in_array($model->$column, $attachments)) {
                $this->deleteImage($model->$column);

                $model->forceDelete();
            }
        }

        foreach ($attachments as $path) {
            if (!in_array($path, $existingPaths)) {
                $relation->create([
                    $column => $path,
                ]);
            }
        }
    }

    public function uploadFiles(
        Request $request,
        string $folder,
        string $field
    ): ?array {
        if (!$request->hasFile($field)) {
            return null;
        }

        $uploadedFiles = [];

        foreach ($request->file($field) as $file) {
            $fileName = uniqid() . '_' . time() . '.' .
                $file->getClientOriginalExtension();

            $path = $this->disk()->putFileAs(
                $folder,
                $file,
                $fileName,
                'public'
            );

            $uploadedFiles[] = $this->buildPath($path);
        }

        return $uploadedFiles;
    }

    public function editFiles(
        Request $request,
        string $folder,
        string $field,
        ?array $existingFiles = null
    ): ?array {
        if (!$request->hasFile($field)) {
            return $existingFiles;
        }

        if ($existingFiles) {
            $this->deleteImage($existingFiles);
        }

        return $this->uploadFiles(
            $request,
            $folder,
            $field
        );
    }

    protected function buildPath(string $path): string
    {
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'uploads/')) {
            return '/' . $path;
        }

        return '/uploads/' . $path;
    }

    protected function extractStoragePath(string $url): string
    {

        $path = str_replace(url(''), '', $url);

        return ltrim($path, '/');
    }
}
