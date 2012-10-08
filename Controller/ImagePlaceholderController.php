<?php
/**
 * @copyright 2012 Rithis Studio LLC
 * @author Vyacheslav Slinko <vyacheslav.slinko@rithis.com>
 */

namespace Rithis\ImagePlaceholderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

class ImagePlaceholderController extends Controller
{
    public function getAction($x, $y, $backgroundColor, $textColor)
    {
        $query = $this->getRequest()->query;
        $y = $y ? : $x;

        $text = $query->get('text');
        $text = strlen($text) > 0 ? $text : sprintf('%dx%d', $x, $y);

        $cacheDir = sprintf("%s/rithis-image-placeholder", $this->container->getParameter('kernel.cache_dir'));
        $cacheFile = sprintf("%s/%s", $cacheDir, md5(sprintf('%d:%d:%s:%s:%s', $x, $y, $backgroundColor, $textColor, $text)));

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0750, true);
        }

        if (!is_readable($cacheFile)) {
            $image = imagecreatetruecolor($x, $y);

            $backgroundColor = $this->allocateColor($image, $backgroundColor ? : 'babdb6');
            imagefilledrectangle($image, 0, 0, $x - 1, $y - 1, $backgroundColor);

            if ($x >= 20) {
                $textLength = strlen($text);
                $textColor = $this->allocateColor($image, $textColor ? : '2e3436');

                $fontFile = __DIR__ . '/../Resources/fonts/Orienta-Regular.ttf';
                $maxFontSize = $x < 50 ? $x / 3 : $x / 4;
                $fontSize = $x / $textLength;
                $fontSize = $fontSize > $maxFontSize ? $maxFontSize : $fontSize;

                $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
                $textX = ceil(($x - $bbox[2] - $bbox[0]) / 2);
                $textY = floor(($y - $bbox[7] - $bbox[1]) / 2);

                imagefttext($image, $fontSize, 0, $textX, $textY, $textColor, $fontFile, $text);
            }

            imagegif($image, $cacheFile);
            imagedestroy($image);
        }

        if (class_exists('Symfony\\Component\\HttpFoundation\\StreamedResponse')) {
            $draw = function () use ($cacheFile) {
                readfile($cacheFile);
            };

            return new StreamedResponse($draw, 200, array('Content-Type' => 'image/gif'));
        } else {
            return new Response(file_get_contents($cacheFile), 200, array('Content-Type' => 'image/gif'));
        }
    }

    private function allocateColor($image, $color)
    {
        $color = array_map('hexdec', str_split($color, 2));

        return imagecolorallocate($image, $color[0], $color[1], $color[2]);
    }
}
