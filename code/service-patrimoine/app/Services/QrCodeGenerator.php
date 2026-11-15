<?php

namespace App\Services;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeGenerator
{
    public function svg(string $data, int $size = 220): string
    {
        return (new SvgWriter())->write($this->build($data, $size))->getString();
    }

    public function pngDataUri(string $data, int $size = 400): string
    {
        $png = (new PngWriter())->write($this->build($data, $size))->getString();

        return 'data:image/png;base64,'.base64_encode($png);
    }

    private function build(string $data, int $size): QrCode
    {
        return new QrCode(
            data: $data,
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 4,
        );
    }
}
