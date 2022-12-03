<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Contracts\ApiResponseInterface;
use Illuminate\Support\Facades\Log;

class ApiResponse implements ApiResponseInterface
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
    public function response($status = Response::HTTP_OK, bool $message = true, $data = [], ...$extraData)
    {
        $json = [
            config('api_response.keys.message') => $message,
            config('api_response.keys.data') => $data,
        ];

        if (is_countable($data) && config('api_response.include_data_count', false) && !empty($data)) {
            $json = array_merge($json, [config('api_response.keys.data_count') => config('api_response.stringify') ? strval(count($data)) : count($data)]);
        }

        if ($extraData) {
            foreach ($extraData as $extra) {
                $json = array_merge($json, $extra);
            }
        }

        return (config('api_response.match_status')) ? response()->json($json, $status, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE) : response()->json($json);
    }

    /**
     * Create successful (200) API response.
     *
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function ok(bool $message = true, $data = [], ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api_response.messages.success');
        }

        return $this->response(Response::HTTP_OK, $message, $data, ...$extraData);
    }

    /**
     * Create successful (200) API response.
     *
     * @param null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function success($message = true, $data = [], ...$extraData)
    {
        return $this->ok($message, $data, ...$extraData);
    }

    /**
     * Create Not found (404) API response.
     *
     * @param string|null $message
     *
     * @return JsonResponse
     */
    public function notFound(bool $message = true, ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api_response.messages.notfound');
        }

        return $this->response(Response::HTTP_NOT_FOUND, $message, [], ...$extraData);
    }

    /**
     * Create Validation (422) API response.
     *
     * @param string|null $message
     * @param array $errors
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function validationError(bool $message = false, $errors = [], ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api_response.messages.validation');
        }

        return $this->response(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $errors, ...$extraData);
    }

    /**
     * Create Validation (403) API response.
     *
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function forbidden(bool $message = false, $data = [], ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api_response.messages.forbidden');
        }

        return $this->response(Response::HTTP_FORBIDDEN, $message, $data, ...$extraData);
    }

    /**
     * Create Validation (401) API response.
     *
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function unauthorized(bool $message = false, $data = [], ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api_response.messages.unauthorized');
        }

        return $this->response(Response::HTTP_UNAUTHORIZED, $message, $data, ...$extraData);
    }


    /**
     * Create Validation (400) API response.
     *
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function badRequest(bool $message = false, $data = [], ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api_response.messages.bad_request');
        }

        return $this->response(Response::HTTP_BAD_REQUEST, $message, $data, ...$extraData);
    }

    /**
     * Create Server error (500) API response.
     *
     * @param string|null $message
     * @param array $data
     * @param array $extraData
     *
     * @return JsonResponse
     */
    public function error(bool $message = false, $data = [], ...$extraData)
    {
        if (is_null($message)) {
            $message = config('api_response.messages.error');
        }
        Log::info('API errors ' . $message);
        return $this->response(Response::HTTP_OK, $message, $data, ...$extraData);
    }
}
