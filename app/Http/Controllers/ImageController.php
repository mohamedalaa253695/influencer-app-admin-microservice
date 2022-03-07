<?php
namespace App\Http\Controllers;

use App\Http\Requests\ImageUploadRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImageController
{
    public function upload(ImageUploadRequest $request)
    {
        $file = $request->file('image');
        $name = Str::random(10);
        $image_url = Storage::putFileAs('images', $file, $name . '.' . $file->extension());

        return [

            'url' => env('APP_URL') . '/' . $image_url,
        ];
    }
}
