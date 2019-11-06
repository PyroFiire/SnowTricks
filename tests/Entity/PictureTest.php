<?php

namespace App\Tests\Util;

use App\Entity\Picture;
use PHPUnit\Framework\TestCase;

class PictureTest extends TestCase
{
    public function testGetAndSetPath()
    {
        $picture = new Picture();

        $result = $picture->setPath('test.png');
        $this->assertEquals($picture, $result);

        $result = $picture->getPath();
        $this->assertEquals('test.png', $result);

    }
}