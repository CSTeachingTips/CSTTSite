<?php

/**
 * @file
 * Contains \Drupal\ds_ui\Form\TokenFieldForm.
 */

namespace Drupal\ds_ui\Form;

use Drupal\ds_ui\Form\FieldFormBase;

/**
 * Configures token fields.
 */
class TokenFieldForm extends FieldFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ds_field_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, $field_key = '') {
    $form = parent::buildForm($form, $form_state, $field_key);
    $field = $this->field;

    $form['content'] = array(
      '#type' => 'text_format',
      '#title' => t('Field content'),
      '#default_value' => isset($field['properties']['content']['value']) ? $field['properties']['content']['value'] : '',
      '#format' => isset($field['properties']['content']['format']) ? $field['properties']['content']['format'] : 'ds_token',
      '#base_type' => 'textarea',
      '#required' => TRUE,
    );

    // Token support.
    if (\Drupal::moduleHandler()->moduleExists('token')) {
      $form['tokens'] = array(
        '#title' => t('Tokens'),
        '#type' => 'container',
        '#states' => array(
          'invisible' => array(
            'input[name="use_token"]' => array('checked' => FALSE),
          ),
        ),
      );
      $form['tokens']['help'] = array(
        '#theme' => 'token_tree',
        '#token_types' => 'all',
        '#global_types' => FALSE,
        '#dialog' => TRUE,
      );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getProperties($form_state) {
    return array(
      'content' => $form_state['values']['content'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return DS_FIELD_TYPE_TOKEN;
  }

  /**
   * {@inheritdoc}
   */
  public function getTypeLabel() {
    return 'Token field';
  }

}
