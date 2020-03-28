<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laurel\Hooks\Models\Hook;
use Laurel\Hooks\Traits\Hookable;

class TestController extends Controller
{
    use Hookable;

    public function index()
    {
        $test = [
            'test' => 123
        ];
        $hook1 = new Hook('testAction', function($data) {
            $data['test'] = 222;
            dump($data);
        }, null,Hook::CALL_BEFORE, $test);
        $hook2 = new Hook('testAction', function() {
            dump('Test closure 2');
        }, null,Hook::CALL_BEFORE);
        $hook3 = new Hook('testAction', 'testHookAction', $this,Hook::CALL_BEFORE);
        $this->addHook($hook1);
        $this->addHook($hook2);
        $this->addHook($hook3);
        $this->fireHooks('testAction', Hook::CALL_BEFORE);
//        dd($test);
    }

    public function testHookAction()
    {
//        dd('from simple method');
    }
}
