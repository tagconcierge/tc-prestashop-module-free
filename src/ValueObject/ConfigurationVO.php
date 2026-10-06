<?php

namespace PrestaShop\Module\TagConciergeFree\ValueObject;

class ConfigurationVO
{
    /** @var string */
    public const STATE = 'TC_STATE';

    /** @var string */
    public const TRACK_USER_ID = 'TC_TRACK_USER_ID';

    /** @var string */
    public const GTM_CONTAINER_SNIPPET_HEAD = 'TC_GTM_CONTAINER_SNIPPET_HEAD';

    /** @var string */
    public const GTM_CONTAINER_SNIPPET_BODY = 'TC_GTM_CONTAINER_SNIPPET_BODY';

    /** @var string */
    public const DEBUG = 'TC_DEBUG';

    /** @var string */
    public const INSTANCE_UUID = 'TC_INSTANCE_UUID';

    public const SERVER_CONTAINER_URL = 'TC_SERVER_CONTAINER_URL';

    public const LOAD_GTM_FROM_SERVER_CONTAINER = 'TC_LOAD_GTM_FROM_SERVER_CONTAINER';

    public const GA4_CLIENT_ACTIVATION_PATH = 'TC_GA4_CLIENT_ACTIVATION_PATH';

    public const GTM_SERVER_PREVIEW_HEADER = 'TC_GTM_SERVER_PREVIEW_HEADER';

    public const SERVER_PURCHASE_BACKGROUND = 'TC_SERVER_PURCHASE_BACKGROUND';

    public const ITEM_ID_SOURCE = 'TC_ITEM_ID_SOURCE';

    public const ITEM_ID_PATTERN = 'TC_ITEM_ID_PATTERN';

    public const ITEM_ID_SOURCE_ID = 'id';

    public const ITEM_ID_SOURCE_SKU = 'sku';

    public const ITEM_ID_SOURCE_VARIANT_SKU = 'variant_sku';

    public const ITEM_ID_SOURCE_PATTERN = 'pattern';
    /**
     * @var array
     */
    private static $fields = [
        self::STATE => [
            'type' => 'switch',
            'label' => 'State',
            'is_bool' => true,
            'desc' => 'General state of the module.',
            'values' => [
                [
                    'id' => 'active',
                    'value' => true,
                    'label' => 'Enabled',
                ],
                [
                    'id' => 'inactive',
                    'value' => false,
                    'label' => 'Disabled',
                ],
            ],
        ],
        self::TRACK_USER_ID => [
            'type' => 'switch',
            'label' => 'Client ID tracking',
            'is_bool' => true,
            'desc' => 'ID of logged-in client will be passed to Google Analytics.',
            'values' => [
                [
                    'id' => 'active',
                    'value' => true,
                    'label' => 'Enabled',
                ],
                [
                    'id' => 'inactive',
                    'value' => false,
                    'label' => 'Disabled',
                ],
            ],
        ],
        self::GTM_CONTAINER_SNIPPET_HEAD => [
            'type' => 'textarea',
            'label' => 'GTM snippet head',
            'desc' => 'Paste the first snippet provided by GTM. It will be loaded in the <head> of the page.',
            'required' => true,
        ],
        self::GTM_CONTAINER_SNIPPET_BODY => [
            'type' => 'textarea',
            'label' => 'GTM snippet body',
            'desc' => 'Paste the second snippet provided by GTM. It will be loaded after opening <body> tag.',
            'required' => true,
        ],
        self::ITEM_ID_SOURCE => [
            'type' => 'select',
            'label' => 'Item ID source',
            'desc' => 'Value used as item_id in ecommerce events.',
        ],
        self::ITEM_ID_PATTERN => [
            'type' => 'text',
            'label' => 'Item ID pattern',
            'desc' => 'Pattern used when item id source is set to a custom pattern. Placeholders: {id}, {variant_id}, {sku}, {variant_sku}.',
        ],
        self::DEBUG => [
            'type' => 'switch',
            'label' => 'Debug',
            'is_bool' => true,
            'desc' => 'Debug mode.',
            'values' => [
                [
                    'id' => 'active',
                    'value' => true,
                    'label' => 'Enabled',
                ],
                [
                    'id' => 'inactive',
                    'value' => false,
                    'label' => 'Disabled',
                ],
            ],
        ],
    ];

    public static function getFields(): array
    {
        return static::$fields;
    }

    public static function getItemIdSources(): array
    {
        return [
            self::ITEM_ID_SOURCE_ID,
            self::ITEM_ID_SOURCE_SKU,
            self::ITEM_ID_SOURCE_VARIANT_SKU,
            self::ITEM_ID_SOURCE_PATTERN,
        ];
    }

    public static function getServerEvents(): array
    {
        return [
            EcommerceEventVO::PURCHASE => true,
        ];
    }

    public static function getEvents(): array
    {
        return [
            EcommerceEventVO::VIEW_ITEM_LIST => true,
            EcommerceEventVO::SELECT_ITEM => true,
            EcommerceEventVO::VIEW_ITEM => true,
            EcommerceEventVO::ADD_TO_CART => false,
            EcommerceEventVO::REMOVE_FROM_CART => true,
            EcommerceEventVO::VIEW_CART => true,
            EcommerceEventVO::BEGIN_CHECKOUT => true,
            EcommerceEventVO::ADD_SHIPPING_INFO => true,
            EcommerceEventVO::ADD_PAYMENT_INFO => true,
            EcommerceEventVO::PURCHASE => false,
        ];
    }
}
