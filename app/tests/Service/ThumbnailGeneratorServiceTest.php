<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Tests\Service;

use MateuszJagielskiRekrutacjaSmartiveapp\Service\ThumbnailGeneratorService;
use PHPUnit\Framework\TestCase;

class ThumbnailGeneratorServiceTest extends TestCase
{
    private $service;
    private $tempDir;

    protected function setUp(): void
    {
        $this->service = new ThumbnailGeneratorService();
        $this->tempDir = sys_get_temp_dir() . '/thumbnail_test';
        mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob("$this->tempDir/*.*"));
        rmdir($this->tempDir);
    }

    public function testGenerate(): void
    {
        $images_dir = __DIR__ . '/../fixtures/images';
        $files = glob("$images_dir/*.*");

        foreach ($files as $file) {
            $filename = basename($file);
            $filepath = $this->tempDir . '/' . $filename;

            copy($file, $filepath);
            $thumbnail_name = $this->service->generate($this->tempDir, $filename);

            $this->assertFileExists($thumbnail_name);

            list($width, $height) = getimagesize($thumbnail_name);
            $this->assertLessThanOrEqual(150, $width);
            $this->assertLessThanOrEqual(150, $height);

            $this->assertFileDoesNotExist($filepath);
        }
    }

    public function testGetThumbnailName(): void
    {
        $images_dir = realpath(__DIR__ . '/../fixtures/images');
        $images = glob("$images_dir/*.*");

        foreach ($images as $image) {
            $expectedThumbnailName = $images_dir . '/' . pathinfo($image, PATHINFO_FILENAME) . '_thumb.' . pathinfo($image, PATHINFO_EXTENSION);
            $realpath = realpath($image);
            $thumbnailName = $this->service->getThumbnailName($realpath);

            $this->assertEquals($expectedThumbnailName, $thumbnailName);
        }
    }
}
