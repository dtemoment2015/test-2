<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

readonly class StoreImageData
{
    public function __construct(
        public UploadedFile $image,
    ) {
    }
}
