<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\IndexImageRequest;
use App\Http\Requests\Api\v1\StoreImageRequest;
use App\Http\Resources\Api\v1\ImageResource;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Изображения
 */
class ImageController extends Controller
{
    public function __construct(
        private ImageService $imageService
    ) {
    }

    /**
     * store
     * 
     * Загрузка изображения через multipart/form-data
     * 
     * @authenticated
     * @apiResource App\Http\Resources\Api\v1\ImageResource
     * @apiResourceModel App\Models\Image
     * @bodyParam image file required Изображение (PNG, JPEG, JPG, максимум 5MB)
     */
    public function store(StoreImageRequest $request)
    {
        return new ImageResource(
            $this->imageService->upload($request->user(), $request->toDTO())
        );
    }

    /**
     * index
     * @authenticated
     * @apiResourceCollection App\Http\Resources\Api\v1\ImageResource
     * @apiResourceModel App\Models\Image
     */
    public function index(IndexImageRequest $request)
    {
        $filters = $request->toDTO();

        return ImageResource::collection(
            $this->imageService->getUserImages($request->user(), $filters->perPage, $filters->page)
        );
    }

    /**
     * show
     * @authenticated
     * @apiResource App\Http\Resources\Api\v1\ImageResource
     * @apiResourceModel App\Models\Image
     */
    public function show(Request $request, int $id)
    {
        return new ImageResource(
            $this->imageService->getUserImage($request->user(), $id)
        );
    }

    /**
     * destroy
     * @authenticated
     */
    public function destroy(Request $request, int $id)
    {
        $this->imageService->delete($request->user(), $id);

        return response()->json([
            'message' => 'Изображение успешно удалено',
        ]);
    }
}
