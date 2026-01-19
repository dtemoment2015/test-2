<?php

namespace App\Repositories\Contracts;

use App\Models\Image;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ImageRepositoryInterface extends RepositoryInterface
{
    public function findByHash(string $hash): ?Image;
    public function getUserImages(int $userId, int $perPage = 15, int $page = 1): LengthAwarePaginator;
    public function getUserImage(int $userId, int $imageId): ?Image;
    public function countOtherImagesWithPath(string $filePath, int $excludeId): int;
}
