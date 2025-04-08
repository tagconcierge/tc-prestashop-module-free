<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param TagConciergeFree $module
 *
 * @return bool
 */
function upgradeModule110(TagConciergeFree $module)
{
    $tabId = Tab::getIdFromClassName('TagConciergeFreeAdminSettings');
    
    if (!$tabId) {
        $tab = new Tab();
        $tab->active = 0;
        $tab->class_name = 'TagConciergeFreeAdminSettings';
        $tab->id_parent = 0;
        $tab->module = $module->name;
        $tab->enabled = 1;
        
        $languages = Language::getLanguages();
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = 'TagConciergeFreeAdminSettings';
        }
        
        if (!$tab->add()) {
            return false;
        }
    }
    
    return $module->resetHooks();
}