<?php


namespace Laurel\Hooks\Contracts;

interface HookContract
{
    public static function checkCallTime(string $callTime);

    public function setActionName(string $actionName) : self;
    public function setCallTime($callTime) : self;
    public function setCallback($callback, &$callbackClassOrObject) : self;
    public function setData($callback) : self;
    public function getActionName() : string;
    public function getCallTime() : string;
    public function getCallback();
    public function getCallbackClassOrObject();
    public function getData();
    public function runCallback();
    public function callBefore() : self;
    public function callAfter() : self;
    public function isCallableBeforeProcessing() : bool;
    public function isCallableAfterProcessing() : bool;
    public function isActionNameEqualTo(string $actionName) : bool;
}
