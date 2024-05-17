<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Tests\Service;

use PHPUnit\Framework\TestCase;
use MateuszJagielskiRekrutacjaSmartiveapp\Service\ThumbnailGeneratorService;

class ThumbnailGeneratorServiceIntegrationTest extends TestCase
{
    public function testGenerateThumbnail(): void
    {
        $images_dir = __DIR__ . '/../fixtures/images';
        $thumbnail_generator = new ThumbnailGeneratorService();
        $files = glob("$images_dir/*.*");

        foreach ($files as $file) {
            $filename = basename($file);
            $file_copy = $images_dir . '/' . pathinfo($filename, PATHINFO_FILENAME) . '_copy.' . pathinfo($filename, PATHINFO_EXTENSION);
            copy($file, $file_copy);
            $thumbnail_path = $thumbnail_generator->generate($images_dir, basename($file_copy));


            $this->assertFileExists($thumbnail_path);
            $expected_thumbnail_name = $images_dir . '/' . pathinfo($filename, PATHINFO_FILENAME) . '_copy_thumb.' . pathinfo($filename, PATHINFO_EXTENSION);
            $this->assertEquals($expected_thumbnail_name, $thumbnail_path);

            list($width, $height) = getimagesize($thumbnail_path);
            $this->assertLessThanOrEqual(300, $width, 'The width of the thumbnail is greater than 300');
            $this->assertLessThanOrEqual(300, $height, 'The height of the thumbnail is greater than 300');

            unlink($thumbnail_path);
        }
    }
}
