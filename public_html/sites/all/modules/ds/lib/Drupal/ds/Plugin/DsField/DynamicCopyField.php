<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\DynamicCopyField.
 */

namespace Drupal\ds\Plugin\DsField;

/**
 * Defines a generic dynamic field that holds a copy of an exisitng ds field.
 *
 * @DsField(
 *   id = "dynamic_copy_field",
 *   derivative = "Drupal\ds\Plugin\Derivative\DynamicCopyField",
 * )
 */
class DynamicCopyField extends DsFieldBase {

  /**
   * The loaded instance.
   */
  private $field_instance;

  /**
   * Constructs a Display Suite field plugin.
   */
  public function __construct($configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $manager = \Drupal::service('plugin.manager.ds');
    $this->field_instance = $manager->createInstance($plugin_definition['properties']['ds_plugin'], $configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return $this->field_instance->build();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm($form, &$form_state) {
    return $this->field_instance->settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary($settings) {
    return $this->field_instance->settingsSummary($settings);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return $this->field_instance->defaultConfiguration();
  }


  /**
   * {@inheritdoc}
   */
  public function formatters() {
    return $this->field_instance->formatters();
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowed() {
    $definition = $this->getPluginDefinition();

    return DsFieldBase::dynamicFieldIsAllowed($definition, $this->bundle(), $this->viewMode());
  }

}
