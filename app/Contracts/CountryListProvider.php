<?php

namespace App\Contracts;

/**
 * Contract for any source of country list (REST API, another API, mock, etc.).
 * Implementations fetch from their source and return a normalized list.
 *
 * @return list<array{name: string, flag: string|null}>
 */
interface CountryListProvider
{
    public function getAll(): array;
}
