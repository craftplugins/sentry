> [!IMPORTANT]
>
> **This plugin is no longer maintained.**
>
> We recommend the [Sentry Logger plugin](https://plugins.craftcms.com/sentry-logger) instead.

# Sentry plugin for Craft CMS

Craft integration with error monitoring service Sentry.

## Installation

To install Sentry, follow these steps:

1. Download & unzip the file and place the `sentry` directory into your `craft/plugins` directory
2. Install plugin in the Craft Control Panel under Settings > Plugins

Sentry works on Craft 2.4.x and Craft 2.5.x.

## Configuring Sentry

You’ll need to create a `sentry.php` file in your `craft/config` directory and set your server access token via the `accessToken` configuration item.

```php
// craft/config/sentry.php

return [

    'dsn' => 'https://<key>:<secret>@sentry.io/<project>',
    'publicDsn' => 'https://<key>@sentry.io/<project>',

];
```
