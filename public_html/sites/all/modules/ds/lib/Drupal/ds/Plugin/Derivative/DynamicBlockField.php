<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\Derivative\DynamicBlockField.
 */

namespace Drupal\ds\Plugin\Derivative;

/**
 * Retrieves dynamic block field plugin definitions.
 */
class DynamicBlockField extends DynamicField {

  /**
   * {@inheritdoc}
   */
  protected function getType() {
    return DS_FIELD_TYPE_BLOCK;
  }

}
