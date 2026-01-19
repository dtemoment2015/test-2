<?php

namespace App\DTOs;

readonly class ImageListFilters
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
    ) {
    }
}
