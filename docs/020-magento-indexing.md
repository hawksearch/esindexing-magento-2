---
title: Indexing
slug: magento-indexing
order: 20
parentDocSlug: magento
categorySlug: connectors
---

Hawksearch Extension for Magento Open Source and Adobe Commerce transfers product, category and content pages data to Hawksearch indices. This process is called _Indexing_.

Extension does support native <<glossary:Magento>> indexers technique to maintain all site data relevant and up to date. The data can be indexed in [automatic][^1] mode or [manually][^2] from the command-line.

Regardless of the indexing mode selected, the final indexing operation processing occurs asynchronously with the help of [Message Queues][^3] mechanism.

## Indexing Settings

![Indexing Settings](https://files.readme.io/05871c0-image-20210204-112701.png "Indexing Settings")

The **Maximum number of items indexed per iteration** setting represents the API limit which has a maximum value of 125 items. Please contact [<<hawksearchSupportContactName>>][^11] if you have any questions or need to extend the limit.


## Magento indexers

Hawksearch Extension does provide the following indexers:

- `hawksearch_products` - Rebuild product items in the index
- `hawksearch_content_pages` - Rebuild content page items in the index
- `hawksearch_categories` - Rebuild category relations. The indexer controls the integrity of
  [Landing Pages][^4] and [Hierarchy][^5] entities.
- `hawksearch_entities` - Rebuild all Hawksearch entities index. The indexer is designed to perform full re-index action only.

All indexers above are shared. When you re-index a shared indexer, other indexers with the same `shared_index` name are labeled as Valid. The reason is in the nature of Hawksearch index which stores all indexed entities (products, pages) in the same physical index.

### Invalidate indexers

Indexers invalidation is used to mark some indexers as "Invalid" what will trigger a full re-indexing job for these indexers when the next cron is run. Hawksearch indexers are marked as "Invalid" in the following cases:

- When "Root Category" or "Website" are changed for Store Group;
- When "Store" value is changed for Store View;
- When product attribute is deleted (in case when it is linked to a Hawksearch field).


## Automatic indexing

Every change and deletion in products, categories and content pages triggers updates in Hawksearch index deltas. So that, it keeps data in the current production index up-to-date.

In case when any of hawksearch indexers is invalidated the full re-indexing process will be triggered by cron automatically.

## Manual indexing

The procedure of manual indexing helps to monitor things step-by-step and [troubleshoot][^6] any issues related to indexing procedure. To manually trigger full re-indexing for all entities use command in CLI:

```shell
php bin/magento indexer:reindex hawksearch_entities
```

Full re-indexing of individual entity is forbidden. For example, if you'll try to run this command:

```shell
php bin/magento indexer:reindex hawksearch_products
```

you'll get a warning message:

>  To trigger full re-index please use `hawksearch_entities` indexer.

## Indexed fields

The correct data indexing process requires a proper [fields configuration][^7] in Hawksearch Workbench.
Some [default system fields][^8], [product][^9] and [content page][^10] fields should be set up in Hawksearch Workbench. These fields are used to correctly identify documents in HawkSearch index.

### Default system fields

The following system fields need to be created:

| Field Name   | Save As    | Description                                                          |
|:-------------|:-----------|:---------------------------------------------------------------------|
| `__uid`      | Text Value | This is the unique item identifier. For products `entity_id` is used |
| `__type`     | Text Value | The item type, for example `product` or `content_page`               |

> 📘 
> `__uid` field should be set as **Primary Key**

## Product indexing

Product entities are re-indexed as part of `hawksearch_products` and `hawksearch_entities` indexers. See [Manual indexing][^2] for the reference.

### Fields & attributes

You are allowed to configure which product attributes should be mapped to HawkSearch fields and pushed to the index. The attributes configuration can be found on **Stores > Configuration > Hawksearch > Products** tab.  
HawkSearch Field properties are synchronized with Magento Attribute properties 'Is Filterable', 'Is Searchable' and ‘Used for Sorting in Product Listing’ every time configuration setting is saved.  
Field properties are NOT synchronized with Magento attribute properties when attribute is chganged in **Stores > Attributes > Product** section.

#### Default product fields

The following fields below are pushed to HawkSearch index regardless of what is set in **Products** configuration setting.

> 📘
> Make sure fields are created in Hawksearch Workbench

| Field Name                                   | Save As       | Description                                                                 |
|:---------------------------------------------|:--------------|:----------------------------------------------------------------------------|
| `name`                                       | Text Value    | Product name                                                                |
| `url`                                        | Text Value    | Product URL                                                                 |
| `image_url`                                  | Text Value    | Product Image URL                                                           |
| `thumbnail_url`                              | Text Value    | Product Thumbnail URL                                                       |
| `type_id`                                    | Text Value    | Magento product type                                                        |
| `visibility_search`                          | Boolean Value | Visibility in the search                                                    |
| `visibility_catalog`                         | Boolean Value | Visibility in the catalog                                                   |
| `category`                                   | Text Value    | Hierarchical field                                                          |
| **Pricing Fields**                           |               |                                                                             |
| price_regular                                | Numeric Value | Regular Product Price                                                       |
| price_final                                  | Numeric Value | Discounted Product Price                                                    |
| price_regular_include_tax                    | Numeric Value | Regular Product Price Including tax                                         |
| price_final_include_tax                      | Numeric Value | Discounted Product Price Including tax                                      |
| price_regular_formatted                      | Text Value    | Regular Product Price including currency sign                               |
| price_final_formatted                        | Text Value    | Discounted Product Price including currency sign                            |
| price_regular_include_tax_formatted          | Text Value    | Regular Product Price including tax and currency sign                       |
| price_final_include_tax_formatted            | Text Value    | Discounted Product Price including tax and currency sign                    |
| price_group_<group_id>                       | Numeric Value | Discounted Product Price per Customer Group                                 |
| price_group_<group_id>_include_tax           | Numeric Value | Discounted Product Price  per Customer Group Including tax                  |
| price_group_<group_id>_formatted             | Text Value    | Discounted Product Price per Customer Group including  currency sign        |
| price_group_<group_id>_include_tax_formatted | Text Value    | Discounted Product Price per Customer Group including tax and currency sign |
| price_min                                    | Numeric Value | Minimal price for configurable, bundle and grouped products                 |
| price_max                                    | Numeric Value | Maximal price for configurable, bundle and grouped products                 |

> 📘 
> `category` field should be set as **Is Hierarchical Field?**

> 📘 
> Before connector version 0.7.0 `visibility` field was used instead of two separate fields `visibility_search` and `visibility_catalog`

Pricing Fields per Customer groups will be pushed for each Customer group created in <<glossary:Magento>>. The full list of customer groups can be obtained on the page `Customers > Customer Groups`. Then replace `<group_id>` in Field name with customer group `ID` from the list.

<figure style="text-align: center;">
    <img src="https://files.readme.io/6ca8208-image.png"
         alt="Customer groups"
         title="Customer groups">
    <figcaption>Customer groups</figcaption>
</figure>

For example, if some store has the following groups (see image above) then the following price attributes will be pushed to Hawksearch index:

| Field Name                          |
| ----------------------------------- |
| price_group_0                       |
| price_group_0_include_tax           |
| price_group_0_formatted             |
| price_group_0_include_tax_formatted |
| price_group_1                       |
| price_group_1_include_tax           |
| price_group_1_formatted             |
| price_group_1_include_tax_formatted |
| price_group_2                       |
| price_group_2_include_tax           |
| price_group_2_formatted             |
| price_group_2_include_tax_formatted |
| price_group_3                       |
| price_group_3_include_tax           |
| price_group_3_formatted             |
| price_group_3_include_tax_formatted |

## Content page indexing

Content page entities are re-indexed as part of `hawksearch_content_pages` and `hawksearch_entities` indexers. See [Manual indexing][^2] for the reference.

### Content page fields

> 📘 
> Make sure fields are created in Hawksearch Workbench

| Field Name        | Save As    | Description     |
|:------------------|:-----------|:----------------|
| `title`           | Text Value | Page Title      |
| `content_heading` | Text Value | Content Heading |
| `content`         | Text Value | Page Content    |


## Category indexing

> ❗️  Category indexing will be supported in the future extension versions

Category entities (Landing Pages and Hierarchies) are re-indexed as part of `hawksearch_categories` and `hawksearch_entities` indexers. See [Manual indexing][^2] for the reference.

TEST!!!


[^1]: #automatic-indexing
[^2]: #manual-indexing
[^3]: doc:magento-message-queues
[^4]: doc:managing-landing-pages
[^5]: doc:hierarchy-api-examples
[^6]: doc:magento-troubleshoot-indexing-process
[^7]: https://userguide.hawksearch.com/docs/create-configure-fields
[^8]: #default-system-fields
[^9]: #default-product-fields
[^10]: #content-page-fields
[^11]: mailto:<<hawksearchSupportContactEmail>>
