<?php


namespace PrestaShop\Module\TagConciergeFree\Hook;

abstract class AbstractHook
{
    /** @var array */
    public const HOOKS = [];

    /**
     * @var \TagConciergeFree
     */
    protected $module;

    public function __construct(\TagConciergeFree $module)
    {
        $this->module = $module;
    }

    public function getHooks(): array
    {
        return static::HOOKS;
    }

    protected function getContext(): \Context
    {
        return \Context::getContext();
    }
}
