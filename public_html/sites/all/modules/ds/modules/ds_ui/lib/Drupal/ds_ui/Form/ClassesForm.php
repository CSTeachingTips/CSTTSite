<?php

/**
 * @file
 * Contains \Drupal\ds_ui\Form\ClassesForm.
 */

namespace Drupal\ds_ui\Form;

use Drupal\Core\Form\ConfigFormBase;

/**
 * Configures classes used by wrappers and regions.
 */
class ClassesForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ds_classes_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->configFactory->get('ds.settings');

    $form['regions'] = array(
      '#type' => 'textarea',
      '#title' => t('CSS classes for regions'),
      '#default_value' => implode("\n", $config->get('classes.region')),
      '#description' => t('Configure CSS classes which you can add to regions on the "manage display" screens. Add multiple CSS classes line by line.<br />If you want to have a friendly name, separate class and friendly name by |, but this is not required. eg:<br /><em>class_name_1<br />class_name_2|Friendly name<br />class_name_3</em>')
    );

    $form['fields'] = array(
      '#type' => 'textarea',
      '#title' => t('CSS classes for fields'),
      '#default_value' =>  implode("\n", $config->get('classes.field')),
      '#description' => t('Configure CSS classes which you can add to fields on the "manage display" screens. Add multiple CSS classes line by line.<br />If you want to have a friendly name, separate class and friendly name by |, but this is not required. eg:<br /><em>class_name_1<br />class_name_2|Friendly name<br />class_name_3</em>')
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    // Prepare region classes
    $region_classes = array();
    if (!empty($form_state['values']['regions'])) {
      $region_classes = explode("\n", str_replace("\r", '', $form_state['values']['regions']));
    }

    // Prepare field classes
    $field_classes = array();
    if (!empty($form_state['values']['fields'])) {
      $field_classes = explode("\n", str_replace("\r", '', $form_state['values']['fields']));
    }

    $config = $this->configFactory->get('ds.settings');
    $config->set('classes.region', $region_classes)
      ->set('classes.field', $field_classes)
      ->save();
  }

}
