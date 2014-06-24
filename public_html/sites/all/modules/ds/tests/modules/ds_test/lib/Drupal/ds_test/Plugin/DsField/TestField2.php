<?php

/**
 * @file
 * Contains \Drupal\ds_test\Plugin\DsField\TestField2.
 */

namespace Drupal\ds_test\Plugin\DsField;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Test code field from plugin.
 *
 * @DsField(
 *   id = "test_field_2",
 *   title = @Translation("Test code field from plugin 2"),
 *   entity_type = "node"
 * )
 */
class TestField2 extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return 'Test code field on node ' . $this->entity()->id();
  }

}
