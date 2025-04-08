{* Tag Concierge Settings *}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
<div id="tag-concierge-settings-app" 
    data-admin-link="{$link->getAdminLink('AdminTagConcierge')}"
    data-instance-uuid="{$instance_uuid|escape:'html':'UTF-8'}"
    data-pro="{$is_pro|escape:'html':'UTF-8'}"
    data-module-name="{$module_name|escape:'html':'UTF-8'}"
    data-module-version="{$module_version|escape:'html':'UTF-8'}"></div>
<script src="{$module_dir}views/js/admin-settings.js?v={$module_version}"></script>