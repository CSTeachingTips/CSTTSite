<?php
/**
 * @file
 * search_api_fivestar.rules_defaults.inc
 */

/**
 * Implements hook_default_rules_configuration().
 */
function search_api_fivestar_default_rules_configuration() {
  $index = variable_get('search_api_fivestar_rating_index');
  $items = array();
  $items['rules_vote_is_cast'] = entity_import('rules_config', '{ "rules_vote_is_cast" : {
    "LABEL" : "Vote is cast",
    "ACTIVE" : true,
    "PLUGIN" : "reaction rule",
    "REQUIRES" : [ "search_api", "voting_rules" ],
    "ON" : [ "voting_rules_insert_node" ],
    "DO" : [
      { "search_api_index" : { "entity" : [ "node" ], "index" : "' . $index . '" } }
    ]
  }
}');
  return $items;
}
