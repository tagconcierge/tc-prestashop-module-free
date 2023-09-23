<?php

namespace PrestaShop\Module\TagConciergeFree\Hook;

use Context;
use Module;

abstract class AbstractHook
{
    public const HOOKS = [];

    protected $module;

    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    public function getHooks(): array
    {
        return static::HOOKS;
    }

    protected function getContext(): Context
    {
        return Context::getContext();
    }
}
