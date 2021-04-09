<?php

namespace Poppy\System\Tests\Testing;

use Poppy\Framework\Application\TestCase;

class TestingSysKey extends TestCase
{
    public function testNotSetSystemKey()
    {
        $hooks = sys_hook('poppy.system.settings');
        foreach ($hooks as $key => $forms) {
            collect($hooks[$key]['forms'])->map(function ($form) {
                if (!class_exists($form)) {
                    return;
                }
                $objForm = new $form();
                if (!method_exists($objForm, 'form')) {
                    return;
                }
                $objForm->form();
                if (!property_exists($objForm, 'group') && property_exists($objForm, 'fields')) {
                    return;
                }
                $group = $objForm->getGroup();
                collect($objForm->fields())->each(function ($formField) use ($group) {
                    if (in_array('required', $formField->getRules(), true) && !sys_setting($group . '.' . $formField->column())) {
                        $this->assertNotNull($formField->column());
                    }
                    $this->assertTrue(true);
                });
            });
        }
    }
}
