<?php
namespace App\Services;


class Base64Validator
{
    private $formats = ['jpg', 'jpeg', 'png'];

    /**
     * @param $base64
     * @return bool
     */
    public function validate($base64)
    {
        if (empty($base64) || is_null($base64) || stristr($base64, 'http') || !stristr($base64, 'data')) {
            return false;
        }
        if ( stristr($base64, 'data')) {

            $format = stristr($base64, ';', true);
            $extension = explode("/", $format)['1'];
            $this->checkFormat($extension);

            @list(, $base64) = explode(',', $base64);
        }
        $img = imagecreatefromstring(base64_decode($base64));
        imagepng($img, 'myimage.png');

        return getimagesize('myimage.png') ? true : false;
    }

    /**
     * @param $mime
     * @return bool
     */
    private function checkFormat($mime)
    {
       return in_array($mime, $this->formats) ?? abort(422, 'Image supported formats: jpg, jpeg and png');
    }
}