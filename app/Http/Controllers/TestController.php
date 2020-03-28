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
        $test2 = 'Test for using in closure';
        $hook1 = new Hook('testAction', function($data) use ($test2) {
            $data['test'] = 222;
        }, null,Hook::CALL_BEFORE, $test);
        $hook2 = new Hook('testAction', function() {
        }, null,Hook::CALL_BEFORE);
        $hook3 = new Hook('testAction2', 'testHookAction', $this,Hook::CALL_BEFORE);
        $this->addHook($hook1);
        $this->addHook($hook2);
        $this->addHook($hook3);
        $this->fireHooksOnce('testAction', Hook::CALL_BEFORE);
//        $this->fireHooksOnce('testAction2', Hook::CALL_BEFORE);
//        dump($this->getHooks());
//        $this->removeBeforeHooksForAction('testAction');
        dd($this->getHooks());
    }

    public function testHookAction()
    {
//        dd('from simple method');
    }
}
