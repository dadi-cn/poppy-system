<?php

namespace Poppy\System\Tests\Classes;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\System\Classes\Misc\MiscArea;
use Poppy\System\Tests\Base\SystemTestCase;

class MiscTest extends SystemTestCase
{
    public function testCountryKv(): void
    {
        $countryKv = MiscArea::kvCountry();
        $this->assertEquals('中国', $countryKv['CN']);
    }
    public function testAreaKv(): void
    {
        $cityKv = MiscArea::kvCity();
        $this->assertEquals('济南市', $cityKv['3701']);
    }
}
