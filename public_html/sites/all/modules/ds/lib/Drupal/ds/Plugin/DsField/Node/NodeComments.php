<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\Node\NodeComments.
 */

namespace Drupal\ds\Plugin\DsField\Node;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Plugin that renders the comments of a node.
 *
 * @DsField(
 *   id = "node_comments",
 *   title = @Translation("Comments"),
 *   entity_type = "node",
 *   provider = "node"
 * )
 */
class NodeComments extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function isAllowed() {
    if (in_array($this->viewMode(), array('full', 'default'))) {
      return TRUE;
    }

    return FALSE;
  }

}
