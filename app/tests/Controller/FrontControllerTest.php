<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FrontControllerTest extends WebTestCase
{
    public function testIndexPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome on Thumbnail generator');
    }

    public function testFormSubmissionWithoutFile(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $token = $crawler->filter('#thumbnail_type_form__token')->attr('value');

        $client->request('POST', '/', [
            'thumbnail_type_form' => [
                '_token' => $token
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="thumbnail_type_form"]');
    }

    public function testFormSubmissionWithValidData(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $token = $crawler->filter('#thumbnail_type_form__token')->attr('value');

        $temp_file_path = tempnam(sys_get_temp_dir(), 'test_image_');
        file_put_contents($temp_file_path, file_get_contents(__DIR__ . '/../fixtures/images/image_small.jpeg'));
        $temp_image_file = new UploadedFile(
            $temp_file_path,
            'image.jpg',
            'image/jpeg',
            null
        );

        $client->request('POST', '/', ['thumbnail_type_form' => [
            'name' => 'Test Image',
            'destination' => 1,
            'dropboxToken' => 'your-dropbox-token',
            '_token' => $token
        ]], ['thumbnail_type_form' => ['image' => $temp_image_file]]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
    }
}
