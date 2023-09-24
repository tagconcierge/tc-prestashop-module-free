<?php

namespace PrestaShop\Module\TagConciergeFree\Hook;

use Configuration;
use Context;
use PrestaShop\Module\TagConciergeFree\Hook\Hooks;
use PrestaShop\Module\TagConciergeFree\ValueObject\ConfigurationVO;

class FrontendAssetsHook extends AbstractHook
{
    /** @var array */
    public const HOOKS = [
        Hooks::DISPLAY_HEADER => [
            'loadHeaderAssets',
            'loadGtmScript',
        ],
        Hooks::DISPLAY_AFTER_BODY_OPENING_TAG => [
            'loadGtmFrame',
        ],
        Hooks::DISPLAY_BEFORE_BODY_CLOSING_TAG => [
            'loadFooterAssets',
        ],
    ];

    public function loadHeaderAssets(): string
    {
        return $this->module->render('hooks/frontend_assets/display_header.tpl');
    }

    public function loadGtmScript(): string
    {
        return Configuration::get(ConfigurationVO::GTM_CONTAINER_SNIPPET_HEAD);
    }

    public function loadGtmFrame(): string
    {
        return Configuration::get(ConfigurationVO::GTM_CONTAINER_SNIPPET_BODY);
    }

    public function loadFooterAssets(): string
    {
        return $this->module->render('hooks/frontend_assets/display_before_body_closing_tag.tpl');
    }
}
