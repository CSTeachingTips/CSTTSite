<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\Derivative\DynamicTokenField.
 */

namespace Drupal\ds\Plugin\Derivative;

/**
 * Retrieves dynamic code field plugin definitions.
 */
class DynamicTokenField extends DynamicField {

  /**
   * {@inheritdoc}
   */
  protected function getType() {
    return DS_FIELD_TYPE_TOKEN;
  }

}
