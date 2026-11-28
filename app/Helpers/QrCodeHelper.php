<?php

namespace App\Helpers;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeHelper
{
    public static function generateSvg(string $text, int $size = 130): string
    {
        $renderer = new ImageRenderer(new RendererStyle($size, 4), new SvgImageBackEnd());

        return (new Writer($renderer))->writeString($text, 'UTF-8');
    }
}
