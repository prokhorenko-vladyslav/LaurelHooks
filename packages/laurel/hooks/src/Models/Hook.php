<?php


namespace Laurel\Hooks\Models;


use Illuminate\Support\Collection;
use Laurel\Hooks\Contracts\HookContract;
use phpDocumentor\Reflection\Types\Mixed_;
use ReflectionFunction;

class Hook implements HookContract
{
    public const CALL_BEFORE = 'before';
    public const CALL_AFTER = 'after';

    private $actionName;
    private $callback;
    private $callTime;
    private $data;

    public static function checkCallTime(string $callTime)
    {
        if ($callTime !== self::CALL_BEFORE && $callTime !== self::CALL_AFTER)
            throw new \Exception('Call time for hook has not been specified');
    }

    public function __construct(string $actionName, $callback, $callTime = self::CALL_BEFORE, Collection $data = null)
    {
        $this->setActionName($actionName);
        $this->setCallback($callback);
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

    public function setCallback($callback) : self
    {
        $this->checkCallback($callback);
        $this->callback = $callback;
        return $this;
    }

    public function setData($data) : self
    {
        if (!$data)
            $this->data = collect([]);
        else if ($data instanceof Collection)
            $this->data = &$data;
        else
            throw new \Exception('Data for hook must be instance of ' . Collection::class);

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

    public function getData(): Collection
    {
        return $this->data;
    }

    private function checkCallback($callback)
    {
        if (!(new ReflectionFunction($callback))->isClosure())
            throw new \Exception('Callback function must be closure');
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
        return $this->getCallback()($this->getData());
    }
}
