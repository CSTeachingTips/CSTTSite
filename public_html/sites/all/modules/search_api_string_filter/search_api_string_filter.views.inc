<?php
/**
 * Implements hook_views_data_alter().
 */
function search_api_string_filter_views_data_alter(&$data) {
  $entity_types = entity_get_info();
  foreach (search_api_index_load_multiple(FALSE) as $index) {
    // Fill in base data.
    $key = 'search_api_index_' . $index->machine_name;
    $table = &$data[$key];
    foreach ($table as $id => $field) {
      if (isset($field['type'])) {
        $inner_type = search_api_extract_inner_type($field['type']);
        if ($inner_type == 'string') {
          $table[$id]['filter']['handler'] = 'SearchApiStringFilter';
        }
      }
    }
  }
}
