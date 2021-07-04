<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\PluginUpdate;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Utils;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\UtilsProvider;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Handle Real Product Manager API calls.
 */
class LicenseActivation {
    use UtilsProvider;
    const ENDPOINT_LICENSE_ACTIVATION = '1.0.0/license/activation';
    /**
     * PluginUpdate instance.
     *
     * @var PluginUpdate
     */
    private $pluginUpdate;
    /**
     * C'tor.
     *
     * @param PluginUpdate $pluginUpdate
     * @codeCoverageIgnore
     */
    private function __construct($pluginUpdate) {
        $this->pluginUpdate = $pluginUpdate;
    }
    /**
     * `POST` to the REST API of Real Product Manager.
     *
     * @param string $code
     * @param string $uuid
     * @param string $installationType
     * @param boolean $telemetry
     * @param boolean $newsletterOptIn
     * @param string $firstName
     * @param string $email
     */
    public function post(
        $code = '',
        $uuid = '',
        $installationType = 'prod',
        $telemetry = \false,
        $newsletterOptIn = \false,
        $firstName = '',
        $email = ''
    ) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ip = empty($ip) ? '127.0.0.1' : $ip;
        $product = $this->getPluginUpdate()
            ->getInitiator()
            ->getProductAndVariant();
        $body = [
            'licenseActivation' => [
                'license' => [
                    'product' => ['id' => $product[0]],
                    'productVariant' => ['id' => $product[1]],
                    'licenseKey' => $code
                ],
                'client' => ['uuid' => $uuid, 'properties' => $this->getClientProperties()],
                'type' => $installationType,
                'telemetryDataSharingOptIn' => $telemetry,
                'newsletterOptIn' => $newsletterOptIn,
                'ip' => $ip,
                'properties' => \array_filter([
                    [
                        'key' => 'pluginVersion',
                        'value' => $this->getPluginUpdate()
                            ->getInitiator()
                            ->getPluginVersion()
                    ],
                    $firstName ? ['key' => 'firstName', 'value' => $firstName] : \false,
                    $email ? ['key' => 'email', 'value' => $email] : \false
                ])
            ]
        ];
        return \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\ClientUtils::request(
            $this->getPluginUpdate()->getInitiator(),
            self::ENDPOINT_LICENSE_ACTIVATION,
            $body,
            'POST'
        );
    }
    /**
     * `PATCH` to the REST API of Real Product Manager.
     *
     * @param string $code
     * @param string $uuid
     */
    public function patch($code, $uuid) {
        $body = [
            'licenseActivation' => [
                'license' => ['licenseKey' => $code],
                'client' => ['uuid' => $uuid, 'properties' => $this->getClientProperties()],
                'properties' => [
                    [
                        'key' => 'pluginVersion',
                        'value' => $this->getPluginUpdate()
                            ->getInitiator()
                            ->getPluginVersion()
                    ]
                ]
            ]
        ];
        return \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\ClientUtils::request(
            $this->getPluginUpdate()->getInitiator(),
            self::ENDPOINT_LICENSE_ACTIVATION,
            $body,
            'PATCH'
        );
    }
    /**
     * `GET` to the REST API of Real Product Manager.
     *
     * @param string $code
     * @param string $uuid
     */
    public function get($code, $uuid) {
        return \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\ClientUtils::request(
            $this->getPluginUpdate()->getInitiator(),
            self::ENDPOINT_LICENSE_ACTIVATION,
            ['licenseKey' => $code, 'clientUuid' => $uuid],
            'GET'
        );
    }
    /**
     * `DELETE` to the REST API of Real Product Manager.
     *
     * @param string $code
     * @param string $uuid
     */
    public function delete($code, $uuid) {
        return \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\ClientUtils::request(
            $this->getPluginUpdate()->getInitiator(),
            self::ENDPOINT_LICENSE_ACTIVATION,
            ['licenseActivation' => ['license' => ['licenseKey' => $code], 'client' => ['uuid' => $uuid]]],
            'DELETE'
        );
    }
    /**
     * Client properties.
     */
    protected function getClientProperties() {
        return [
            [
                'key' => 'hostname',
                'value' => \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\Utils::getCurrentHostname()
            ],
            ['key' => 'wpVersion', 'value' => get_bloginfo('version')],
            ['key' => 'wpLanguage', 'value' => get_locale()],
            ['key' => 'phpVersion', 'value' => \phpversion()]
        ];
    }
    /**
     * Get plugin update instance.
     *
     * @codeCoverageIgnore
     */
    public function getPluginUpdate() {
        return $this->pluginUpdate;
    }
    /**
     * New instance.
     *
     * @param PluginUpdate $pluginUpdate
     * @codeCoverageIgnore
     */
    public static function instance($pluginUpdate) {
        return new \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\LicenseActivation(
            $pluginUpdate
        );
    }
}
