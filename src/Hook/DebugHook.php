<?php

namespace PrestaShop\Module\TagConciergeFree\Hook;

class DebugHook extends AbstractHook
{
    public const HOOKS = [
        Hooks::DISPLAY_BEFORE_BODY_CLOSING_TAG => [
            'addDebugConsole',
        ],
    ];

    public function addDebugConsole(): string
    {
        if (true === $this->module->isDebug()) {
            $this->getContext()->smarty->assign('module_dir', $this->module->getPath());
            $this->getContext()->smarty->assign('module_version', $this->module->getVersion());

            return $this->module->render('hooks/debug_hook/display_before_body_closing_tag.tpl');
        }

        return '';
    }
}
