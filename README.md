# Hawksearch for Magento Open Source and Adobe Commerce (Elastic based Version)

## Introduction
The Hawksearch service enables online retailers and publishers the ability to drive a rich, compelling user experience. 
This experience drives visitors to the products and information that they are seeking. 

Hawksearch provides the ability to power the product listing pages for categories and brand pages on the site 
in addition to driving the search page on the site.  

This extension is capable to replace the built-in Magento search with a feature reach, relevant and personalized 
search experience powered with HawkSearch ElasticSearch API v4.0+

## Support
You can submit a new ticket through our [Support Center](https://support.bridgeline.com/)
If you have any questions please read our [Support FAQ](https://userguide.hawksearch.com/docs/support-fq)

## Documentation

Check out our [Getting Started](https://developerdocs.hawksearch.com/docs/magento-getting-started) guide and start using [HawkSearch](https://www.hawksearch.com/ ) for Magento 2.

## Platform Version Support

* Magento Open Source: **2.4.4 - 2.4.8**
* Adobe Commerce: **2.4.4 - 2.4.8**
* PHP: **8.1 - 8.4**

## Installation Instructions

1. Install composer packages

```shell
composer require hawksearch/esindexing-magento-2 --no-update
composer update hawksearch/esindexing-magento-2
```
2. Setup the extension in Magento

```shell
bin/magento module:enable --clear-static-content HawkSearch_Connector HawkSearch_EsIndexing
bin/magento setup:upgrade
bin/magento cache:flush
```
3. Proceed with [Getting Started](https://developerdocs.hawksearch.com/docs/magento-getting-started) guide

## Contribution
Coming soon

## Customization
Coming soon

## Visitor address in Vue widgets

The storefront loads the visitor's current remote address from the
`hawksearch-private-vue-config` customer-data section before creating Vue widgets.
The address is passed to the SDK as `clientRemoteAddress`; it is not embedded in
the full-page-cached HTML. Pages with widgets make one private section request.
The section's `config` object is merged into each widget's configuration;
private properties take precedence at the top level, including replacing nested
objects. Magento section metadata is not passed to the SDK.
If that request fails, an error is logged and widgets initialize without the
private address. The SDK sends this address in the `X-Forwarded-For` header, so
cross-origin Hawksearch endpoints must allow that header in their CORS policy.

## Customizing Vue widgets

Widget initialization waits for the private section request, so DOM ready or a
RequireJS dependency on `hawksearchCommon` does not mean a widget is ready.
Use `hawksearch.whenVueWidgetsReady()` and obtain the widget in its `done`
callback. It returns a jQuery promise and also works for scripts loaded after
initialization:

```js
define(['jquery', 'underscore', 'hawksearchCommon'], function ($, _) {
    'use strict';

    hawksearch.whenVueWidgetsReady().done(function () {
        const widget = hawksearch.getVueWidget('searchbox');
        if (_.isEmpty(widget)) {
            return; // This page has no successfully initialized searchbox.
        }

        HawksearchVue.getWidgetStore(widget).subscribeAction({
            after: function (action, state) {
                if (action.type === 'fetchSuggestions') {
                    $(widget.$el).trigger('contentUpdated');
                    // Customize state.suggestions here.
                }
            }
        });
    }).fail(function (error) {
        console.error('Unable to initialize the Hawksearch customization.', error);
    });
});
```

The promise resolves after widget creation attempts and the connector's action
subscriptions, including when there are no widgets or the private request fails
and initialization falls back to public configuration. Check for the specific
widget: a creation error is logged and other widgets can still initialize.
The promise rejects if global configuration is missing or initialization cannot
finish. It does not replay actions dispatched during widget creation; register
`createWidget:before` through `hawksearchVueEvents` before initialization when
you need to modify a widget's configuration before it is mounted.
