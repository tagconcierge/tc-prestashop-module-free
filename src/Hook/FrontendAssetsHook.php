<?php

namespace PrestaShop\Module\TagConciergeFree\Hook;

use Configuration;
use Hook as PrestaShopHook;
use PrestaShop\Module\TagConciergeFree\Model\Product as ProductModel;
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
        Hooks::ACTION_GET_PRODUCT_PROPERTIES_AFTER => [
            'addItemIdReferences',
        ],
    ];

    public function loadHeaderAssets(): string
    {
        $source = Configuration::get(ConfigurationVO::ITEM_ID_SOURCE);
        if (false === in_array($source, ConfigurationVO::getItemIdSources(), true)) {
            $source = ConfigurationVO::ITEM_ID_SOURCE_ID;
        }

        $pattern = Configuration::get(ConfigurationVO::ITEM_ID_PATTERN);
        if (false === $pattern) {
            $pattern = '';
        }

        $this->getContext()->smarty->assign('tc_item_id_config', json_encode([
            'itemIdSource' => $source,
            'itemIdPattern' => $pattern,
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));

        return $this->module->render('hooks/frontend_assets/display_header.tpl');
    }

    /**
     * Cart AJAX exposes a single reference (variant SKU, or the main SKU when the variant reference is empty).
     * Separate values are required to build item_id for add_to_cart and remove_from_cart.
     *
     * @param array $params
     */
    public function addItemIdReferences(array $params)
    {
        $source = Configuration::get(ConfigurationVO::ITEM_ID_SOURCE);
        if (false === in_array($source, [
            ConfigurationVO::ITEM_ID_SOURCE_SKU,
            ConfigurationVO::ITEM_ID_SOURCE_VARIANT_SKU,
            ConfigurationVO::ITEM_ID_SOURCE_PATTERN,
        ], true)) {
            return;
        }

        if (false === isset($params['product']) || false === is_array($params['product'])) {
            return;
        }

        $references = ProductModel::resolveReferences(
            (int) ($params['product']['id_product'] ?? 0),
            (int) ($params['product']['id_product_attribute'] ?? 0)
        );

        $params['product']['tc_sku'] = $references['sku'];
        $params['product']['tc_variant_sku'] = $references['variant_sku'];
    }

    public function loadGtmScript(): string
    {
        return PrestaShopHook::exec(Hooks::TC_DISPLAY_BEFORE_GTM_HEAD_SNIPPET) . Configuration::get(ConfigurationVO::GTM_CONTAINER_SNIPPET_HEAD);
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
