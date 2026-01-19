<?php

namespace App\Services;

use App\DTOs\StoreImageData;
use App\Models\Image;
use App\Models\User;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageService
{
    public function __construct(
        private ImageRepositoryInterface $repository
    ) {
    }

    public function upload(User $user, StoreImageData $data): Image
    {
        $file = $data->image;
        
        // Вычисляем хеш файла для дедупликации
        $fileHash = hash_file('sha256', $file->getRealPath());

        // Проверяем, существует ли уже такое изображение
        $existingImage = $this->repository->findByHash($fileHash);
        if ($existingImage && Storage::disk('public')->exists($existingImage->file_path)) {
            // Если изображение уже существует и файл есть, создаем новую запись, но используем тот же файл
            $filePath = $existingImage->file_path;
            $storedMimeType = $existingImage->mime_type;
            $width = $existingImage->width;
            $height = $existingImage->height;
            $fileSize = $existingImage->file_size;
        } else {
            // Обрабатываем и сохраняем изображение
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            
            // Сжимаем изображение без значительной потери качества
            $image->scale(width: 1920, height: 1920);
            
            // Конвертируем в JPEG для лучшего сжатия (если не PNG с прозрачностью)
            $extension = strtolower($file->getClientOriginalExtension());
            $shouldConvertToJpeg = $extension !== 'png' || !$this->hasTransparency($file->getRealPath());
            
            if ($shouldConvertToJpeg) {
                $image->toJpeg(85);
                $storedExtension = 'jpg';
                $storedMimeType = 'image/jpeg';
            } else {
                $image->toPng();
                $storedExtension = 'png';
                $storedMimeType = 'image/png';
            }

            // Сохраняем файл
            $fileName = $fileHash . '.' . $storedExtension;
            $filePath = 'images/' . $user->id . '/' . $fileName;
            
            Storage::disk('public')->put($filePath, (string) $image->encode());
            
            // Устанавливаем правильные права доступа для веб-сервера
            $fullPath = Storage::disk('public')->path($filePath);
            @chmod($fullPath, 0644);

            // Получаем размеры изображения
            $imageData = $manager->read(Storage::disk('public')->path($filePath));
            $width = $imageData->width();
            $height = $imageData->height();
            $fileSize = Storage::disk('public')->size($filePath);
        }

        // Создем запись в БД
        $image = $this->repository->create([
            'user_id' => $user->id,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_hash' => $fileHash,
            'file_size' => $fileSize,
            'mime_type' => $storedMimeType,
            'width' => $width,
            'height' => $height,
        ]);

        return $image;
    }

    public function getUserImages(User $user, int $perPage = 15, int $page = 1)
    {
        return $this->repository->getUserImages($user->id, $perPage, $page);
    }

    public function getUserImage(User $user, int $imageId): Image
    {
        $image = $this->repository->getUserImage($user->id, $imageId);
        
        if (!$image) {
            throw new NotFoundHttpException('Изображение не найдено');
        }

        return $image;
    }

    public function delete(User $user, int $imageId): bool
    {
        $image = $this->repository->getUserImage($user->id, $imageId);
        
        if (!$image) {
            throw new NotFoundHttpException('Изображение не найдено');
        }

        // Проверяем, используется ли файл другими записями
        $otherImagesCount = $this->repository->countOtherImagesWithPath($image->file_path, $image->id);

        // Удаляем файл только если он не используется другими записями
        if ($otherImagesCount === 0 && Storage::disk('public')->exists($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }

        return $this->repository->delete($image->id);
    }

    private function hasTransparency(string $filePath): bool
    {
        if (!function_exists('imagecreatefrompng')) {
            return false;
        }

        $image = @imagecreatefrompng($filePath);
        if (!$image) {
            return false;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Оптимизация: проверяем только каждый 10-й пиксель для больших изображений
        $step = max(1, min(10, (int) ($width * $height / 10000)));

        for ($x = 0; $x < $width; $x += $step) {
            for ($y = 0; $y < $height; $y += $step) {
                $color = imagecolorat($image, $x, $y);
                $alpha = ($color >> 24) & 0x7F;
                if ($alpha > 0) {
                    return true;
                }
            }
        }

        return false;
    }
}
