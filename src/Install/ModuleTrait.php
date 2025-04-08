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

    public function resetHooks(): bool
    {
        $installer = InstallerFactory::create();

        return $installer->resetHooks($this);
    }

    /**
     * @throws SmartyException
     */
    public function getContent(): string
    {
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('instance_uuid', PrestaShopConfiguration::get(ConfigurationVO::INSTANCE_UUID));
        $this->context->smarty->assign('link', $this->context->link);
        $this->context->smarty->assign('is_pro', $this->pro ? 'true' : 'false');
        $this->context->smarty->assign('module_name', $this->displayName);
        $this->context->smarty->assign('module_version', $this->version);

        return $this->render('admin/configure.tpl');
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
        $path = sprintf('views/templates/%s', $templatePath);
        $templateExists = file_exists(sprintf('%s/views/templates/%s', dirname(static::MODULE_FILE), $templatePath));

        if (false === $templateExists) {
            $path = sprintf('vendor/tagconcierge/tc-prestashop-module-free/views/templates/%s', $templatePath);
        }

        return $this->display(
            static::MODULE_FILE,
            $path
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
