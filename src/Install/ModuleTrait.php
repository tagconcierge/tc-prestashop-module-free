<?php

namespace PrestaShop\Module\TagConciergeFree\Install;

use Configuration as PrestaShopConfiguration;
use PrestaShop\Module\TagConciergeFree\Hook\Event\AbstractEcommerceEventHook;
use PrestaShop\Module\TagConciergeFree\ValueObject\ConfigurationVO;
use PrestaShopLogger;
use RuntimeException;
use SmartyException;
use Tools as PrestaShopTools;
use PrestaShop\Module\TagConciergeFree\Hook\HookProvider;

trait ModuleTrait
{
    /** @var array */
    private $hooks;

    /**
     * @var HookProvider
     */
    private $hookProvider;

    private function init(): void
    {
        @define('TC_VERSION', $this->version);

        $this->hookProvider = new HookProvider($this);
        $this->setupHooks();
    }

    public function install(): bool
    {
        if (false === parent::install()) {
            return false;
        }

        $installer = InstallerFactory::create();

        return $installer->install($this);
    }

    public function uninstall(): bool
    {
        if (false === parent::uninstall()) {
            return false;
        }

        $installer = InstallerFactory::create();

        return $installer->uninstall($this);
    }

    /**
     * @throws SmartyException
     */
    public function getContent(): string
    {
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('instance_uuid', PrestaShopConfiguration::get(ConfigurationVO::INSTANCE_UUID));
        $this->context->smarty->assign('plugin_version', $this->version);

        $output = '';

        if (PrestaShopTools::isSubmit('submit_tc')) {
            // get actual value of PS_USE_HTMLPURIFIER
            $usePurifier = PrestaShopConfiguration::get('PS_USE_HTMLPURIFIER');
            // disable it to allow store gtm snippets in configuration
            PrestaShopConfiguration::updateValue('PS_USE_HTMLPURIFIER', 0);

            $state = PrestaShopTools::getValue(ConfigurationVO::STATE);
            $containerHead = PrestaShopTools::getValue(ConfigurationVO::GTM_CONTAINER_SNIPPET_HEAD);
            $containerBody = PrestaShopTools::getValue(ConfigurationVO::GTM_CONTAINER_SNIPPET_BODY);

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
                        PrestaShopTools::getValue($key),
                        in_array($key, $htmlFields, true)
                    );
                }

                $output .= $this->displayConfirmation('Settings updated.');
            }
            // restore original value of PS_USE_HTMLPURIFIER
            PrestaShopConfiguration::updateValue('PS_USE_HTMLPURIFIER', $usePurifier);
        }

        if (PrestaShopTools::isSubmit('submit_tc_events')) {
            foreach (ConfigurationVO::getEvents() as $event => $isPro) {
                $key = sprintf('TC_EVENT_STATE_BROWSER_%s', strtoupper($event));

                PrestaShopConfiguration::updateValue(
                    $key,
                    (true === $isPro && false === $this->pro) ? false : PrestaShopTools::getValue($key)
                );
            }

            $output .= $this->displayConfirmation('Settings updated.');
        }

        /*
         * general settings form
         */
        $helper = new \HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = PrestaShopConfiguration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_tc';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = PrestaShopTools::getAdminTokenLite('AdminModules');

        $vars = [];
        $input = [];

        foreach (ConfigurationVO::getFields() as $key => $value) {
            $vars[$key] = PrestaShopConfiguration::get($key);
            $value['name'] = $key;

            if (ConfigurationVO::TRACK_USER_ID === $key && false === $this->pro) {
                $value['disabled'] = true;
                $value['desc'] .= ' <a href="https://tagconcierge.com/tag-concierge-for-prestashop" target="_blank">Upgrade to PRO</a>';
                $vars[$key] = false;
            }

            $input[] = $value;
        }

        $helper->tpl_vars = [
            'fields_value' => $vars,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $generalSettingsForm = $helper->generateForm([[
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
        ]]);
        /*
         * /general settings form
         */

        /*
         * events settings form
         */
        $helper = new \HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = PrestaShopConfiguration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_tc_events';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = PrestaShopTools::getAdminTokenLite('AdminModules');

        $vars = [];
        $input = [];

        foreach (ConfigurationVO::getEvents() as $event => $isPro) {
            $disabled = false;
            $description = '';
            $key = sprintf('TC_EVENT_STATE_BROWSER_%s', strtoupper($event));

            $vars[$key] = true === PrestaShopConfiguration::hasKey($key) ? PrestaShopConfiguration::get($key) : true;

            if (true === $isPro && false === $this->pro) {
                $vars[$key] = false;
                $disabled = true;
                $description = '<a href="https://tagconcierge.com/tag-concierge-for-prestashop" target="_blank">Upgrade to PRO</a>';
            }

            $input[] = [
                'name' => $key,
                'desc' => $description,
                'type' => 'switch',
                'label' => $event,
                'disabled' => $disabled,
                'is_bool' => true,
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
            ];
        }

        $helper->tpl_vars = [
            'fields_value' => $vars,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $eventsSettingsForm = $helper->generateForm([[
            'form' => [
                'legend' => [
                    'title' => 'Events settings',
                    'icon' => 'icon-bell',
                ],
                'input' => $input,
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ]]);
        /*
         * /events settings form
         */

        return $output . $generalSettingsForm . $eventsSettingsForm . $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
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

    public function isDebug(): bool
    {
        return '1' === PrestaShopConfiguration::get(ConfigurationVO::DEBUG);
    }

    public function render(string $templatePath): string
    {
        return $this->display(
            static::MODULE_FILE,
            sprintf('views/templates/%s', $templatePath)
        );
    }

    public function __call(string $name, array $arguments)
    {
        try {
            $hookName = null;

            if ('hook' === PrestaShopTools::substr($name, 0, 4)) {
                $hookName = lcfirst(PrestaShopTools::substr($name, 4));
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

                if ((true === $hook instanceof AbstractEcommerceEventHook) && false === $hook->isEnabled()) {
                    continue;
                }

                foreach ($callbacks as $callback) {
                    $result .= $hook->{$callback}($arguments[0]);
                }
            }

            if (false === empty($result)) {
                return $result;
            }
        } catch (\Throwable $e) {
            PrestaShopLogger::addLog(
                sprintf(
                    '%s: %s',
                    $this->name,
                    $e->getMessage()
                ),
                $severityError = 3
            );
        }

        return null;
    }
}
