<?php

namespace PrestaShop\Module\TagConciergeFree\Hook;

class Hooks
{
    /** @var string */
    public const DISPLAY_HEADER = 'displayHeader';

    /** @var string */
    public const DISPLAY_AFTER_BODY_OPENING_TAG = 'displayAfterBodyOpeningTag';

    /** @var string */
    public const DISPLAY_ORDER_CONFIRMATION = 'displayOrderConfirmation';

    /** @var string */
    public const DISPLAY_BEFORE_BODY_CLOSING_TAG = 'displayBeforeBodyClosingTag';

    /** @var string */
    public const TC_DISPLAY_BEFORE_GTM_HEAD_SNIPPET = 'tcDisplayBeforeGtmHeadSnippet';
}
