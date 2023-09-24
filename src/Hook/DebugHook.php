<?php

namespace PrestaShop\Module\TagConciergeFree\Hook;

class DebugHook extends AbstractHook
{
    public const HOOKS = [
        Hooks::DISPLAY_AFTER_BODY_OPENING_TAG => [
            'addDebugConsole',
        ],
        Hooks::DISPLAY_BEFORE_BODY_CLOSING_TAG => [
            'addDebugConsolePlaceholder',
        ],
    ];

    public function addDebugConsole(): string
    {
        if (true === $this->module->isDebug()) {
            return $this->module->render('hooks/debug_hook/display_after_body_opening_tag.tpl');
        }

        return '';
    }

    public function addDebugConsolePlaceholder(): string
    {
        if (true === $this->module->isDebug()) {
            return $this->module->render('hooks/debug_hook/display_before_body_closing_tag.tpl');
        }

        return '';
    }
}
