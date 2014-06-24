<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\Field.
 */

namespace Drupal\ds\Plugin\DsField;

use Drupal\Component\Utility\String;

/**
 * The base plugin to create DS fields.
 */
abstract class Field extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    // Initialize output
    $output = '';

    // Basic string.
    $entity_render_key = $this->entityRenderKey();

    if (isset($config['link text'])) {
      $output = t($config['link text']);
    }
    elseif (!empty($entity_render_key) && isset($this->entity()->{$entity_render_key})) {
      if ($this->getEntityTypeId() == 'user' && $entity_render_key == 'name') {
        $output = $this->entity()->getUsername();
      }
      else {
        $output = $this->entity()->{$entity_render_key}->value;
      }
    }

    if (empty($output)) {
      return array();
    }

    // Link.
    if (!empty($config['link'])) {
      $uri_info = $this->entity()->urlInfo();
      $output = l($output, $this->entity()->getSystemPath(), $uri_info['options']);
    }
    else {
      $output = String::checkPlain($output);
    }

    // Wrapper and class.
    if (!empty($config['wrapper'])) {
      $wrapper = String::checkPlain($config['wrapper']);
      $class = (!empty($config['class'])) ? ' class="' . String::checkPlain($config['class']) . '"' : '';
      $output = '<' . $wrapper . $class . '>' . $output . '</' . $wrapper . '>';
    }

    return array(
      '#markup' => $output,
    );
  }

  /**
   * Returns the entity render key for this field.
   */
  protected function entityRenderKey() {
    return '';
  }

}
