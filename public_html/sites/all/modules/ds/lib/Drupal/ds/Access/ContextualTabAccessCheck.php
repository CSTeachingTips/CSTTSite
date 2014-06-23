<?php

/**
 * @file
 * Contains \Drupal\ds\Access\ContextualTabAccessCheck.
 */

namespace Drupal\ds\Access;

use Drupal\Core\Routing\Access\AccessInterface as RoutingAccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * Provides an access check for ds extras switch field routes.
 */
class ContextualTabAccessCheck implements RoutingAccessInterface {

  /**
   * {@inheritdoc}
   */
  public function access(Route $route, Request $request, AccountInterface $account) {
    return \Drupal::moduleHandler()->moduleExists('contextual') && \Drupal::moduleHandler()->moduleExists('field_ui') ? static::ALLOW : static::DENY;
  }

}
