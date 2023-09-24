<?php

namespace PrestaShop\Module\TagConciergeFree\Install;

use Configuration;
use Module;
use PrestaShop\Module\TagConciergeFree\ValueObject\ConfigurationVO;

class Installer
{
    public function install(TagConciergeModuleInterface $module): bool
    {
        foreach (ConfigurationVO::getFields() as $key => $value) {
            if (ConfigurationVO::INSTANCE_UUID === $key) {
                continue;
            }
            $boolean = $value['boolean'] ?? false;
            Configuration::updateValue($key, $boolean ? false : '');
        }

        if (false === Configuration::get(ConfigurationVO::INSTANCE_UUID)) {
            Configuration::updateValue(ConfigurationVO::INSTANCE_UUID, $this->generateUuid());
        }

        return $this->registerHooks($module);
    }

    public function uninstall(TagConciergeModuleInterface $module): bool
    {
        foreach (array_keys(ConfigurationVO::getFields()) as $key) {
            if (ConfigurationVO::INSTANCE_UUID === $key) {
                continue;
            }
            Configuration::deleteByName($key);
        }

        return true;
    }

    private function registerHooks(TagConciergeModuleInterface $module): bool
    {
        foreach ($module->getHooks() as $hook) {
            if (false === $module->registerHook($hook)) {
                return false;
            }
        }

        return true;
    }

    private function generateUuid(): string
    {
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    }
}
