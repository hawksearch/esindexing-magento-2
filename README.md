# Hawksearch ES Indexing module for Magento 2

## Installation Instructions
### Steps to install via composer
1. Update the “repositories” section of your sites “composer.json” file to include the repositories for the hawksearch modules:
    ```shell script
    composer config repositories.hawksearch-connector '{"type": "git", "url": "https://gitlab.idevdesign.net/magento2-modules/hawksearch-connector-2.git"}'
    composer config repositories.hawksearch-esindexing '{"type": "git", "url": "https://gitlab.idevdesign.net/magento2-modules/hawksearch-esindexing-2.git"}'
    ```
2. Install composer packages:
    ```shell script
    composer require hawksearch/connector hawksearch/esindexing --no-update
    composer update hawksearch/connector hawksearch/esindexing
    ```
3. Update Magento as logged in Magento filesystem owner:
    ```shell script
    bin/magento module:enable –clear-static-content HawkSearch_Connector HawkSearch_EsIndexing
    bin/magento setup:upgrade
    bin/magento cache:clean
    ```
4. Login to your Magento Dashboard and configure the modules with instructions provided by your Hawksearch account manager.


### Steps to install the HawkSearch modules via zip file
1. Open https://gitlab.idevdesign.net/magento2-modules/hawksearch-connector-2 and https://gitlab.idevdesign.net/magento2-modules/hawksearch-esindexing-2 in a browser.
2. On each page, click the “Download zip” button to download the zip files.
3. Create a directory named “HawkSearch” in the Magento “app/code” directory and unzip the downloaded files in that directory.
4. Rename the unzipped directories to “Connector” and “EsIndexing” respectively.
5. Ensure the files have appropriate file permissions for your installation (see http://devdocs.magento.com/guides/v2.1/install-gde/install-quick-ref.html for reference).
6. While logged in as the Magento filesystem owner, run the following commands in a command shell from your Magento 2 root installation directory
    ```shell script
    bin/magento module:enable –clear-static-content HawkSearch_Connector HawkSearch_EsIndexing
    bin/magento setup:upgrade
    bin/magento cache:clean
    ```
7. Login to your Magento Dashboard and configure the modules with instructions provided by your Hawksearch account manager.
