<?php

namespace App\Providers;

use App\Contracts\CountryListProvider;
use App\Clients\RestCountriesClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CountryListProvider::class, function () {
            $config = config('services.countries_api', []);

            return new RestCountriesClient(
                url: $config['url'] ?? 'https://restcountries.com/v3.1/all?fields=name,flags',
                timeout: $config['timeout'] ?? 15,
                retryTimes: $config['retry_times'] ?? 2,
            );
        });
    }

    public function boot(): void
    {
    }
}
