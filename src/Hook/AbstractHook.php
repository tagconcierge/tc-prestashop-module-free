<?php

namespace PrestaShop\Module\TagConciergeFree\Hook;

use Context;
use PrestaShop\Module\TagConciergeFree\Install\TagConciergeModuleInterface;

abstract class AbstractHook
{
    public const HOOKS = [];

    /** @var TagConciergeModuleInterface */
    protected $module;

    public function __construct(TagConciergeModuleInterface $module)
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
