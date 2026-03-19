<?php

namespace App\Services;

use App\Contracts\CountryListProvider;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Caches and paginates the country list. Does not know where the list comes from.
 * To replace the external API, swap the CountryListProvider implementation in the container.
 */
class CountryService
{
    private const CACHE_KEY = 'countries.all';

    public function __construct(
        private readonly CountryListProvider $countryListProvider,
    ) {
    }

    /**
     * Paginated countries for the API. Null when the provider fails (e.g. upstream API down).
     *
     * @return array{data: list<array{name: string, flag: string|null}>, meta: array{current_page: int, last_page: int, per_page: int, total: int}}|null
     */
    public function page(int $page, int $perPage): ?array
    {
        try {
            $all = Cache::remember(
                self::CACHE_KEY,
                max(60, (int) config('services.countries_cache_ttl_seconds', 3600)),
                fn (): array => $this->countryListProvider->getAll(),
            );
        } catch (HttpClientException $e) {
            $this->logProviderError($e);

            return null;
        }

        $total = count($all);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = max(1, $page);
        $slice = array_slice($all, ($page - 1) * $perPage, $perPage);

        return [
            'data' => $slice,
            'meta' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
            ],
        ];
    }

    private function logProviderError(HttpClientException $e): void
    {
        $status = $e instanceof RequestException && $e->response
            ? $e->response->status()
            : null;

        Log::warning('Countries provider request failed', [
            'message' => $e->getMessage(),
            'http_status' => $status,
        ]);
    }
}
