<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\Derivative\DynamicCopyField.
 */

namespace Drupal\ds\Plugin\Derivative;

/**
 * Retrieves dynamic ds field plugin definitions.
 */
class DynamicCopyField extends DynamicField {

  /**
   * {@inheritdoc}
   */
  protected function getType() {
    return DS_FIELD_TYPE_COPY;
  }

}
