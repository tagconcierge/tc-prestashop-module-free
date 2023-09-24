<?php

namespace PrestaShop\Module\TagConciergeFree\Install;

interface TagConciergeModuleInterface
{
    public function render(string $templatePath): string;

    public function getHooks(): array;
}
