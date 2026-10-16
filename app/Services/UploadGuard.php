<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * PHP accepts at most `max_file_uploads` files per request and discards the
 * rest without a word — the form comes back saved, minus some images. A product
 * with a main image and a few variant galleries reaches that ceiling easily, so
 * a request that arrives at the limit is treated as truncated and rejected.
 */
class UploadGuard
{
    public function limit(): int
    {
        return (int) (ini_get('max_file_uploads') ?: 20);
    }

    /**
     * Files actually received, counted across every input including nested
     * arrays such as variant_groups[0][gallery_files][].
     */
    public function count(Request $request): int
    {
        return $this->countIn($request->allFiles());
    }

    private function countIn(array $files): int
    {
        $total = 0;

        foreach ($files as $file) {
            $total += is_array($file) ? $this->countIn($file) : ($file instanceof UploadedFile ? 1 : 0);
        }

        return $total;
    }

    /**
     * Hitting the limit exactly is indistinguishable from having been cut off
     * there, so both are refused rather than risking a silent loss.
     */
    public function exceeded(Request $request): bool
    {
        return $this->count($request) >= $this->limit();
    }

    public function message(): string
    {
        return __('Too many images in one save. Please upload at most :count at a time.', [
            'count' => $this->limit() - 1,
        ]);
    }
}
