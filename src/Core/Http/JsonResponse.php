<?php

namespace App\Core\Http;

use React\Http\Response;

class JsonResponse extends Response
{
    /**
     * JsonResponse constructor.
     * @param int $statusCode
     * @param null $data
     * @param array $headers
     */
    public function __construct(int $statusCode, $data = null, array $headers = [])
    {
        $body = $data ? json_encode($data) : null;

        $headers['Content-Type'] = 'application/json';

        parent::__construct($statusCode, $headers, $body);
    }

    /**
     * @return JsonResponse
     */
    public static function created(): self
    {
        return new self(201);
    }

    /**
     * @param string $error
     * @return JsonResponse
     */
    public static function badRequest(string $error): self
    {
        return new self(400, ['error' => $error]);
    }
}
