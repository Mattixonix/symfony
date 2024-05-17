<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Service;

use Imagine\Image\Box;
use Imagine\Gd\Imagine;
use MateuszJagielskiRekrutacjaSmartiveapp\Service\ThumbnailGeneratorInterface;

class ThumbnailGeneratorService implements ThumbnailGeneratorInterface
{
    private const MAX_SIDE_LENGHT = 150;
    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function generate(string $filepath, string $filename): string
    {
        $image_realpath = $filepath . '/' .  $filename;
        $thumbnail_size = $this->calculateThumnailSize($image_realpath);
        $thumbnail_name = $this->getThumbnailName($image_realpath);
        $image = $this->imagine->open($image_realpath);

        $image
            ->resize(new Box($thumbnail_size['width'], $thumbnail_size['height']))
            ->save($thumbnail_name);

        if (file_exists($filepath . '/' .  $filename)) {
            unlink($filepath . '/' .  $filename);
        }

        return $thumbnail_name;
    }

    public function getThumbnailName(string $realpath): string
    {
        $image_info = pathinfo($realpath);
        return $image_info['dirname'] . '/' . $image_info['filename'] . '_thumb' . '.' . $image_info['extension'];
    }

    private function calculateThumnailSize(string $realpath): array
    {
        list($iwidth, $iheight) = getimagesize($realpath);
        $ratio = $iwidth / $iheight;

        if ($iwidth >= $iheight) {
            $thumbnail_width = min($iwidth, self::MAX_SIDE_LENGHT);
            $thumbnail_height = $thumbnail_width / $ratio;
        } else {
            $thumbnail_height = min($iheight, self::MAX_SIDE_LENGHT);
            $thumbnail_width = $thumbnail_height * $ratio;
        }

        return [
            'width' => $thumbnail_width,
            'height' => $thumbnail_height
        ];
    }
}
