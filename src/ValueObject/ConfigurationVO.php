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
