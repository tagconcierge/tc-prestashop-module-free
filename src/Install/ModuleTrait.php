<?php

namespace PrestaShop\Module\TagConciergeFree\Install;

use Configuration as PrestaShopConfiguration;
use PrestaShopLogger;
use Tools as PrestaShopTools;
use PrestaShop\Module\TagConciergeFree\Hook\HookProvider;
use PrestaShop\Module\TagConciergeFree\Install\InstallerFactory;
use PrestaShop\Module\TagConciergeFree\ValueObject\ConfigurationVO;

trait ModuleTrait
{
    private function init()
    {
        @define('TC_VERSION', $this->version);

        $this->hookProvider = new HookProvider($this);
        $this->setupHooks();
    }

    public function install()
    {
        if (false === parent::install()) {
            return false;
        }

        $installer = InstallerFactory::create();

        return $installer->install($this);
    }

    public function uninstall()
    {
        if (false === parent::uninstall()) {
            return false;
        }

        $installer = InstallerFactory::create();

        return $installer->uninstall($this);
    }

    public function getContent(): string
    {
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('instance_uuid', PrestaShopConfiguration::get($this->configurationVO::INSTANCE_UUID));
        $this->context->smarty->assign('plugin_version', $this->version);

        $output = '';

        if (PrestaShopTools::isSubmit('submit_tc')) {
            // get actual value of PS_USE_HTMLPURIFIER
            $usePurifier = PrestaShopConfiguration::get('PS_USE_HTMLPURIFIER');
            // disable it to allow store gtm snippets in configuration
            PrestaShopConfiguration::updateValue('PS_USE_HTMLPURIFIER', 0);

            $state = PrestaShopTools::getValue($this->configurationVO::STATE);
            $containerHead = PrestaShopTools::getValue($this->configurationVO::GTM_CONTAINER_SNIPPET_HEAD);
            $containerBody = PrestaShopTools::getValue($this->configurationVO::GTM_CONTAINER_SNIPPET_BODY);

            $htmlFields = [
                $this->configurationVO::GTM_CONTAINER_SNIPPET_HEAD,
                $this->configurationVO::GTM_CONTAINER_SNIPPET_BODY,
            ];

            if ($state && (true === empty($containerHead) || true === empty($containerBody))) {
                $output .= $this->displayError('Please, provide valid GTM snippets.');
            } else {
                foreach (array_keys($this->configurationVO::getFields()) as $key) {
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

        foreach ($this->configurationVO::getFields() as $key => $value) {
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
        return '1' === PrestaShopConfiguration::get($this->configurationVO::STATE);
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
        return '1' === \Configuration::get($this->configurationVO::DEBUG);
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
