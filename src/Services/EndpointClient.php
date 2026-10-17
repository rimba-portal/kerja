<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Rimba\Work\Models\Endpoint;
use RuntimeException;

final class EndpointClient
{
    public function request(Endpoint $endpoint, string $method, string $path, array $data = []): array
    {
        if (! $endpoint->is_active) {
            throw new RuntimeException("Endpoint [{$endpoint->code}] is inactive.");
        }

        $url = rtrim($endpoint->base_url, '/').'/'.ltrim($path, '/');
        $request = Http::acceptJson()->asJson()->timeout($endpoint->timeout_seconds);
        $request = $this->applyAuthentication($request, $endpoint);

        $response = $this->send($request, strtoupper($method), $url, $data);
        $response->throw();

        $json = $response->json();
        if ($json === null) {
            return [];
        }

        if (! is_array($json)) {
            throw new RuntimeException("Endpoint [{$endpoint->code}] returned a non-JSON-object/array response.");
        }

        return $json;
    }

    private function applyAuthentication(PendingRequest $request, Endpoint $endpoint): PendingRequest
    {
        if (blank($endpoint->auth_profile)) {
            return $request;
        }

        $profile = config("bites.kerja.endpoint_auth.{$endpoint->auth_profile}");
        if (! is_array($profile)) {
            throw new RuntimeException("Endpoint auth profile [{$endpoint->auth_profile}] is not configured.");
        }

        return match ($profile['type'] ?? null) {
            'bearer' => $request->withToken((string) ($profile['token'] ?? '')),
            'basic' => $request->withBasicAuth((string) ($profile['username'] ?? ''), (string) ($profile['password'] ?? '')),
            'header' => $request->withHeader((string) ($profile['name'] ?? ''), (string) ($profile['value'] ?? '')),
            default => throw new RuntimeException("Unsupported Endpoint auth profile [{$endpoint->auth_profile}]."),
        };
    }

    private function send(PendingRequest $request, string $method, string $url, array $data): Response
    {
        return match ($method) {
            'GET' => $request->get($url, $data),
            'POST' => $request->post($url, $data),
            'PUT' => $request->put($url, $data),
            'PATCH' => $request->patch($url, $data),
            'DELETE' => $request->delete($url, $data),
            default => throw new RuntimeException("HTTP method [{$method}] is not supported."),
        };
    }
}
