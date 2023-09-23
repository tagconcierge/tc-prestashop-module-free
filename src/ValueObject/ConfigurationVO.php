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
            'view_item_list' => true,
            'select_item' => true,
            'view_item' => true,
            'add_to_cart' => false,
            'remove_from_cart' => true,
            'view_cart' => true,
            'begin_checkout' => true,
            'purchase' => false,
        ];
    }
}
