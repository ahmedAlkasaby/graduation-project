<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\AdminHelperTrait;
use App\Traits\ApiResponseTrait;
use App\Traits\HelperTrait;
use App\Traits\NotifiTrait;
use App\Traits\ResponseMessageTrait;

/**
 * Base for every API controller: the response shape, the page size rules, and
 * the settings an endpoint is likely to echo back to the app.
 */
class ApiHomeController extends Controller
{
    use AdminHelperTrait, ApiResponseTrait, HelperTrait, NotifiTrait, ResponseMessageTrait;

    protected int $perPage;

    protected int $limit = 10;

    protected string $site_mark = ' - ';

    protected array $options = [];

    protected string $site_language = 'en';

    protected ?string $site_email = null;

    protected ?string $site_phone = null;

    protected string $site_url;

    protected string $phone_code;

    protected string $phone_regex;

    protected array $lang_array = ['ar', 'en'];

    public function __construct()
    {
        $this->perPage = min((int) request()->get('per_page', $this->limit) ?: $this->limit, 50);

        $settings = settings();

        $this->options = $settings->all();
        $this->site_url = url('');
        $this->site_email = $settings->get('site_email');
        $this->site_phone = $settings->get('site_phone');
        $this->site_language = $settings->get('site_language', 'en');
        $this->phone_code = $settings->getPhoneCode();
        $this->phone_regex = $settings->getPhoneRegex();
    }

    /**
     * Honour an "all_data=0" request by returning the whole set unpaginated,
     * and report which of the two happened so the caller can attach meta.
     *
     * @return array{0: mixed, 1: bool}
     */
    protected function getDataWithPagination($query, $request): array
    {
        $allData = isset($request['all_data']) ? (int) $request['all_data'] : 1;
        $limit = isset($request['limit']) ? (int) $request['limit'] : $this->limit;

        if ($allData <= 0) {
            return [$query->get(), false];
        }

        return [$query->paginate($limit > 0 ? $limit : $this->limit), true];
    }
}
