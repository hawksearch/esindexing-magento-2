# Indexing

Hawksearch extension for Magento Open Source and Adobe Commerce takes products, cetegories and pages
content and transfers it to Hawksearch indices. This is what is called _Indexing_.

Extension does support native Magento indexers technique to maintain all your data relevant and up to date.
The data can be indexed with one of the following ways:

- Automatically with the help of [Message queues](MessageQueues.md)
- Manually from the command-line

All indexing operations are processed asynchronously with a [message queues](MessageQueues.md)

## Magento indexers

Hawksearch extension does provide the following indexers:

- `hawksearch_products` - Rebuild product items in the index
- `hawksearch_content_pages` - Rebuild content page items in the index
- `hawksearch_categories` - Rebuild category relations. The indexer controls the integrity of
  [Landing Pages](https://hawksearch.atlassian.net/wiki/spaces/HSKB/pages/1545896606/Using+Dashboard+API+to+Manage+Landing+Pages) 
  and [Hierarchy](https://hawksearch.atlassian.net/wiki/spaces/HSKB/pages/138608725/Hawksearch+v4.0+-+Hierarchy+API) entities.
- `hawksearch_entities` - Rebuild all Hawksearch entities index. The indexer is designed to perform full reindex action only.

All indexers above are shared. When you reindex a shared indexer, other indexers with the same `shared_index` name 
are labeled as Valid. The reason is in the nature of Hawksearch index which stores all indexed entities (products, pages)
in the same index.

### Invalidate indexers

Indexers invalidation is used to mark some indexers as "Invalid" what will trigger a full reindexing 
job for these indexers when the next cron is run. 
Hawksearch indexers are are marked as "Invalid" in the following cases:

- When "Root Category" or "Website" are changed in Store Group
- When "Store" value is chaged in Store View
- When product attribute is mapped to Hawksearch field and it is deleted


## Automatic indexing

Every change and deletion on products, categories and content pages triggers updates to Hawksearch index deltas 
and keeps data in the current production index up to date. 

## Manual indexing

There is an option to manually trigger full reindexing for all indexed entities using command-line:

```bash
php bin/magento indexer:reindex hawksearch_entities
```

Full reindexig of individual entity is forbidden. Foer example, if you'll try to run this command:

```bash
php bin/magento indexer:reindex hawksearch_products
```

you'll get a warning message:

>  To trigger full reindex please use `hawksearch_entities` indexer.

## Indexed Fields
The correct data indexing process requires some fields to be created in Hawksearch.  Please refer to the 
[Fields documentation](https://hawksearch.atlassian.net/wiki/spaces/HSKB/pages/327729) for more information about 
Fields Management. It requires [Default System Fields](#Default-system-fields) which are used to correctly identify 
documents in Hawksearch index as well as mandatory Products and Pages fields to be crated in Hawksearch Dashboard.

### Default system fields

The following system fields need to be created:

| Field Name   | Save As    | Description                                                          |
|:-------------|:-----------|:---------------------------------------------------------------------|
| `__uid`      | Text Value | This is the unique item identifier. For products `entity_id` is used |
| `__type`     | Text Value | The item type. It is one of product or content_page                  | 
| `category`   | Text Value | Hierarchical field                                                   |

> `__uid` field should be set as **Primary Key**

> `category` field should be set as **Is Hierarchical Field?**

## Product indexing

### Full reindexing

Product entities are reindexed as part of `hawksearch_entities` indexer. See [Manual indexing](#manual-indexing) for 
the reference.

### Indexed attributes

It is possible to configure which product attributes will be pushed to Hawksearch index. 
The attributes configuration can be done on Product Settings tab through `Stores > Configuration > HAWKSEARCH`.

### Default indexed attributes

The following attributes are pushed to Hawksearch index regardless of what is set in configuration.

> Make sure that fields are created in Hawksearch Workbench

| Field Name                                   | Save As       | Description                                                          |
|:---------------------------------------------|:--------------|:---------------------------------------------------------------------|
| `name`                                       | Text Value    | Product name |
| `url`                                        | Text Value    | Product URL                 | 
| `image_url`                                  | Text Value    | Product Image URL                                              |
| `thumbnail_url`                              | Text Value    | Product Thumbnail URL |
| `type_id`                                    | Text Value    | Magento product type                  | 
| `visibility`                                 | Text Value    | Product visibility                                                   |
| **Pricing Fields**                           | -------       | -----------                                                          | 
| price_regular                                | Numeric Value | Regular Product Price |
| price_final                                  | Numeric Value | Discounted Product Price                  | 
| price_regular_include_tax                    | Numeric Value | Regular Product Price Including tax                                                   |
| price_final_include_tax                      | Numeric Value | Discounted Product Price Including tax |
| price_regular_formatted                      | Text Value    | Regular Product Price including currency sign                  | 
| price_final_formatted                        | Text Value    | Discounted Product Price including currency sign                                                   |
| price_regular_include_tax_formatted          | Text Value    | Regular Product Price including tax and currency sign |
| price_final_include_tax_formatted            | Text Value    | Discounted Product Price including tax and currency sign                  | 
| price_group_<group_id>                       | Numeric Value | Discounted Product Price per Customer Group                                                  |
| price_group_<group_id>_include_tax           | Numeric Value | Discounted Product Price  per Customer Group Including tax                                              |
| price_group_<group_id>_formatted             | Text Value    | Discounted Product Price per Customer Group including  currency sign                                              |
| price_group_<group_id>_include_tax_formatted | Text Value    | Discounted Product Price per Customer Group including tax and currency sign                                                   |
| price_min                                    | Numeric Value | Minimal price for configurable, bundle and grouped products                                                  |
| price_max                                    | Numeric Value | Maximal price for configurable, bundle and grouped products                                                  |

Pricing Fields per Customer groups will be pushed for each Customer group created in the Magento. 
You can review the full list of customer groups in Magento on the page `Customers > Customer Groups` and replace 
`<group_id>` in Field name with `ID` from the list.

For example, if your store has the following groups (see image above) then these price attributes will be 
pushed to HawkSearch index:

* price_group_0
* price_group_0_include_tax
* price_group_0_formatted
* price_group_0_include_tax_formatted
* price_group_1
* price_group_1_include_tax
* price_group_1_formatted
* price_group_1_include_tax_formatted
* price_group_2
* price_group_2_include_tax
* price_group_2_formatted
* price_group_2_include_tax_formatted
* price_group_3
* price_group_3_include_tax
* price_group_3_formatted
* price_group_3_include_tax_formatted

## Content page indexing

### Full reindexing

Content page entities are reindexed as part of `hawksearch_entities` indexer. See [Manual indexing](#manual-indexing) for
the reference.

### Indexed attributes

> Make sure that fields are created in Hawksearch Workbench

| Field Name        | Save As    | Description     |
|:------------------|:-----------|:----------------|
| `title`           | Text Value | Page Title      |
| `content_heading` | Text Value | Content Heading | 
| `content`         | Text Value | Page Content    |
