<?php

namespace Poppy\System\Tests\Setting;

/**
 * Copyright (C) Update For IDE
 */

use Poppy\Framework\Application\TestCase;
use Poppy\System\Tests\Testing\TestingPam;

class TestingPamTest extends TestCase
{

    public function testExclude()
    {
        $exclude = TestingPam::exclude();
        $this->assertNotNull($exclude);
    }


    public function testGetAllKeys()
    {
        $hooks = sys_hook('poppy.system.settings');

        foreach ($hooks as $key => $value) {
            collect($hooks[$key]['forms'])->map(function ($form) {
                if (class_exists($form)) {
                    $objForm = new $form();
                    if (method_exists($objForm, 'form')) {
                        $objForm->form();
                        if (property_exists($objForm, 'group') && property_exists($objForm, 'fields')) {
                            foreach ($objForm->fields() as $k => $formField) {
                                if (in_array('required', $formField->getRules(), true) && !sys_setting($objForm->getGroup() . '.' . $formField->column())) {
                                    dump($formField->column());
                                }
                            }
                        }
                    }
                }
            });
        }
    }


}
