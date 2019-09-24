<?php

namespace Caronae\Tests;

use Caronae\CaronaeService;
use PHPUnit\Framework\TestCase;

class CaronaeServiceTest extends TestCase
{
    public function testCreatesServiceWithoutErrors()
    {
        $service = new CaronaeService();
        $this->assertNotNull($service);
    }
}
