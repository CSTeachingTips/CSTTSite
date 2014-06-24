<?php

/**
 * @file
 * Display Suite Extras administrative functions.
 */

/**
 * Alters the Display Suite settings screen.
 */
function ds_extras_settings_alter(&$form, &$form_state) {

  $config = \Drupal::config('ds.extras');

  $form['fs2'] = array(
    '#type' => 'details',
    '#title' => t('Extra fields'),
    '#group' => 'additional_settings',
    '#tree' => TRUE,
  );

  $form['fs2']['fields_extra'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable extra fields'),
    '#description' => t('Make fields from other modules available on the "Manage display" screens.'),
    '#default_value' => $config->get('fields_extra'),
  );

  $form['fs2']['fields_extra_list'] = array(
    '#type' => 'textarea',
    '#description' => t('Enter fields line by line, where each line is a combination of entity type, bundle and field name. E.g. node|article|tweetbutton. It might be possible that the actual content of the field depends on configuration of that field/module.'),
    '#default_value' => implode("\n", $config->get('fields_extra_list')),
    '#states' => array(
      'visible' => array(
        'input[name="fs2[fields_extra]"]' => array('checked' => TRUE),
      ),
    ),
  );

  $form['fs3'] = array(
    '#type' => 'details',
    '#title' => t('Other'),
    '#group' => 'additional_settings',
    '#tree' => TRUE,
  );

  $form['fs3']['switch_view_mode'] = array(
    '#type' => 'checkbox',
    '#title' => t('View mode per node'),
    '#description' => t('Change view modes for individual nodes. A new tab \'Display settings\' will appear on the content create form.<br />You can also pass the name of a view mode through the URL, eg node/x?v=full.<br />If you install the Page manager module and override the node view, Page manager will win.'),
    '#default_value' => $config->get('switch_view_mode'),
  );

  $form['fs3']['field_permissions'] = array(
    '#type' => 'checkbox',
    '#title' => t('Field permissions'),
    '#description' => t('Enables view permissions on all Display Suite fields.'),
    '#default_value' => $config->get('field_permissions'),
  );

  if (\Drupal::moduleHandler()->moduleExists('block')) {
    $form['fs3']['region_to_block'] = array(
      '#type' => 'checkbox',
      '#title' => t('Region to block'),
      '#description' => t('Create additional regions exposed as block. Note: this will not work on the default view mode.'),
      '#default_value' => $config->get('region_to_block'),
    );
  }

  $form['fs3']['switch_field'] = array(
    '#type' => 'checkbox',
    '#title' => t('View mode switcher'),
    '#description' => t('Adds a field with links to switch view modes inline with Ajax. Only works for nodes at this time.'),
    '#default_value' => $config->get('switch_field'),
  );

  $form['fs3']['hidden_region'] = array(
    '#type' => 'checkbox',
    '#title' => t('Hidden region'),
    '#description' => t('Add a hidden region to the layouts. Fields will be built but not printed.'),
    '#default_value' => $config->get('hidden_region'),
  );

  $form['fs3']['override_node_revision'] = array(
    '#type' => 'checkbox',
    '#title' => t('Custom node revision view mode'),
    '#description' => t('Override the node revision page view with a custom view mode'),
    '#default_value' => $config->get('override_node_revision'),
  );

  $options = array();
  $view_modes = entity_get_view_modes('node');
  foreach ($view_modes as $key => $view_mode) {
    $options[$key] = $view_mode['label'];
  }

  $form['fs3']['override_node_revision_view_mode'] = array(
    '#type' => 'select',
    '#description' => t('The revision view mode'),
    '#default_value' => $config->get('override_node_revision_view_mode'),
    '#options' => $options,
    '#states' => array(
      'visible' => array(
        'input[name="fs3[override_node_revision]"]' => array('checked' => TRUE),
      ),
    ),
  );

  $form['#submit'][] = 'ds_extras_settings_submit';
  $form['#attached']['library'][] = array('ds_extras', 'ds_extras.settings');
}

/**
 * Submit callback: Extras settings screen.
 */
function ds_extras_settings_submit($form, &$form_state) {
  $values = $form_state['values'];

  $extra_fields = array();
  if (!empty($values['fs2']['fields_extra_list'])) {
    $extra_fields = explode("\n", str_replace("\r", '', $values['fs2']['fields_extra_list']));
  }

  $config = \Drupal::config('ds.extras')
    ->set('fields_extra', $values['fs2']['fields_extra'])
    ->set('fields_extra_list', $extra_fields)
    ->set('switch_view_mode', $values['fs3']['switch_view_mode'])
    ->set('field_permissions', $values['fs3']['field_permissions'])
    ->set('switch_field', $values['fs3']['switch_field'])
    ->set('hidden_region', $values['fs3']['hidden_region'])
    ->set('override_node_revision', $values['fs3']['override_node_revision'])
    ->set('override_node_revision_view_mode', $values['fs3']['override_node_revision_view_mode']);
  if (\Drupal::moduleHandler()->moduleExists('block')) {
    $config->set('region_to_block', $values['fs3']['region_to_block']);
  }
  $config->save();

  // Mark the router items for rebuild
  \Drupal::service('router.builder')->setRebuildNeeded();
}

