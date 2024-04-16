<?php

namespace PrestaShop\Module\TagConciergeFree\Hook;

use PrestaShop\Module\TagConciergeFree\Install\TagConciergeModuleInterface;

class HookProvider
{
    /**
     * @var AbstractHook[]
     */
    private $hooks;

    /**
     * @var TagConciergeModuleInterface
     */
    private $module;

    public function __construct(TagConciergeModuleInterface $module)
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
