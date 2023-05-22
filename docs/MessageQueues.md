# Message queues

To be able to start searching on the Magento store the data should be uploaded to Hawksearch Index.
The indexing process works asynchronousely in background and doesn't require Magento administartor to wasit for the 
operation to be completed.

After triggering the re-indexing CLI command

```shell
bin/magento indexer:reindex hawksearch_entities
```
a new bulk of async operations is created. Each operation is linked with a queue message which is processed
by the consumer `hawksearch.indexing`. Consumers can be started manually or scheduled by Cron. See [Manage message queues](https://experienceleague.adobe.com/docs/commerce-operations/configuration-guide/message-queues/manage-message-queues.html?lang=en)
article for more details.

## Processing Bulk Operations

The list of operation topics needed for full re-indexing of search data is provided below.

* `hawksearch.indexing.fullreindex.start` - is used to markup that full re-indexing job is started
* `hawksearch.indexing.hierarchy.reindex` - is used for rebuilding Hierarchies (Magento categories)
* `hawksearch.indexing.landing_page.reindex` - is used for rebuilding Landing Pages (Magento categories)
* `hawksearch.indexing.catalog.reindex` - is used for indexing catalog products
* `hawksearch.indexing.content_page.reindex` - is used for indexing content pages

On each particular store the list of topics and number of operations can be different depending on store configurations.
For example, some stores do not require indexing of content pages, so related operations will not be scheduled.

The consumer processes operations asynchronousely. The bulk is considered as complete in case when all 
operations are finished with status `1` = **Complete**.  There are many reasons why operation can be incomplete: 
network errors, server errors, connection delays, queue consumers configurations, failed response from Hawksearch API, etc. 
If indexing process wasn’t completed it can be troubleshooted with the help of 
[bulk operations status REST endpoints](#rest-api-endpoints-used-for-tracking-operations-status) 
or in [Admin UI](#use-admin-ui-for-tracking-operations-status).

When the last operation is completed the temporary index is swapped with the production one. The full reindexing 
process is finished.

### REST API endpoints used for tracking operations status

To check the status of bulk operations use one of [REST endpoints](https://developer.adobe.com/commerce/webapi/rest/use-rest/operation-status-endpoints/):

```shell
GET /V1/bulk/:bulkUuid/status
GET /V1/bulk/:bulkUuid/operation-status/:status
GET /V1/bulk/:bulkUuid/detailed-status
```

Using the following [example](https://developer.adobe.com/commerce/webapi/rest/use-rest/operation-status-search/)
we can compose a cURL request to find last scheduled full re-indexing bulks:

```shell
curl --location -g --request GET 'https://magento-domain.com/rest/V1/bulk/?searchCriteria[filterGroups][0][filters][0][field]=topic_name&searchCriteria[filterGroups][0][filters][0][value]=hawksearch.indexing.fullreindex.start&searchCriteria[sortOrders][0][field]=start_time&searchCriteria[sortOrders][0][direction]=DESC&searchCriteria[pageSize]=1' \
--header 'Authorization: Bearer <API_KEY>' \
--data-raw ''
```

### Use Admin UI for tracking operations status

A list of Hawksearch bulks is accessible from Menu `Stores > Hawksearch > Indexing Bulks`. The list contains only 
bulks which are related to Hawksearch indexing.

The bulk is considered as **Hawksearch indexing bulk** if and only if `topic_name` of all operations inside the bulk 
is started with `hawksearch.indexing.` string.

The Retry button on the Bulk Details Page changes operations statuses _Failed Retriably_ and _Failed Not Retriably_ 
to _Not Started_.

### Retry Bulk Operations

In case when any error occured the indexing process would not be finished. After finding and fixing the problem failed bulk can be retried. The **Retry** action results in all affected operations to be re-added back to queue. The operations status is changed to `4` = **Not Started**.

There are two ways how you are able to retry bulks:

* Using **Retry** button in Admin UI. It is restricted to retry Bulks only with failed status. 
* Using CLI command 
```shell
bin/magento hawksearch:retry-bulk <bulk-uuid> [<statuses>...]
```

