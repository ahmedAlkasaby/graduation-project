<?php

namespace App\Traits;

use App\Services\ImageHandlerService;
use Illuminate\Http\UploadedFile;

/**
 * The odds and ends every controller shares: the API status strings, uploads,
 * and the two-language name/content pickers.
 *
 * Query helpers do not live here. They used to — whereActive($query, ...) and
 * seventeen siblings — and BaseBuilder replaced all of them with real Eloquent
 * builder methods. Add new query verbs to a builder, not to this trait.
 */
trait HelperTrait
{
    protected $successStatus = 'success';

    protected $errorStatus = 'error';

    protected $error = 'error';

    protected $success = 'success';

    protected $unauthenticatedStatus = 'unauthenticated';

    protected $unauthorizedStatus = 'unauthorized';

    /*
    |--------------------------------------------------------------------------
    | Uploads
    |--------------------------------------------------------------------------
    |
    | These used to be a second upload implementation writing straight into
    | public/ with its own naming scheme. They now delegate to
    | ImageHandlerService so there is one place that decides where a file goes,
    | and one disk to repoint when uploads move off the app server.
    |
    */

    protected function imageHandler(): ImageHandlerService
    {
        return app(ImageHandlerService::class);
    }


    public function uploadImage($image, $folder_path = '', $is_upload = 1)
    {
        if (! $image instanceof UploadedFile) {
            return null;
        }

        $folder = $folder_path !== '' ? $folder_path : date('Y/m');

        if (strtolower((string) $image->getClientOriginalExtension()) === 'svg') {
            return $this->imageHandler()->storeRaw($image, $folder);
        }

        return $this->imageHandler()->storeFile($image, $folder);
    }

    public function deleteFile($file)
    {
        $this->imageHandler()->deleteImage($file);
    }

    public function uploadFile($file, $folder_path = '')
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        return $this->imageHandler()->storeRaw($file, 'files'.($folder_path !== '' ? '/'.$folder_path : ''));
    }
    public function getImage($image)
    {
        if ($image != '' && $image != null) {
            return str_replace(url(''), '', $image);
        }
    }

    public function getName($name_ar, $name_en)
    {
        return [
            'ar' => htmlspecialchars($name_ar, ENT_NOQUOTES, 'UTF-8'),
            'en' => trim(filter_var($name_en, FILTER_SANITIZE_STRING)),
        ];
    }

    public function getContent($name_ar, $name_en)
    {
        return [
            'ar' => trim($name_ar),
            'en' => trim($name_en),
        ];
    }

    /**
     * Size in bytes of a stored upload, or 0 if it is no longer there.
     */
    public function getFileSize($filePath): int
    {
        $handler = $this->imageHandler();

        return $handler->exists($filePath) ? (int) $handler->disk()->size(ltrim((string) $filePath, '/')) : 0;
    }
}
