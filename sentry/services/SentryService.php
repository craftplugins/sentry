<?php

/**
 * Sentry plugin for Craft CMS.
 *
 * Sentry Service
 *
 * @author    Joshua Baker
 * @copyright Copyright (c) 2016 Joshua Baker
 *
 * @link      https://joshuabaker.com/
 * @since     0.1.0
 */
namespace Craft;

use Exception;
use Raven_Client;

class SentryService extends BaseApplicationComponent
{
    /**
     * The Sentry client.
     *
     * @var \Raven_Client
     */
    protected $sentryClient;

    /**
     * Registers the back-end and front-end clients based on configuration.
     */
    public function register()
    {
        $dsn = craft()->config->get('dsn', 'sentry');
        $publicDsn = craft()->config->get('publicDsn', 'sentry');
        $ravenJsUrl = craft()->config->get('ravenJsUrl', 'sentry');

        if ($dsn && is_null($this->sentryClient)) {
            // Create a back-end client
            $this->sentryClient = new Raven_Client($dsn);

            // Set user context
            $currentUser = craft()->userSession->getUser();
            if ($currentUser) {
                $this->sentryClient->set_user_data($currentUser->id, $currentUser->email);
            }

            // Register callback for filtering errors
            $this->sentryClient->setSendCallback(function($data) {
                return $this->sendCallback($data);
            });

            // Capture Craft exceptions
            craft()->onException->add(function ($event) {
                $this->captureException($event->exception);
            });

            // Capture Craft errors
            craft()->onError->add(function ($event) {
                $this->captureMessage($event->message);
            });

            // Capture Craft logs
            $logger = Craft::getLogger();
            $logger->attachEventHandler('onFlush', function($event) use ($logger) {
                foreach ($logger->getLogs() as $log) {
                    $this->captureLog($log);
                }
            });
        }

        if ($publicDsn && $ravenJsUrl) {
            // Enable the front-end client
            craft()->templates->includeJsFile($ravenJsUrl);
            craft()->templates->includeJs("Raven.config('{$publicDsn}').install();");

            // Set user context
            $currentUser = craft()->userSession->getUser();
            if ($currentUser) {
                $userJson = json_encode([
                    'email' => $currentUser->email,
                    'id' => $currentUser->id,
                ]);

                craft()->templates->includeJs("Raven.setUserContext({$userJson});");
            }
        }
    }

    /**
     * Returns a Sentry client.
     *
     * @return \Raven_Client
     */
    public function getClient()
    {
        $this->register();

        return $this->sentryClient;
    }

    /**
     * Captures exceptions.
     *
     * @see https://docs.sentry.io/clients/php/usage/#reporting-exceptions
     *
     * @param Exception $exception
     * @param array $options
     */
    public function captureException(Exception $exception, array $options = [])
    {
        if ($client = $this->getClient()) {
            return $client->captureException($exception, $options);
        }
    }

    /**
     * Captures error messages.
     *
     * @see https://docs.sentry.io/clients/php/usage/#reporting-other-errors
     *
     * @param string $message
     * @param array $options
     */
    public function captureMessage(string $message, array $options = [])
    {
        if ($client = $this->getClient()) {
            return $client->captureMessage($message, $options);
        }
    }

    /**
     * Pass a Craft log to Rollbar.
     *
     * @see CLogger::getLogs();
     *
     * @param mixed $log
     */
    public function captureLog($log)
    {
        $message = $log[0];
        $level = $log[1];
        $category = $log[2];
        $timestamp = $log[3];
        $plugin = $log[5];

        $includeCraftLogs = craft()->config->get('includeCraftLogs', 'rollbar');
        $includePluginLogs = craft()->config->get('includePluginLogs', 'rollbar');

        if ($category == 'application' && !$includeCraftLogs) {
            // Don’t log Craft
            return;
        }

        if ($category == 'plugin' && !$includePluginLogs) {
            // Don’t log plugins
            return;
        }

        if (is_array($includePluginLogs) && !in_array($plugin, $includePluginLogs)) {
            // Don’t log this plugin
            return;
        }

        return $this->captureMessage($message, [
            'extra' => [
                'category' => $category,
                'plugin' => $plugin,
            ],
            'level' => $level,
        ]);
    }

    /**
     * Send callback for filtering out errors.
     *
     * @see https://docs.sentry.io/clients/php/usage/#filtering-out-errors
     *
     * @param array $data
     * @return null|bool
     */
    protected function sendCallback($data)
    {
        $sendCallback = craft()->config->get('sendCallback', 'sentry');

        if ($sendCallback) {
            return $sendCallback($data);
        }
    }
}
