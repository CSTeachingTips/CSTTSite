<?php

/**
 * @file
 * Contains \Drupal\ds_search\Plugin\DsField\SearchSnippet.
 */

namespace Drupal\ds_search\Plugin\DsField;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Plugin that prints the search snippet
 *
 * @DsField(
 *   id = "search_snippet",
 *   title = @Translation("Search snippet"),
 *   entity_type = "node"
 * )
 */
class SearchSnippet extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->entity();

    if (!empty($node->snippet)) {
      return array(
        '#markup' => $node->snippet
      );
    }

    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowed() {
    $search_view_mode = \Drupal::config('ds_search.settings')->get('view_mode');
    return $this->viewMode() == $search_view_mode;
  }

}
