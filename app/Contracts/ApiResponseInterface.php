<?php

namespace App\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

interface ApiResponseInterface
{
    /**
     * Create API response.
     *
     * @param int $status
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function response($status = Response::HTTP_OK, bool $message = true, $data = [], ...$extraData);

    /**
     * Create successful (200) API response.
     *
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function ok(bool $message = true, $data = [], ...$extraData);

    /**
     * Create Not found (404) API response.
     *
     * @param string|null $message
     *
     * @return JsonResponse
     */
    public function notFound(bool $message = true);

    /**
     * Create Validation (422) API response.
     *
     * @param string|null $message
     * @param array $errors
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function validationError(bool $message = true, $errors = [], ...$extraData);

    /**
     * Create Validation (403) API response.
     *
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function forbidden(bool $message = false, $data = [], ...$extraData);

    /**
     * Create Validation (401) API response.
     *
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function unauthorized(bool $message = false, $data = [], ...$extraData);

    /**
     * Create Validation (400) API response.
     *
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function badRequest(bool $message = false, $data = [], ...$extraData);

    /**
     * Create Server error (500) API response.
     *
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function error(bool $message = false, $data = [], ...$extraData);
}
