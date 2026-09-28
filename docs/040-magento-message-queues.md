---
title: Message Queues
slug: magento-message-queues
order: 40
parentDocSlug: magento
categorySlug: connectors
---


To be able to start searching on the Magento store the data should be uploaded to Hawksearch Index. The indexing process works asynchronously in background and doesn't require Magento administrator to wait for the operation to be completed.

After triggering the re-indexing CLI command

```shell
bin/magento indexer:reindex hawksearch_entities
```
a new bulk of async operations will be created. Each operation is linked with a queue message which is processed by the consumer `hawksearch.indexing`. Consumers can be started manually or scheduled by Cron. See [Manage message queues](https://experienceleague.adobe.com/docs/commerce-operations/configuration-guide/message-queues/manage-message-queues.html?lang=en) article for more details.

#### Queue Consumers

**Version 0.7.x and earlier:**
- `hawksearch.indexing` - Processes all indexing operations

**Version 0.8.0+:**
- `hawksearch.indexing` - Processes catalog data and stores in middleware table
- `hawksearch.indexing.items` - Publishes items from middleware table to HawkSearch API

To start consumers manually:

```shell
# Version 0.7.x
bin/magento queue:consumers:start hawksearch.indexing

# Version 0.8.0+
bin/magento queue:consumers:start hawksearch.indexing
bin/magento queue:consumers:start hawksearch.indexing.items
```

## Processing Bulk Operations

The list of operation topics needed for full re-indexing of search data is provided below.

### Version 0.7.x and Earlier

> ⚠️ The topics below are relevant for version 0.7.x and earlier
>
> In version 0.8.0, the indexing architecture was improved with a middleware table for better performance. See the updated topics for version 0.8.0 below.

* `hawksearch.indexing.fullreindex.start` - is used to markup that full re-indexing job is started
* `hawksearch.indexing.hierarchy.reindex` - is used for rebuilding Hierarchies (Magento categories)
* `hawksearch.indexing.landing_page.reindex` - is used for rebuilding Landing Pages (Magento categories)
* `hawksearch.indexing.catalog.reindex` - is used for indexing catalog products
* `hawksearch.indexing.content_page.reindex` - is used for indexing content pages

### Version 0.8.0+

> 📘 New in version 0.8.0
>
> Starting from version 0.8.0, the indexing process uses a two-queue architecture with a middleware table for improved performance. The system now separates data processing from API communication.

**Performance Improvement:** Version 0.8.0 introduces a middleware table that significantly improves indexing performance by:
- Loading larger batches of items from the database (reducing database queries)
- Storing processed data in a middleware table
- Publishing items to HawkSearch API in optimal batch sizes (125 items max per API request)

#### Two-Queue Architecture

The indexing process now uses two separate message queues:

**First Queue Topics** (Data Processing):
* `hawksearch.indexing.fullreindex.start` - Marks that full re-indexing job is started
* `hawksearch.indexing.hierarchy.reindex` - Rebuilds Hierarchies (Magento categories)
* `hawksearch.indexing.landing_page.reindex` - Rebuilds Landing Pages (Magento categories)
* `hawksearch.indexing.catalog.reindex` - Processes catalog products and stores them in middleware table
* `hawksearch.indexing.content_page.reindex` - Processes content pages and stores them in middleware table

**Second Queue Topics** (API Publishing):
* `hawksearch.indexing.items.publish` - Transfers processed items from middleware table to HawkSearch API

#### How It Works

1. **First Queue Consumer** (`hawksearch.indexing`):
   - Loads items from Magento database in configurable batch sizes
   - Processes attributes, prices, and other data
   - Stores encoded JSON objects in the middleware table
   - Publishes messages to the second queue

2. **Middleware Table**:
   - Stores processed index items ready to be pushed to HawkSearch API
   - Provides retry capability for failed API requests
   - Enables better tracking of indexing progress

3. **Second Queue Consumer** (`hawksearch.indexing.items`):
   - Reads items from the middleware table
   - Transfers data to HawkSearch Indexing API in batches of up to 125 items
   - Updates item status after successful/failed API calls

On each particular store the list of topics and number of operations can be different depending on store configurations.
For example, some stores do not require indexing of content pages, so related operations will not be scheduled.

The consumer processes operations asynchronously. The bulk is considered as complete in case when all operations are finished with status `1` = **Complete**.  There are many reasons why operation can be incomplete: network errors, server errors, connection delays, queue consumers configurations, failed response from Hawksearch API, etc. If indexing process was not completed it can be troubleshooted with the help of [bulk operations status REST endpoints](#rest-api-endpoints-used-for-tracking-operations-status) or in [Admin UI](#use-admin-ui-for-tracking-operations-status).

When the last operation is completed the temporary index is swapped with the production one. The full re-indexing process is finished.

### REST API endpoints used for tracking operations status

To check the status of bulk operations use one of [REST endpoints](https://developer.adobe.com/commerce/webapi/rest/use-rest/operation-status-endpoints/):

```shell
GET /V1/bulk/:bulkUuid/status
GET /V1/bulk/:bulkUuid/operation-status/:status
GET /V1/bulk/:bulkUuid/detailed-status
```

Using the following [example](https://developer.adobe.com/commerce/webapi/rest/use-rest/operation-status-search/) we can compose a cURL request to find last scheduled full re-indexing bulks:

```shell
curl --location -g --request GET 'https://magento-domain.com/rest/V1/bulk/?searchCriteria[filterGroups][0][filters][0][field]=topic_name&searchCriteria[filterGroups][0][filters][0][value]=hawksearch.indexing.fullreindex.start&searchCriteria[sortOrders][0][field]=start_time&searchCriteria[sortOrders][0][direction]=DESC&searchCriteria[pageSize]=1' \
--header 'Authorization: Bearer <API_KEY>' \
--data-raw ''
```

### Use Admin UI for tracking operations status

A list of Hawksearch bulks is accessible from Menu **Stores > Hawksearch > Indexing Bulks**. The list contains only bulks which are related to Hawksearch indexing.

The bulk is considered as **Hawksearch indexing bulk** if and only if `topic_name` of all operations inside the bulk is started with `hawksearch.indexing.` string.

The Retry button on the Bulk Details Page changes operations statuses _Failed Retriably_ and _Failed Not Retriably_ to _Not Started_.

### Retry Bulk Operations

In case when any error occurred the indexing process would not be finished. After finding and fixing the problem failed bulk can be retried. The **Retry** action button results in all affected operations to be re-added back to queue. The operations status is changed to `4` = **Not Started**.

There are two ways how you are able to retry bulks:

* Using **Retry** action button in Admin UI. It is restricted to retry Bulks only with failed status. 
* Using CLI command 

```shell
bin/magento hawksearch:retry-bulk <bulk-uuid> [<statuses>...]
```
