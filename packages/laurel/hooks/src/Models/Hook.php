<?php


namespace Laurel\Hooks\Models;


use Illuminate\Support\Collection;
use Laurel\Hooks\Contracts\HookContract;
use Mockery\Exception;
use phpDocumentor\Reflection\Types\Mixed_;
use ReflectionFunction;

class Hook implements HookContract
{
    public const CALL_BEFORE = 'before';
    public const CALL_AFTER = 'after';

    private $actionName;
    private $callback;
    private $callbackClassOrObject;
    private $callTime;
    private $data;

    public static function checkCallTime(string $callTime)
    {
        if ($callTime !== self::CALL_BEFORE && $callTime !== self::CALL_AFTER)
            throw new \Exception('Call time for hook has not been specified');
    }

    private static function checkCallback($callback, $callbackClassOrObject)
    {
        if (is_null($callbackClassOrObject))
            self::checkCallbackIfItIsClosure($callback);
        else if (is_object($callbackClassOrObject))
            self::checkCallbackIfItIsObject($callback, $callbackClassOrObject);
        else if (is_string($callbackClassOrObject))
            self::checkCallbackIfItIsStaticMethod($callback, $callbackClassOrObject);
        else
            self::throwIncorrectCallbackTypeException();
    }

    private static function throwIncorrectCallbackTypeException()
    {
        throw new Exception('Callback function must be closure, class name with name of static method or object with method name');
    }

    private static function throwCallbackObjectDoesNotHasMethod($callback)
    {
        throw new Exception("Callback object does not has method \"{$callback}\"");
    }

    private static function throwCallbackClassDoesNotHasMethod($callback)
    {
        throw new Exception("Callback class does not has static method \"{$callback}\"");
    }

    private static function checkCallbackIfItIsClosure($callback)
    {
        if (!(new ReflectionFunction($callback))->isClosure())
            self::throwIncorrectCallbackTypeException();
    }

    private static function checkCallbackIfItIsObject($callback, $callbackObject)
    {
        if (!method_exists($callbackObject, $callback))
            self::throwCallbackObjectDoesNotHasMethod($callback);
    }

    private static function checkCallbackIfItIsStaticMethod($callback, $callbackClass)
    {
        if (!method_exists($callbackClass, $callback))
            self::throwCallbackClassDoesNotHasMethod($callback);
    }

    public function __construct(string $actionName, $callback, $callbackClassOrObject = null, $callTime = self::CALL_BEFORE, $data = null)
    {
        $this->setActionName($actionName);
        $this->setCallback($callback, $callbackClassOrObject);
        $this->setCallTime($callTime);
        $this->setData($data);
    }

    public function setActionName(string $actionName) : self
    {
        if (!strlen(trim($actionName)))
            throw new \Exception('Action name for hook has not been specified');

        $this->actionName = $actionName;
        return $this;
    }

    public function setCallTime($callTime) : self
    {
        self::checkCallTime($callTime);
        $this->callTime = $callTime;
        return $this;
    }

    public function setCallback($callback, &$callbackClassOrObject) : self
    {
        self::checkCallback($callback, $callbackClassOrObject);
        $this->callback = $callback;
        $this->callbackClassOrObject = $callbackClassOrObject;
        return $this;
    }

    public function setData($data) : self
    {
        $this->data = $data;
        return $this;
    }

    public function getActionName() : string
    {
        return $this->actionName;
    }

    public function getCallTime() : string
    {
        return $this->callTime;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getCallbackClassOrObject()
    {
        return $this->callbackClassOrObject;
    }

    public function getData()
    {
        return $this->data;
    }

    public function callBefore() : self
    {
        $this->callTime = self::CALL_BEFORE;
        return $this;
    }

    public function callAfter() : self
    {
        $this->callTime = self::CALL_AFTER;
        return $this;
    }

    public function isCallableBeforeProcessing() : bool
    {
        return $this->callTime === self::CALL_BEFORE;
    }

    public function isCallableAfterProcessing() : bool
    {
        return $this->callTime === self::CALL_AFTER;
    }

    public function isActionNameEqualTo(string $actionName) : bool
    {
        return $this->actionName === $actionName;
    }

    public function runCallback()
    {
        if (is_null($this->getCallbackClassOrObject()))
            $this->getCallback()($this->getData());
        else if (is_object($this->getCallbackClassOrObject()))
            $this->callbackClassOrObject->{$this->getCallback()}($this->getData());
        else if (is_string($this->getCallbackClassOrObject()))
            $this->callbackClassOrObject::{$this->getCallback()}($this->getData());
    }
}
