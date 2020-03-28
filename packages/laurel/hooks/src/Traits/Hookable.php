<?php


namespace Laurel\Hooks\Traits;


use Laurel\Hooks\Models\Hook;
use Illuminate\Support\Collection;
use Laurel\Hooks\Contracts\HookContract;


/**
 * Trait Hookable
 * @package App\Traits
 * TODO Need to add method for saving hooks in the database
 */
trait Hookable
{
    private $hooks;

    public function addHook(HookContract $hook) : self
    {
        if (!$this->hooks) {
            $this->hooks = collect([]);
        }
        $this->hooks->push($hook);
        return $this;
    }

    public function addHooks(array $hooks) : self
    {
        foreach ($hooks as $hook)
            $this->addHooks($hook);

        return $this;
    }

    public function getHooks() : Collection
    {
        return $this->hooks ?? collect([]);
    }

    public function getBeforeProcessingHooks() : Collection
    {
        return $this->getHooks()->filter(function (Hook $hook) {
            return $hook->isCallableBeforeProcessing();
        });
    }

    public function getAfterProcessingHooks() : Collection
    {
        return $this->getHooks()->filter(function (Hook $hook) {
            return $hook->isCallableAfterProcessing();
        });
    }

    public function getHooksByActionName(string $actionName) : Collection
    {
        return $this->getHooks()->filter(function (Hook $hook) use ($actionName) {
           return $hook->getActionName() === $actionName;
        });
    }

    public function getBeforeProcessingHooksByActionName(string $actionName) : Collection
    {
        return $this->getHooks()->filter(function (Hook $hook) use ($actionName) {
            return $hook->isCallableBeforeProcessing() && $hook->isActionNameEqualTo($actionName);
        });
    }

    public function getAfterProcessingHooksByActionName(string $actionName) : Collection
    {
        return $this->getHooks()->filter(function (Hook $hook) use ($actionName) {
            return $hook->isCallableAfterProcessing() && $hook->isActionNameEqualTo($actionName);
        });
    }

    public function fireHooks(string $actionName, string $callTime)
    {
        Hook::checkCallTime($callTime);
        if ($callTime === Hook::CALL_BEFORE)
            $this->fireBeforeHooks($actionName);
        else
            $this->fireAfterHooks($actionName);
    }

    public function fireBeforeHooks(string $actionName)
    {
        $hooks = $this->getBeforeProcessingHooksByActionName($actionName);
        $hooks->each(function (Hook $hook) {
            $hook->runCallback();
        });
    }

    public function fireAfterHooks(string $actionName)
    {
        $hooks = $this->getAfterProcessingHooksByActionName($actionName);
        $hooks->each(function (Hook $hook) {
            $hook->runCallback();
        });
    }
}
