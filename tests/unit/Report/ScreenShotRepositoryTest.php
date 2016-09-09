<?php

namespace Athena\Tests\Report;

use Athena\Logger\ImageRepository;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

class ScreenShotRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function testSaveImage_RandomImageString_ShouldWriteImageFile()
    {
        $fakeFileSystem = vfsStream::setup('root');

        $fileRepository = new ImageRepository($fakeFileSystem->url());

        $fileName = $fileRepository->write(openssl_random_pseudo_bytes(5));

        $this->assertTrue($fakeFileSystem->hasChild(basename($fileName)));
    }
}