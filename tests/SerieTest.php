<?php

namespace App\Tests;

use App\Entity\Serie;
use PHPUnit\Framework\TestCase;

class SerieTest extends TestCase
{
    public function testSetterName(): void
    {
        $serie = new Serie();
        $serie->setName('Michel');
        $this->assertEquals('Michel', $serie->getName(), 'Setter name NOT OK');
        $this->assertNotEquals('michel', $serie->getName(), 'Setter name OK');
    }
}
