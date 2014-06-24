<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\Node\NodeAuthor.
 */

namespace Drupal\ds\Plugin\DsField\Node;

use Drupal\Component\Utility\String;
use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Plugin that renders the author of a node.
 *
 * @DsField(
 *   id = "node_author",
 *   title = @Translation("Author"),
 *   entity_type = "node",
 *   provider = "node"
 * )
 */
class NodeAuthor extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $user = $this->entity()->getOwner();

    // Users without a user name are anonymous users. These are never linked.
    if (empty($user->name)) {
      $anonymous_string = \Drupal::config('user.settings')->get('anonymous');
      return array(
        '#markup' => String::checkPlain($anonymous_string),
      );
    }

    $field = $this->getFieldConfiguration();
    if ($field['formatter'] == 'author') {
      return array(
        '#markup' => $user->getUsername(),
      );
    }

    if ($field['formatter'] == 'author_linked') {
      return array(
        '#theme' => 'username',
        '#account' => $user,
      );
    }

    // Otherwise return an empty array
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function formatters() {

    $formatters = array(
      'author' => t('Author'),
      'author_linked' => t('Author linked to profile')
    );

    return $formatters;
  }

}
