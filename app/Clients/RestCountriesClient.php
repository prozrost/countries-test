<?php

namespace App\Clients;

use App\Contracts\CountryListProvider;
use Illuminate\Support\Facades\Http;

/**
 * REST Countries API implementation. All REST Countries–specific logic lives here.
 * Replace this (or add another provider) to swap the external API.
 */
class RestCountriesClient implements CountryListProvider
{
    public function __construct(
        private readonly string $url,
        private readonly int $timeout = 15,
        private readonly int $retryTimes = 2,
    ) {
    }

    /**
     * @return list<array{name: string, flag: string|null}>
     */
    public function getAll(): array
    {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'User-Agent' => 'CountriesApp/1.0',
                'Accept' => 'application/json',
            ])
            ->retry($this->retryTimes, 500, throw: false)
            ->get($this->url);

        if ($response->failed()) {
            $response->throw();
        }

        $out = [];
        foreach ($response->json() ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $name = $row['name']['common'] ?? null;
            if (! is_string($name) || $name === '') {
                continue;
            }
            $flags = isset($row['flags']) && is_array($row['flags']) ? $row['flags'] : [];
            $raw = $flags['png'] ?? $flags['svg'] ?? null;
            $out[] = ['name' => $name, 'flag' => $this->validFlagUrl($raw)];
        }
        usort($out, fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));

        return array_values($out);
    }

    private function validFlagUrl(mixed $url): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        $url = trim($url);

        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return $url;
    }
}
