<?php

namespace PrestaShop\Module\TagConciergeFree\Controller\Admin;

use Configuration;
use Exception;
use ModuleAdminController;
use PrestaShop\Module\TagConciergeFree\ValueObject\ConfigurationVO;
use Tools;

class SettingsController extends ModuleAdminController
{
    private $configMap = [
        'basic' => [
            ConfigurationVO::STATE => 'state',
            ConfigurationVO::TRACK_USER_ID => 'trackUserId',
            ConfigurationVO::DEBUG => 'debug',
        ],
        'gtm_installation' => [
            ConfigurationVO::GTM_CONTAINER_SNIPPET_HEAD => 'gtmContainerSnippetHead',
            ConfigurationVO::GTM_CONTAINER_SNIPPET_BODY => 'gtmContainerSnippetBody',
        ],
        'server_side' => [
            ConfigurationVO::SERVER_CONTAINER_URL => 'serverContainerUrl',
            ConfigurationVO::LOAD_GTM_FROM_SERVER_CONTAINER => 'loadGtmFromServerContainer',
            ConfigurationVO::GA4_CLIENT_ACTIVATION_PATH => 'ga4ClientActivationPath',
            ConfigurationVO::GTM_SERVER_PREVIEW_HEADER => 'gtmServerPreviewHeader',
            ConfigurationVO::SERVER_PURCHASE_BACKGROUND => 'serverPurchaseBackground',
        ],
        'item_id' => [
            ConfigurationVO::ITEM_ID_SOURCE => 'itemIdSource',
            ConfigurationVO::ITEM_ID_PATTERN => 'itemIdPattern',
        ],
    ];

    private $booleanProperties = ['state', 'trackUserId', 'debug', 'loadGtmFromServerContainer', 'serverPurchaseBackground'];
    private $htmlProperties = ['gtmContainerSnippetHead', 'gtmContainerSnippetBody'];

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    private function getSectionSettings($section)
    {
        $settings = [];
        if (isset($this->configMap[$section])) {
            foreach ($this->configMap[$section] as $configKey => $frontendKey) {
                $value = Configuration::get($configKey);
                if ('itemIdSource' === $frontendKey && false === in_array($value, ConfigurationVO::getItemIdSources(), true)) {
                    $value = ConfigurationVO::ITEM_ID_SOURCE_ID;
                }
                if ('itemIdPattern' === $frontendKey && false === $value) {
                    $value = '';
                }
                if ('ga4ClientActivationPath' === $frontendKey && (false === $value || '' === $value)) {
                    $value = '/mp';
                }
                if ('gtmServerPreviewHeader' === $frontendKey && false === $value) {
                    $value = '';
                }
                $settings[$frontendKey] = in_array($frontendKey, $this->booleanProperties)
                    ? (bool) $value
                    : $value;
            }
        }

        return $settings;
    }

    private function saveSectionSettings($section, $formData)
    {
        if (isset($this->configMap[$section])) {
            foreach ($this->configMap[$section] as $configKey => $frontendKey) {
                if (isset($formData[$frontendKey])) {
                    if ('itemIdSource' === $frontendKey && false === in_array($formData[$frontendKey], ConfigurationVO::getItemIdSources(), true)) {
                        throw new Exception('Invalid item id source.');
                    }
                    $isHtml = in_array($frontendKey, $this->htmlProperties);
                    if (true === $isHtml) {
                        $usePurifier = Configuration::get('PS_USE_HTMLPURIFIER');
                        Configuration::updateValue('PS_USE_HTMLPURIFIER', 0);
                    }
                    Configuration::updateValue($configKey, $formData[$frontendKey], $isHtml);
                    if (true === $isHtml) {
                        Configuration::updateValue('PS_USE_HTMLPURIFIER', $usePurifier);
                    }
                }
            }
        }
    }

    private function saveServerEventsConfiguration($formData)
    {
        foreach (ConfigurationVO::getServerEvents() as $event => $isPro) {
            $eventKey = sprintf('event_server_%s', $event);
            if (isset($formData[$eventKey])) {
                $configKey = sprintf('TC_EVENT_STATE_SERVER_%s', strtoupper($event));
                Configuration::updateValue($configKey, (bool) $formData[$eventKey]);
            }
        }
    }

    private function saveEventsConfiguration($formData)
    {
        $events = ConfigurationVO::getEvents();

        foreach ($events as $event => $isPro) {
            $eventKey = sprintf('event_%s', $event);
            if (isset($formData[$eventKey])) {
                $configKey = sprintf('TC_EVENT_STATE_BROWSER_%s', strtoupper($event));
                $value = $formData[$eventKey];

                Configuration::updateValue($configKey, (bool) $value);
            }
        }
    }

    public function ajaxProcessSaveSettings()
    {
        $response = ['success' => false, 'message' => ''];

        try {
            $section = Tools::getValue('section', 'all');
            $formData = $_POST;

            $this->saveSectionSettings($section, $formData);

            if ($section === 'basic') {
                $this->saveEventsConfiguration($formData);
                $this->saveServerEventsConfiguration($formData);
            }

            $response['success'] = true;
            $response['message'] = 'Settings saved successfully';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        exit(json_encode($response));
    }

    public function ajaxProcessGetSettings()
    {
        $response = ['success' => false, 'data' => null];

        try {
            $section = Tools::getValue('section', 'all');
            $frontendSettings = [];

            $frontendSettings = $this->getSectionSettings($section);

            $response['success'] = true;
            $response['data'] = $frontendSettings;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        exit(json_encode($response));
    }

    public function ajaxProcessGetServerEvents()
    {
        $response = ['success' => false, 'data' => null];

        try {
            $eventsList = [];

            foreach (ConfigurationVO::getServerEvents() as $eventName => $isPro) {
                $configKey = sprintf('TC_EVENT_STATE_SERVER_%s', strtoupper($eventName));
                $eventsList[] = [
                    'name' => $eventName,
                    'isPro' => $isPro,
                    'enabled' => '1' === (string) Configuration::get($configKey),
                ];
            }

            $response['success'] = true;
            $response['data'] = $eventsList;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        exit(json_encode($response));
    }

    public function ajaxProcessGetEvents()
    {
        $response = ['success' => false, 'data' => null];

        try {
            $events = ConfigurationVO::getEvents();
            $eventsList = [];

            foreach ($events as $eventName => $isPro) {
                $configKey = sprintf('TC_EVENT_STATE_BROWSER_%s', strtoupper($eventName));
                $enabled = Configuration::hasKey($configKey) ? (bool) Configuration::get($configKey) : true;

                $eventsList[] = [
                    'name' => $eventName,
                    'isPro' => $isPro,
                    'enabled' => $enabled,
                ];
            }

            $response['success'] = true;
            $response['data'] = $eventsList;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        exit(json_encode($response));
    }
}
