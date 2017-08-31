<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Caronae\CaronaeService;

class CaronaeServiceTest extends PHPUnit_Framework_TestCase
{

    public function testCreatesServiceWithoutErrors()
    {
        $service = new CaronaeService();
        $this->assertNotNull($service);
    }
}
