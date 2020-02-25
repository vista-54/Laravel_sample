<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 20.06.2019
 * Time: 13:02
 */

namespace App\Services;

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

class Barcode
{
    public function generate($text, $redeemed = false)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($redeemed ? 'REDEEMED' : $text);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setFormat('PNG');
        $barcode->setScale(5);
        $barcode->setThickness(80);
        $barcode->setFontSize(30);
        return 'data:image/png;base64,' . $barcode->generate();
    }
}
