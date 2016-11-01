<?php

/**
 * Sentry plugin for Craft CMS.
 *
 * Craft integration with error monitoring service Sentry.
 *
 * @author    Joshua Baker
 * @copyright Copyright (c) 2016 Joshua Baker
 *
 * @link      https://joshuabaker.com/
 * @since     0.1.0
 */
namespace Craft;

use Raven_Client;

class SentryPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        require_once __DIR__.'/vendor/autoload.php';

        craft()->sentry->register();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return Craft::t('Sentry');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Craft integration with error monitoring service Sentry.');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/joshuabaker/craft-sentry/blob/master/README.md';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/joshuabaker/craft-sentry/master/releases.json';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '0.1.0';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'Joshua Baker';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://joshuabaker.com/';
    }
}
