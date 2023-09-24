<?php

use PrestaShop\Module\TagConciergeFree\Hook;
use PrestaShop\Module\TagConciergeFree\Install\ModuleTrait;
use PrestaShop\Module\TagConciergeFree\Install\TagConciergeModuleInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

if ('vendor' !== basename(realpath(dirname(__FILE__).'/../../'))) {
    require_once _PS_MODULE_DIR_ . 'tagconciergefree/vendor/autoload.php';
}

class TagConciergeFree extends Module implements TagConciergeModuleInterface
{
    use ModuleTrait;

    /** @var array */
    const HOOKS = [
        Hook\FrontendAssetsHook::class,
        Hook\DebugHook::class,
        // ecommerce browser
        Hook\Event\Browser\AddToCartHook::class,
        Hook\Event\Browser\PurchaseHook::class,
    ];

    /** @var string */
    const MODULE_FILE = _PS_MODULE_DIR_ . 'tagconciergefree/tagconciergefree.php';

    private $pro = false;

    /**
     * TagConcierge constructor.
     */
    public function __construct()
    {
        $this->name = 'tagconciergefree';
        $this->author = 'Tag Concierge';
        $this->version = '1.0.3';
        $this->ps_versions_compliancy = ['min' => '1.7.1.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->tab = 'advertising_marketing';

        parent::__construct();

        $this->displayName = $this->trans('Tag Concierge Free', [], 'Modules.TagConciergeFree.Admin');
        $this->description = $this->trans('Leverage the Flexibility of Google Tag Manager to Measure and Optimize Sales Results (GA4 ready).', [], 'Modules.TagConciergeFree.Admin');

        $this->init();
    }
}
