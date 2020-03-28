<?php


namespace Laurel\Hooks\Traits;


use Laurel\Hooks\Models\Hook;
use Illuminate\Support\Collection;
use Laurel\Hooks\Contracts\HookContract;


/**
 * Trait Hookable
 * @package App\Traits
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

    public function fireAllHooks()
    {
        $this->fireBeforeHooks();
        $this->fireAfterHooks();
    }

    public function fireAllHooksOnce()
    {
        $this->fireAllHooks();
        $this->removeAllHooks();
    }

    public function fireBeforeHooks()
    {
        $this->getBeforeProcessingHooks()->each(function (Hook $hook) {
            $hook->runCallback();
        });
    }

    public function fireBeforeHooksOnce()
    {
        $this->fireBeforeHooks();
        $this->removeAllBeforeProcessingHooks();
    }

    public function fireAfterHooks()
    {
        $this->getAfterProcessingHooks()->each(function (Hook $hook) {
            $hook->runCallback();
        });
    }

    public function fireAfterHooksOnce()
    {
        $this->fireAfterHooks();
        $this->removeAllAfterProcessingHooks();
    }

    public function fireHooks(string $actionName, string $callTime)
    {
        Hook::checkCallTime($callTime);
        if ($callTime === Hook::CALL_BEFORE)
            $this->fireBeforeHooksForAction($actionName);
        else if ($callTime === Hook::CALL_AFTER)
            $this->fireAfterHooksForAction($actionName);
    }

    public function fireHooksOnce(string $actionName, string $callTime)
    {
        $this->fireHooks($actionName, $callTime);
        $this->removeHooks($actionName, $callTime);
    }

    public function fireBeforeHooksForAction(string $actionName)
    {
        $hooks = $this->getBeforeProcessingHooksByActionName($actionName);
        $hooks->each(function (Hook $hook) {
            $hook->runCallback();
        });
    }

    public function fireBeforeHooksForActionOnce(string $actionName)
    {
        $this->fireBeforeHooksForAction($actionName);
        $this->removeBeforeHooksForAction($actionName);
    }

    public function fireAfterHooksForAction(string $actionName)
    {
        $hooks = $this->getAfterProcessingHooksByActionName($actionName);
        $hooks->each(function (Hook $hook) {
            $hook->runCallback();
        });
    }

    public function fireAfterHooksForActionOnce(string $actionName)
    {
        $this->fireAfterHooksForAction($actionName);
        $this->removeAfterHooksForAction($actionName);
    }

    public function removeAllHooks()
    {
        $this->hooks = collect([]);
    }

    public function removeHooks(string $actionName, string $callTime)
    {
        $this->hooks = $this->getHooks()->reject(function (Hook $hook) use ($actionName, $callTime) {
            return $hook->getActionName() === $actionName && $hook->getCallTime() === $callTime;
        });
    }

    public function removeAllHooksForAction(string $actionName)
    {
        $this->hooks = $this->getHooks()->reject(function (Hook $hook) use ($actionName) {
           return $hook->getActionName() === $actionName;
        });
    }

    public function removeAllBeforeProcessingHooks()
    {
        $this->hooks = $this->getHooks()->reject(function (Hook $hook) {
            return $hook->isCallableBeforeProcessing();
        });
    }

    public function removeAllAfterProcessingHooks()
    {
        $this->hooks = $this->getHooks()->reject(function (Hook $hook) {
            return $hook->isCallableAfterProcessing();
        });
    }

    public function removeBeforeHooksForAction($actionName)
    {
        $this->hooks = $this->getHooks($actionName)->reject(function (Hook $hook) use ($actionName) {
            return $hook->isCallableBeforeProcessing() && $hook->getActionName() === $actionName;
        });
    }

    public function removeAfterHooksForAction($actionName)
    {
        $this->hooks = $this->getHooks($actionName)->reject(function (Hook $hook) use ($actionName) {
            return $hook->isCallableAfterProcessing() && $hook->getActionName() === $actionName;
        });
    }
}
