<?php
// tests/MediaObjectTest.php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\MediaObject;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaObjectTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testCreateAMediaObject(): void
    {
        $file = new UploadedFile('fixtures/files/image.png', 'image.png');
        $client = self::createClient();

        $client->request('POST', '/media_objects', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                // If you have additional fields in your MediaObject entity, use the parameters.
                'parameters' => [
                    'title' => 'My file uploaded',
                ],
                'files' => [
                    'file' => $file,
                ],
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(MediaObject::class);
        $this->assertJsonContains([
            'title' => 'My file uploaded',
        ]);
    }
}