<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\DynamicTokenField.
 */

namespace Drupal\ds\Plugin\DsField;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Defines a generic dynamic code field.
 *
 * @DsField(
 *   id = "dynamic_token_field",
 *   derivative = "Drupal\ds\Plugin\Derivative\DynamicTokenField"
 * )
 */
class DynamicTokenField extends TokenBase {

  /**
   * {@inheritdoc}
   */
  public function content() {
    $definition = $this->getPluginDefinition();
    return $definition['properties']['content']['value'];
  }

  /**
   * {@inheritdoc}
   */
  public function format() {
    $definition = $this->getPluginDefinition();
    return $definition['properties']['content']['format'];
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowed() {
    $definition = $this->getPluginDefinition();
    return DsFieldBase::dynamicFieldIsAllowed($definition, $this->bundle(), $this->viewMode());
  }
}
