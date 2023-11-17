<?php

namespace PrestaShop\Module\TagConciergeFree\ValueObject;

class EcommerceEventVO
{
    public const BROWSER_SIDE = 'browser';

    public const SERVER_SIDE = 'server';

    public const VIEW_ITEM_LIST = 'view_item_list';

    public const VIEW_ITEM = 'view_item';

    public const ADD_TO_CART = 'add_to_cart';

    public const SELECT_ITEM = 'select_item';

    public const REMOVE_FROM_CART = 'remove_from_cart';

    public const VIEW_CART = 'view_cart';

    public const BEGIN_CHECKOUT = 'begin_checkout';

    public const ADD_SHIPPING_INFO = 'add_shipping_info';

    public const PURCHASE = 'purchase';

    public const USER_ID = 'user_id';
}
