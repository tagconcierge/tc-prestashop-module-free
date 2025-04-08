<?php

use PrestaShop\Module\TagConciergeFree\ValueObject\ConfigurationVO;

class AdminTagConciergeController extends ModuleAdminController
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
        ],
    ];

    private $booleanProperties = ['state', 'trackUserId', 'debug', 'loadGtmFromServerContainer'];
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
            }

            $response['success'] = true;
            $response['message'] = $this->l('Settings saved successfully');
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
