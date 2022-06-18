<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

trait Token
{
    /**
     * Generate brick token
     */
    public function generateToken()
    {
        $endpoint = config('api.brick_url') . 'v1/auth/token';
        $response = Http::get($endpoint, [
            'clientId' => config('api.brick_client_id'),
            'clientSecret' => config('api.brick_client_secret')
        ]);

        $data = [
            'token' => $response->data->access_token
        ];

        return $response->json($data, 200);
    }
}