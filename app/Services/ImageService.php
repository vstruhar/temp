<?php

namespace App\Services;

use Intervention\Image\Facades\Image;

class ImageService
{
    /**
     * Upload image
     *
     * @param  \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|array|null  $requestFile
     * @param  string  $imageName
     * @param  string  $saveLocation
     *
     * @return string|null
     */
    public static function upload($requestFile, $imageName, $saveLocation)
    {
        if (!$requestFile) {
            return null;
        }

        Image::make($requestFile->getRealPath())
             ->encode('jpg', 100)
             ->save(storage_path("app/public/{$saveLocation}/{$imageName}.jpg"));

        return "{$saveLocation}/{$imageName}.jpg";
    }

    /**
     * @param  string  $imagePath
     * @param  int  $width
     * @param  int  $height
     */
    public static function resize($imagePath, $width, $height)
    {
        $path = public_path('storage/'.$imagePath);

        $img = Image::make($path)
                    ->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

        $img->save($path);
    }
}
