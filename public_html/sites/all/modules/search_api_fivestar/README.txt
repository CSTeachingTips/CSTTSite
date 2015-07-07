Solr based search won't index five star rating field ratings corrently. This module aims to fix it. You can use fivestar ratings for facets with this module.

Currently it provides the following functionality:
- Makes Search API indexes to index rating values correctly according to settings.
- Updates votes in realtime to selected indexes after vote is cast
- Provides 3 new fields to index

Limitations:
- Currently works only on entities that have single rating field.

Installation:
By default it works on 5 star rating fields on all indexes. You can limit instant indexing to certain indexes from options page. If you use different amount of stars than 5, you can choose between 1-10. Notice: This applies to all ratings currently.

TODO:

Find out how to set different rating fields stars numbers
Handling of multiple rating fields per entity
Provide facets with styling
This is a D7 module.

Dependencies:
- Voting Rules
- Search API
- Fivestar
