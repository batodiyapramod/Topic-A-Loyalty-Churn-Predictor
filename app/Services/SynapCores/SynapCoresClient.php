<?php

namespace App\Services\SynapCores;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SynapCoresClient
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.synapcores.base_url');
        $this->apiKey = config('services.synapcores.api_key');
    }

    /**
     * Execute a raw SQL statement or machine learning query on SynapCores AIDB
     */
    // public function query(string $sql, array $bindings = []): array
    // {
    //     // Use the API key directly as a Bearer token or custom header
    //     // Note: If SynapCores expects an X-API-Key header instead, swap .withToken() for .withHeaders()
    //     $response = Http::withToken($this->apiKey)
    //         ->timeout(30)
    //         ->post("{$this->baseUrl}/v1/query/execute", [
    //             'sql' => $sql,
    //             'bindings' => $bindings,
    //         ]);

    //     if ($response->failed()) {
    //         Log::error('SynapCores Query Execution Failure', [
    //             'sql' => $sql,
    //             'status' => $response->status(),
    //             'error' => $response->body()
    //         ]);

    //         throw new Exception("SynapCores SQL error: " . ($response->json('error.message') ?? 'Unknown Error'));
    //     }

    //     return $response->json('results', []);
    // }
    public static function query(string $sql, array $bindings = [])
    {
        $instance = app(self::class);

        return Http::withHeaders([
            'Authorization' => "Bearer " . $instance->apiKey,
            'Content-Type' => 'application/json',
        ])
        ->timeout(300)
        ->post("{$instance->baseUrl}/v1/query/execute", [
            'sql' => $sql,
            'params' => $bindings
        ]);
    }
}
