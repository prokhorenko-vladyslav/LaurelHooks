<?php


namespace Laurel\Hooks\Traits;


use App\Abstracts\Hook;
use App\Contracts\HookContract;
use Illuminate\Support\Collection;


/**
 * Trait Hookable
 * @package App\Traits
 * TODO Need to add method for saving hooks in the database
 */
trait Hookable
{
    private $hooks;

    public function addHook(HookContract $hook)
    {
        if (!$this->hooks) {
            $this->hooks = collect([]);
        }
        $this->hooks->push($hook);
    }

    public function addHooks(array $hooks)
    {
        foreach ($hooks as $hook)
            $this->addHooks($hook);
    }

    public function getHooks() : Collection
    {
        return $this->hooks ?? collect([]);
    }

    public function getBeforeProcessingHooks()
    {
        return $this->getHooks()->filter(function (Hook $hook) {
            return $hook->isCallableBeforeProcessing();
        });
    }

    public function getAfterProcessingHooks()
    {
        return $this->getHooks()->filter(function (Hook $hook) {
            return $hook->isCallableAfterProcessing();
        });
    }

    public function getBeforeProcessingHooksByActionName(string $actionName)
    {
        return $this->getHooks()->filter(function (Hook $hook) use ($actionName) {
            return $hook->isCallableBeforeProcessing() && $hook->isActionNameEqualTo($actionName);
        });
    }

    public function getAfterProcessingHooksByActionName(string $actionName)
    {
        return $this->getHooks()->filter(function (Hook $hook) use ($actionName) {
            return $hook->isCallableAfterProcessing() && $hook->isActionNameEqualTo($actionName);
        });
    }

    public function callHooksForActionByCallTime(string $actionName, string $callTime)
    {
        /*Hook::checkCallTime($callTime);
        $hooks = $this->get*/
    }
}
