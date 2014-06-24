<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\User\User.
 */

namespace Drupal\ds\Plugin\DsField\User;

use Drupal\ds\Plugin\DsField\Entity;

/**
 * Plugin that renders a view mode.
 *
 * @DsField(
 *   id = "user",
 *   title = @Translation("User"),
 *   entity_type = "node",
 *   provider = "user"
 * )
 */
class User extends Entity {

  /**
   * {@inhertidoc}
   */
  public function build() {
    $view_mode = $this->getEntityViewMode();

    $node = $this->entity();
    $uid = $node->getAuthorId();

    $user = entity_load('user', $uid);
    $build = entity_view($user, $view_mode);

    return $build;
  }

  /**
   * {@inhertidoc}
   */
  public function linkedEntity() {
    return 'user';
  }

}
