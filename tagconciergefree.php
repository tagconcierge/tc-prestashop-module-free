<?php

use Configuration as PrestaShopConfiguration;
use PrestaShop\Module\TagConciergeFree\Hook;
use PrestaShop\Module\TagConciergeFree\Hook\HookProvider;
use PrestaShop\Module\TagConciergeFree\Install\InstallerFactory;
use PrestaShop\Module\TagConciergeFree\ValueObject\ConfigurationVO;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'tagconciergefree/vendor/autoload.php';

class TagConciergeFree extends Module
{
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

    /** @var array */
    private $hooks;

    /**
     * @var HookProvider
     */
    private $hookProvider;

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

        @define('TC_VERSION', $this->version);

        parent::__construct();

        $this->displayName = $this->trans('Tag Concierge Free', [], 'Modules.TagConciergeFree.Admin');
        $this->description = $this->trans('Leverage the Flexibility of Google Tag Manager to Measure and Optimize Sales Results (GA4 ready).', [], 'Modules.TagConciergeFree.Admin');

        $this->hookProvider = new HookProvider($this);

        $this->setupHooks();
    }

    /**
     * {@inheritDoc}
     */
    public function install()
    {
        if (false === parent::install()) {
            return false;
        }

        $installer = InstallerFactory::create();

        return $installer->install($this);
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall()
    {
        if (false === parent::uninstall()) {
            return false;
        }

        $installer = InstallerFactory::create();

        return $installer->uninstall($this);
    }

    /**
     * @throws PrestaShopException
     * @throws SmartyException
     *
     * @todo refactor
     */
    public function getContent(): string
    {
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('instance_uuid', PrestaShopConfiguration::get(ConfigurationVO::INSTANCE_UUID));
        $this->context->smarty->assign('plugin_version', $this->version);

        $output = '';

        if (Tools::isSubmit('submit_tc')) {
            // get actual value of PS_USE_HTMLPURIFIER
            $usePurifier = PrestaShopConfiguration::get('PS_USE_HTMLPURIFIER');
            // disable it to allow store gtm snippets in configuration
            PrestaShopConfiguration::updateValue('PS_USE_HTMLPURIFIER', 0);

            $state = Tools::getValue(ConfigurationVO::STATE);
            $containerHead = Tools::getValue(ConfigurationVO::GTM_CONTAINER_SNIPPET_HEAD);
            $containerBody = Tools::getValue(ConfigurationVO::GTM_CONTAINER_SNIPPET_BODY);

            $htmlFields = [
                ConfigurationVO::GTM_CONTAINER_SNIPPET_HEAD,
                ConfigurationVO::GTM_CONTAINER_SNIPPET_BODY,
            ];

            if ($state && (true === empty($containerHead) || true === empty($containerBody))) {
                $output .= $this->displayError('Please, provide valid GTM snippets.');
            } else {
                foreach (array_keys(ConfigurationVO::getFields()) as $key) {
                    PrestaShopConfiguration::updateValue(
                        $key,
                        Tools::getValue($key),
                        in_array($key, $htmlFields, true)
                    );
                }

                $output .= $this->displayConfirmation('Settings updated.');
            }
            // restore original value of PS_USE_HTMLPURIFIER
            PrestaShopConfiguration::updateValue('PS_USE_HTMLPURIFIER', $usePurifier);
        }

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = PrestaShopConfiguration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_tc';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $vars = [];
        $input = [];

        foreach (ConfigurationVO::getFields() as $key => $value) {
            $vars[$key] = PrestaShopConfiguration::get($key);

            $value['name'] = $key;
            $input[] = $value;
        }

        $helper->tpl_vars = [
            'fields_value' => $vars,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $output . $helper->generateForm([[
            'form' => [
                'legend' => [
                    'title' => 'General settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => $input,
                'submit' => [
                    'title' => 'Save',
                ],
            ],
            ]]) . $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
    }

    public function getHooks(): array
    {
        return array_keys($this->hooks);
    }

    private function isModuleActive(): bool
    {
        return '1' === PrestaShopConfiguration::get(ConfigurationVO::STATE);
    }

    private function setupHooks(): void
    {
        foreach (self::HOOKS as $hookClass) {
            foreach ($hookClass::HOOKS as $hookName => $callbacks) {
                $this->hooks[$hookName][$hookClass] = $callbacks;
            }
        }
    }

    public static function isDebug(): bool
    {
        return '1' === Configuration::get(ConfigurationVO::DEBUG);
    }

    /**
     * @return string|null
     */
    public function __call(string $name, array $arguments)
    {
        try {
            $hookName = null;

            if ('hook' === Tools::substr($name, 0, 4)) {
                $hookName = lcfirst(Tools::substr($name, 4));
            }

            if (null === $hookName) {
                throw new RuntimeException(sprintf('Method not implemented: %s.', $name));
            }

            if (false === isset($this->hooks[$hookName])) {
                return null;
            }

            if (false === $this->isModuleActive()) {
                return null;
            }

            $result = '';

            foreach ($this->hooks[$hookName] as $hookClass => $callbacks) {
                $hook = $this->hookProvider->provide($hookClass);

                foreach ($callbacks as $callback) {
                    $result .= $hook->{$callback}($arguments[0]);
                }
            }

            if (false === empty($result)) {
                return $result;
            }
        } catch (Throwable $e) {
            PrestaShopLogger::addLog(
                sprintf(
                    'Tag Concierge Free Error: %s',
                    $e->getMessage()
                ),
                PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR
            );
        }

        return null;
    }
}
