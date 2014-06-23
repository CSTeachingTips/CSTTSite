<?php

/**
 * @file
 * Contains \Drupal\ds\Form\SettingsForm.
 */

namespace Drupal\ds\Form;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configures Display Suite settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructs a \Drupal\ds\Form\SettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(ConfigFactory $config_factory, ModuleHandlerInterface $module_handler) {
    parent::__construct($config_factory);

    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ds_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->configFactory->get('ds.settings');

    $form['additional_settings'] = array(
      '#type' => 'vertical_tabs',
      '#attached' => array(
        'library' => array(array('ds', 'ds.admin.js')),
      ),
    );

    $form['fs1'] = array(
      '#type' => 'details',
      '#title' => t('Field Templates'),
      '#group' => 'additional_settings',
      '#tree' => TRUE,
      '#collapsed' => FALSE,
    );

    $form['fs1']['field_template'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable Field Templates'),
      '#description' => t('Customize the labels and the HTML output of your fields.'),
      '#default_value' => $config->get('field_template'),
    );

    $theme_functions = $this->moduleHandler->invokeAll('ds_field_theme_functions_info');
    $form['fs1']['ft-default'] = array(
      '#type' => 'select',
      '#title' => t('Default Field Template'),
      '#options' => $theme_functions,
      '#default_value' => $config->get('ft-default'),
      '#description' => t('Default will output the field as defined in Drupal Core.<br />Reset will strip all HTML.<br />Minimal adds a simple wrapper around the field.<br/>There is also an Expert Field Template that gives full control over the HTML, but can only be set per field.<br /><br />You can override this setting per field on the "Manage display" screens or when creating fields on the instance level.<br /><br /><strong>Template suggestions</strong><br />You can create .html.twig files as well for these field theme functions, e.g. field--reset.html.twig, field--minimal.html.twig<br /><br /><label>CSS classes</label>You can add custom CSS classes on the <a href="!url">classes form</a>. These classes can be added to fields using the Default Field Template.<br /><br /><label>Advanced</label>You can create your own custom field templates which need to be defined with hook_ds_field_theme_functions_info(). See ds.api.php for an example.', array('!url' => url('admin/structure/ds/classes'))),
      '#states' => array(
        'visible' => array(
          'input[name="fs1[field_template]"]' => array('checked' => TRUE),
        ),
      ),
    );

    $form['fs1']['ft-kill-colon'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide colon'),
      '#default_value' => $config->get('ft-kill-colon'),
      '#description' => t('Hide the colon on the reset field template.'),
      '#states' => array(
        'visible' => array(
          'select[name="fs1[ft-default]"]' => array('value' => 'theme_ds_field_reset'),
          'input[name="fs1[field_template]"]' => array('checked' => TRUE),
        ),
      ),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    $this->configFactory->get('ds.settings')
      ->set('field_template', $form_state['values']['fs1']['field_template'])
      ->set('ft-default', $form_state['values']['fs1']['ft-default'])
      ->set('ft-kill-colon', $form_state['values']['fs1']['ft-kill-colon'])
      ->save();

    entity_info_cache_clear();
    field_info_cache_clear();
    $this->moduleHandler->resetImplementations();
    \Drupal::service('theme.registry')->reset();
    \Drupal::service('router.builder')->setRebuildNeeded();
  }

}
