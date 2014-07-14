<?php

/**
 * Implement hook_views_data_alter().
 */
function views_tpo_views_data_alter(&$data) {
  $data['term_data']['name']['argument']['handler'] = 'views_tpo_handler_taxonomy_term_name';
}

/**
 * Implement hook_views_handlers().
 */
function views_tpo_views_handlers() {
  return array(
    'info' => array(
      'path' => drupal_get_path('module', 'views_tpo') . '/includes',
    ),
    'handlers' => array(
      'views_tpo_handler_taxonomy_term_name' => array(
        'parent' => 'views_handler_argument_string',
      ),
    )
  );
}
