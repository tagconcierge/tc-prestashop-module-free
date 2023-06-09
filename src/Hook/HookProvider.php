<?php

namespace PrestaShop\Module\TagConciergeFree\Hook;

class HookProvider
{
    /**
     * @var AbstractHook[]
     */
    private $hooks;

    /**
     * @var \TagConciergeFree
     */
    private $module;

    public function __construct(\TagConciergeFree $module)
    {
        $this->module = $module;
    }

    public function provide(string $hookClass): AbstractHook
    {
        if (false === isset($this->hooks[$hookClass])) {
            $this->hooks[$hookClass] = new $hookClass($this->module);
        }

        return $this->hooks[$hookClass];
    }
}
