<?php


namespace Laurel\Hooks\Contracts;


use Illuminate\Support\Collection;
use phpDocumentor\Reflection\Types\Mixed_;

interface HookContract
{
    public function setActionTime(string $actionName) : self;
    public function setCallTime($callTime) : self;
    public function setCallback($callback) : self;
    public function setData($callback) : self;
    public function getActionName() : string;
    public function getCallTime() : string;
    public function getCallback() : Mixed_;
    public function getData() : Collection;
    public function runCallback();
    public function callBefore() : self;
    public function callAfter() : self;
    public function isCallableBeforeProcessing() : bool;
    public function isCallableAfterProcessing() : bool;
    public function isActionNameEqualTo(string $actionName) : bool;
}
