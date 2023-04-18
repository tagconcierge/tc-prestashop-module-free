<?php

namespace PrestaShop\Module\TagConciergeFree\Install;

class InstallerFactory
{
    public static function create(): Installer
    {
        return new Installer();
    }
}
