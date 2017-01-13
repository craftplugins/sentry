<?php

namespace Craft;

return [

    /**
     * The DSN.
     *
     * @see https://docs.sentry.io/clients/php/usage/#basics
     *
     * @param string
     */
    'dsn' => null,

    /**
     * The public DSN.
     *
     * @see https://docs.sentry.io/clients/php/usage/#basics
     *
     * @param string
     */
    'publicDsn' => null,

    /**
     * Callback run before sending.
     *
     * @see https://docs.sentry.io/clients/php/usage/#filtering-out-errors
     *
     * @param callable
     */
    'sendCallback' => null,

    /**
     * The URL to use for the JavaScript client.
     *
     * @param string
     */
    'ravenJsUrl' => 'https://cdn.ravenjs.com/3.7.0/raven.min.js',

    /**
     * Whether or not to include Craft logs.
     *
     * @param bool
     */
    'includeCraftLogs' => false,

    /**
     * Whether or not to include plugin logs.
     *
     * @param bool
     */
    'includePluginLogs' => false,

    /**
     * Fingerprints to use for exceptions.
     *
     * Set keys to exception classes and values fingerprint values.
     *
     * @param array
     */
    'exceptionFingerprints' => [
        HttpException::class => [
            UrlHelper::getUrl(craft()->request->getPath())
        ],
    ],

];
