<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\TokenBase.
 */

namespace Drupal\ds\Plugin\DsField;

/**
 * The base plugin to create DS code fields.
 */
abstract class TokenBase extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $content = $this->content();
    $format = $this->format();

    $value = check_markup($content, $format);
    $value = \Drupal::service('token')->replace($value, array($this->getEntityTypeId() => $this->entity()), array('clear' => TRUE));

    return array(
      '#markup' => $value,
    );
  }

  /**
   * Returns the format of the code field.
   */
  protected function format() {
    return 'plain_text';
  }

  /**
   * Returns the value of the code field.
   */
  protected function content() {
    return '';
  }

}
