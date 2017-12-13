<?php
/**
 * Add watermark.
 * Make small images.
 */

namespace App\Http\Controllers\Admin\Support;


use Illuminate\Support\Facades\Storage;

class ImageCreator
{
    public function watermark()
    {
        $text = config('app.url');
        $font = public_path('fonts/OpenSans-Regular.ttf');
        foreach (Storage::disk('local')->allFiles('images/products/raw') as $file) {

            $imagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $file;

            // create image
            $image = imagecreatefromjpeg($imagePath);

            // rotate image if needing
            $imageSize = getimagesize($imagePath);
            if ($imageSize[0] > $imageSize[1]) {
                $image = imagerotate($image, 90, 0);
            }

            // watermark image
            $textLeft = imagesx($image) * 0.1;
            $textTop = imagesy($image) * 0.8;
            $textSize = imagesx($image) * 0.06;
            $color = hexdec(config('shop.product_image_watermark_color'));

            imagettftext($image, $textSize, 0, $textLeft, $textTop, $color, $font, $text);

            // source image parameters
            $sourceWidth = imagesx($image);
            $sourceHeight = imagesy($image);

            // create large image
            $imageStream = fopen('php://memory', 'w+');
            $largeWidth = config('shop.large_product_image_height') / config('shop.product_image_size_rate');
            $largeHeight = config('shop.large_product_image_height');
            $large = imagecreatetruecolor($largeWidth, $largeHeight);
            imagecopyresized($large, $image, 0, 0, 0, 0, $largeWidth, $largeHeight, $sourceWidth, $sourceHeight);
            imagejpeg($large, $imageStream, 100);
            Storage::disk('public')->put(str_replace('raw/', 'big/', $file), $imageStream);

            // create thumbnail image
            $imageStream = fopen('php://memory', 'w+');
            $thumbWidth = config('shop.small_product_image_height') / config('shop.product_image_size_rate');
            $thumbHeight = config('shop.small_product_image_height');
            $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
            imagecopyresized($thumbnail, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $sourceWidth, $sourceHeight);
            imagejpeg($thumbnail, $imageStream, 100);
            Storage::disk('public')->put(str_replace('raw/', 'small/', $file), $imageStream);
        }
    }
}