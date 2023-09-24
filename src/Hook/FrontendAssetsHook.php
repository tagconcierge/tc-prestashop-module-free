<?php

namespace PrestaShop\Module\TagConciergeFree\Hook;

use Configuration;
use Context;
use PrestaShop\Module\TagConciergeFree\ValueObject\ConfigurationVO;

class FrontendAssetsHook extends AbstractHook
{
    /** @var array */
    public const HOOKS = [
        Hooks::DISPLAY_HEADER => [
            'loadGtmScript',
            'loadAssets',
        ],
        Hooks::DISPLAY_AFTER_BODY_OPENING_TAG => [
            'loadGtmFrame',
        ],
    ];

    public function loadAssets(): string
    {
        $context = Context::getContext();

        $assetsVersion = (_PS_MODE_DEV_ || $this->module->isDebug()) ? time() : TC_VERSION;

        $context->smarty->assign(
            'tc_app_js_path',
            sprintf(
                '%s%s/views/js/front-office.js',
                _MODULE_DIR_,
                $this->module->name
            )
        );
        $context->smarty->assign('tc_debug', $this->module->isDebug() ? 'true' : 'false');
        $context->smarty->assign('tc_assets_version', $assetsVersion);

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
}
