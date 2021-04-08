<?php

namespace Webkul\Product\Repositories;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Webkul\Core\Eloquent\Repository;

class ProductImageRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Product\Contracts\ProductImage';
    }

    /**
     * @param  array  $data
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return void
     */
    public function uploadImages($data, $product)
    {
        $previousImageIds = $product->images()->pluck('id');

// var_dump($data['imageURL']);

        if (isset($data['images'])) {
            foreach ($data['images'] as $imageId => $image) {
                $file = 'images.' . $imageId;
                $dir = 'product/' . $product->id;
                
                if ($image instanceof UploadedFile) {
                    if (request()->hasFile($file)) {

                        $this->create([
                            'path'       => request()->file($file)->store($dir),
                            'product_id' => $product->id,
                        ]);
                    }
                } else {
                    if (is_numeric($index = $previousImageIds->search($imageId))) {
                        $previousImageIds->forget($index);
                    }
                }
            }
        }

        if (isset($data['imageURL'])) {
            foreach ($data['imageURL'] as $imageId => $image) {

                $file = 'images.' . $imageId;
                $dir = 'product/' . $product->id;
                $url = $image;
            $contents = file_get_contents($url);
            $name = substr($url, strrpos($url, '/') + 1);
            Storage::put($dir . '/' . $name, $contents);
                    if ($contents) {
                        $this->create([
                            'path'       => $dir . '/' . $name,
                            'product_id' => $product->id,
                        ]);

                    }
            }
  
        }


        foreach ($previousImageIds as $imageId) {
            if ($imageModel = $this->find($imageId)) {
                Storage::delete($imageModel->path);

                $this->delete($imageId);
            }
        }
    }
}
