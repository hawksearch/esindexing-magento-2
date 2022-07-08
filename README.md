# Hawksearch for Magento 2 (ElasticSearch based Version)

## Introduction
The Hawksearch service enables online retailers and publishers the ability to drive a rich, compelling user experience. 
This experience drives visitors to the products and information that they are seeking. 

Hawksearch provides the ability to power the product listing pages for categories and brand pages on the site 
in addition to driving the search page on the site.  

This extension is capable to replace the built-in Magento search with a feature reach, relevant and personalized 
search experience powered with HawkSearch ElasticSearch API v4.0+

## Support
You can submit a new ticket through our [Support Center](https://support.bridgeline.com/)
If you have any questions please read our [Support FAQ](https://hawksearch.atlassian.net/wiki/spaces/HSKB/pages/327719/Support%2BFAQ)

## Documentation

Check out our [Getting Started](https://hawksearch.atlassian.net/wiki/spaces/CON/pages/1626112046/Getting+Started+with+Hawksearch+ES) 
guide and start using [HawkSearch](https://www.hawksearch.com/ ) for Magento 2.

## Magento Version Support
2.4.x

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
3. Proceed with [Getting Started](https://hawksearch.atlassian.net/wiki/spaces/CON/pages/1626112046/Getting+Started+with+Hawksearch+ES) guide


## Contribution
Coming soon

## Customization
Coming soon