/**
 * Alter Manage display screen.
 */
function ds_extras_field_ui_alter(&$form, &$form_state) {
  // Region to block only fires if there is a layout and we're working on the
  // a view mode which is not equal to default.
  if (isset($form['#ds_layout']) && $form['#mode'] != 'default' && \Drupal::config('ds.extras')->get('region_to_block')) {

    // Get the entity_type, bundle and view mode.
    $entity_type = $form['#entity_type'];
    $bundle = $form['#bundle'];
    $view_mode = $form['#mode'];

    $region_blocks_options = array();
    $region_blocks = \Drupal::config('ds.extras')->get('region_blocks');

    if (!empty($region_blocks)) {
      foreach ($region_blocks as $key => $block) {
        if ($block['info'] == "{$entity_type}_{$bundle}_{$view_mode}") {
          $region_blocks_options[$key] = t('Remove') . ' ' . $block['title'];
        }
      }
    }

    $form['region_to_block'] = array(
      '#type' => 'details',
      '#group' => 'additional_settings',
      '#title' => t('Block regions'),
      '#description' => t('Create additional regions in this layout which will be exposed as blocks.')
    );

    $form['region_to_block']['new_block_region'] = array(
      '#type' => 'textfield',
      '#title' => t('Region name'),
      '#description' => t('Enter a name to create a new region.'),
    );
    $form['region_to_block']['new_block_region_key'] = array(
      '#title' => t('Machine name'),
      '#type' => 'machine_name',
      '#default_value' => '',
      '#maxlength' => 32,
      '#required' => FALSE,
      '#description' => t('The machine-readable name of this block region. This name must contain only lowercase letters and underscores. This name must be unique.'),
      '#disabled' => FALSE,
      '#machine_name' => array(
        'exists' => 'ds_extras_region_to_block_unique',
        'source' => array('region_to_block', 'new_block_region'),
      ),
    );

    if (!empty($region_blocks_options)) {
      $form['region_to_block']['remove_block_region'] = array(
        '#type' => 'checkboxes',
        '#title' => t('Existing block regions'),
        '#options' => $region_blocks_options,
        '#description' => t('Check the regions you want to remove.'),
      );
    }

    $form['#submit'][] = 'ds_extras_block_submit';
  }
}

/**
 * Submit callback after Field UI submission of a views display.
 */
function ds_extras_vd_field_ui_submit($form, &$form_state) {
  // Add the 'type' key to the extra title key so we can ditch the notice.
  $form_state['values']['fields']['title']['type'] = 'hidden';
}

/**
 * Submit callback: manage block regions.
 */
function ds_extras_block_submit($form, &$form_state) {

  // Create new region.
  if (!empty($form_state['values']['new_block_region'])) {

    // Get the entity_type, bundle and view mode.
    $entity_type = $form['#entity_type'];
    $bundle = $form['#bundle'];
    $view_mode = $form['#mode'];

    $block = array(
      'title' => $form_state['values']['new_block_region'],
      'info' => "{$entity_type}_{$bundle}_{$view_mode}",
    );

    $block_key = $form_state['values']['new_block_region_key'];
    $region_blocks = \Drupal::config('ds.extras')->get('region_blocks');
    if (empty($region_blocks)) {
      $region_blocks = array();
    }
    $region_blocks[$block_key] = $block;
    \Drupal::config('ds.extras')->set('region_blocks', $region_blocks)->save();
  }

  // Remove a region.
  if (isset($form_state['values']['remove_block_region'])) {
    $region_blocks = \Drupal::config('ds.extras')->get('region_blocks');
    $remove = $form_state['values']['remove_block_region'];
    foreach ($remove as $key => $value) {
      if ($key === $value) {
        unset($region_blocks[$key]);
      }

      // Make sure there is no active block instance for this ds block region.
      if (\Drupal::moduleHandler()->moduleExists('block')) {
        $ids = \Drupal::entityQuery('block')
          ->condition('plugin', 'ds_region_block:' . $key)
          ->execute();
        $block_storage = \Drupal::entityManager()->getStorageController('block');
        foreach ($block_storage->loadMultiple($ids) as $block) {
          $block->delete();
        }
      }
    }

    // Clear cached block and ds plugin defintions
    \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
    \Drupal::service('plugin.manager.ds')->clearCachedDefinitions();

    // Remove the region from ds settings
    \Drupal::config('ds.extras')->set('region_blocks', $region_blocks)->save();
  }
}

/**
 * Return unique region to block.
 */
function ds_extras_region_to_block_unique($name) {
  $region_blocks = \Drupal::config('ds.extras')->get('region_blocks');
  if (empty($region_blocks)) {
    $region_blocks = array();
  }
  $value = strtr($name, array('-' => '_'));
  return isset($region_blocks[$value]) ? TRUE : FALSE;
}
