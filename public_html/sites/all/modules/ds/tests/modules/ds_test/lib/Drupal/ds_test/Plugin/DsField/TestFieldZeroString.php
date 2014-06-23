<?php

/**
 * @file
 * Contains \Drupal\ds_test\Plugin\DsField\TestFieldZeroString.
 */

namespace Drupal\ds_test\Plugin\DsField;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Test code field that returns zero as a string.
 *
 * @DsField(
 *   id = "test_field_zero_string",
 *   title = @Translation("Test code field that returns zero as a string"),
 *   entity_type = "node"
 * )
 */
class TestFieldZeroString extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return '0';
  }

}
