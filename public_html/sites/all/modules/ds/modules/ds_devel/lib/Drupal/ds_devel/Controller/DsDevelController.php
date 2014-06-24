<?php

/**
 * @file
 * Contains \Drupal\ds_devel\Controller\DsDevelController.
 */

namespace Drupal\ds_devel\Controller;

use Drupal\Component\Utility\String;

/**
 * Returns responses for Views UI routes.
 */
class DsDevelController {

  /**
   * Lists all instances of fields on any views.
   *
   * @return array
   *   The Views fields report page.
   */
  public function nodeMarkup($node, $key = 'default') {
    $node = node_load($node);

    $build = entity_view($node, $key);
    $markup = drupal_render($build);

    $links = array();
    $links[] = l('Default', 'node/' . $node->id() . '/devel/markup/');
    $view_modes = entity_get_view_modes('node');
    foreach ($view_modes as $id => $info) {
      if (!empty($info['status'])) {
        $links[] = l($info['label'], 'node/' . $node->id() . '/devel/markup/' . $id);
      }
    }

    return array(
      '#markup' => '<div>' . implode(' - ', $links) . '</div><hr/><code><pre>' . String::checkPlain($markup) . '</pre></code>'
    );
  }

}
