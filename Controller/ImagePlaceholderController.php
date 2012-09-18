<?php
/**
 * @copyright 2012 Rithis Studio LLC
 * @author Vyacheslav Slinko <vyacheslav.slinko@rithis.com>
 */

namespace Rithis\ImagePlaceholderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImagePlaceholderController extends Controller
{
    public function getAction($x, $y, $backgroundColor, $textColor)
    {
        $query = $this->getRequest()->query;
        $y = $y ? : $x;

        $text = $query->get('text');
        $text = strlen($text) > 0 ? $text : sprintf('%dx%d', $x, $y);
        $textLength = strlen($text);

        $fontFile = __DIR__ . '/../Resources/fonts/Orienta-Regular.ttf';
        $fontSize = $textLength > 4 ? $x / $textLength : $x / 4;

        $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textX = ceil(($x - $bbox[2] - $bbox[0]) / 2);
        $textY = ceil(($y - $bbox[7] - $bbox[1]) / 2);

        $image = imagecreatetruecolor($x, $y);

        $backgroundColor = $this->allocateColor($image, $backgroundColor ? : 'babdb6');
        $textColor = $this->allocateColor($image, $textColor ? : '2e3436');

        imagefilledrectangle($image, 0, 0, $x - 1, $y - 1, $backgroundColor);
        imagefttext($image, $fontSize, 0, $textX, $textY, $textColor, $fontFile, $text);

        $draw = function () use ($image) {
            imagegif($image);
            imagedestroy($image);
        };

        return new StreamedResponse($draw, 200, array('Content-Type' => 'image/gif'));
    }

    private function allocateColor($image, $color)
    {
        $color = array_map('hexdec', str_split($color, 2));

        return imagecolorallocate($image, $color[0], $color[1], $color[2]);
    }
}
