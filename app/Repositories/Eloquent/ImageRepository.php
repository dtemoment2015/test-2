<?php

namespace App\Repositories\Eloquent;

use App\Models\Image;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ImageRepository extends BaseRepository implements ImageRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Image());
    }

    public function findByHash(string $hash): ?Image
    {
        return $this->model->where('file_hash', $hash)->first();
    }

    public function getUserImages(int $userId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getUserImage(int $userId, int $imageId): ?Image
    {
        return $this->model->where('user_id', $userId)
            ->where('id', $imageId)
            ->first();
    }

    public function countOtherImagesWithPath(string $filePath, int $excludeId): int
    {
        return $this->model->where('file_path', $filePath)
            ->where('id', '!=', $excludeId)
            ->count();
    }
}
