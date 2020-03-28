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
        $hook1 = new Hook('testAction', function() {
            dump('Test closure');
        }, Hook::CALL_BEFORE);
        $hook2 = new Hook('testAction', function() {
            dump('Test closure 2');
        }, Hook::CALL_BEFORE);
        $this->addHook($hook1);
        $this->addHook($hook2);
        $this->fireHooks('testAction', Hook::CALL_BEFORE);
        dd('awdawd');
    }
}
