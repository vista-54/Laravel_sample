<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 24.05.2019
 * Time: 15:08
 */

namespace App\Services;


use Storage;

class Base64
{
    public function save($base64, $name, $path)
    {
        $file_data = $base64;
        $file_name = $name . '_' . time() . '.' . $this->parseExtension($base64); //generating unique file name;
        @list($type, $file_data) = explode(';', $file_data);
        @list(, $file_data) = explode(',', $file_data);
        if ($file_data != "") { // storing image in storage/app/public Folder
            $filePath = $path . '/' . $file_name;
            return Storage::disk('public')->put($path . '/' . $file_name, base64_decode($file_data)) ? $filePath : false;
        }
    }

    public function parseExtension($base64)
    {
        $format = stristr($base64, ';', true);
        return explode("/", $format)['1'];
    }
}