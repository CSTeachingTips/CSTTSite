<?php

/**
 * @file
 * Contains \Drupal\ds_test\Plugin\DsField\TestFieldZeroFloat.
 */

namespace Drupal\ds_test\Plugin\DsField;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Test code field that returns zero as a floating point number.
 *
 * @DsField(
 *   id = "test_field_zero_float",
 *   title = @Translation("Test code field that returns zero as a floating point number"),
 *   entity_type = "node"
 * )
 */
class TestFieldZeroFloat extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return 0.0;
  }

}
