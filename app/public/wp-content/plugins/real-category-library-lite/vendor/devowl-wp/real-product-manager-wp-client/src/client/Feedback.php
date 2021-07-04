<?php

namespace DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client;

use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\PluginUpdate;
use DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\UtilsProvider;
// @codeCoverageIgnoreStart
\defined('ABSPATH') or die('No script kiddies please!');
// Avoid direct file request
// @codeCoverageIgnoreEnd
/**
 * Handle Real Product Manager API calls.
 */
class Feedback {
    use UtilsProvider;
    const ENDPOINT_DEACTIVATION_FEEDBACK = '1.0.0/deactivation-feedback';
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
     * @param string $reason
     * @param string $note
     * @param string $email
     */
    public function post($reason, $note, $email) {
        $initiator = $this->getPluginUpdate()->getInitiator();
        $product = $initiator->getProductAndVariant();
        return \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\ClientUtils::request(
            $initiator,
            self::ENDPOINT_DEACTIVATION_FEEDBACK,
            [
                'deactivationFeedback' => [
                    'product' => ['id' => $product[0]],
                    'productVariant' => ['id' => $product[1]],
                    'reason' => $reason,
                    'note' => $note,
                    'email' => $email,
                    'hasAnswerOptIn' => !empty($email),
                    'ip' => empty($email) ? null : $_SERVER['REMOTE_ADDR']
                ]
            ],
            'POST'
        );
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
        return new \DevOwl\RealCategoryLibrary\Vendor\DevOwl\RealProductManagerWpClient\client\Feedback($pluginUpdate);
    }
}
