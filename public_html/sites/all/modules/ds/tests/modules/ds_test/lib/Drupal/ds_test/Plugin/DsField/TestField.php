<?php

/**
 * @file
 * Contains \Drupal\ds_test\Plugin\DsField\TestField.
 */

namespace Drupal\ds_test\Plugin\DsField;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Test code field from plugin.
 *
 * @DsField(
 *   id = "test_field",
 *   title = @Translation("Test code field from plugin"),
 *   entity_type = "node"
 * )
 */
class TestField extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return 'Test code field on node ' . $this->entity()->id();
  }

}
