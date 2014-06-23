<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\DsFieldInterface.
 */

namespace Drupal\ds\Plugin\DsField;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Interface for DS plugins.
 */
interface DsFieldInterface extends ConfigurablePluginInterface {

  /**
   * Renders a field.
   */
  public function build();

  /**
   * Returns the summary of the chosen settings.
   *
   * @param $field
   *   Contains all the general configuration of the field.
   * @param $settings
   *   Contains the settings of the field.
   *
   * @return array
   *   A render array containing the summary.
   */
  public function settingsSummary($settings);

  /**
   * Returns a list of possible formatters for this field.
   *
   * @return array
   *   A list of possible formatters
   */
  public function formatters();

  /**
   * Returns if the field is allowed on the field UI screen.
   */
  public function isAllowed();

}
