<?php

/**
 * @file
 * Field UI functions for Display Suite.
 */

use Drupal\Component\Utility\String;
use Drupal\Core\Render\Element;
use Drupal\ds\Ds;
use Drupal\field\Field;
use Drupal\field_ui\FieldUI;

/**
 * Adds the Display Suite fields and layouts to the form.
 */
function ds_field_ui_fields_layouts(&$form, &$form_state) {
  global $base_root, $base_path;

  // Get the entity_type, bundle and view mode.
  $entity_type = $form['#entity_type'];
  $bundle = $form['#bundle'];
  $view_mode = $form['#mode'];

  $form['#export_id'] = $entity_type . '.' . $bundle . '.' . $view_mode;

  // Create vertical tabs.
  ds_field_ui_create_vertical_tabs($form);

  // Add layout fieldset.
  _ds_field_ui_table_layouts($entity_type, $bundle, $view_mode, $form, $form_state);

  // Add/alter fields on the table, but only if a layout is selected.
  if ($view_mode != 'form' && !empty($form['#ds_layout'])) {
    _ds_field_ui_fields($entity_type, $bundle, $view_mode, $form, $form_state);

    // Also alter core fields
    _ds_field_ui_core_fields($form, $form_state);
  }

  // Special validate function for field group.
  if (isset($form_state['no_field_group'])) {
    array_unshift($form['#validate'], '_ds_field_group_field_ui_fix_notices');
  }

  // Attach js.
  $form['#attached']['js'][] = drupal_get_path('module', 'ds') . '/js/ds.admin.js';

  // Attach css.
  $form['#attached']['css'][] = drupal_get_path('module', 'ds') . '/css/ds.admin.css';

  // Add process function to add the regions.
  $form['#process'][] = 'ds_field_ui_regions';

  // Add a destination so we can get back if layout has been changed.
  $form['ds_source'] = array(
    '#type' => 'hidden',
    '#value' => $base_root . $base_path,
  );
  $form['ds_destination'] = array(
    '#type' => 'hidden',
    '#value' => drupal_get_destination(),
  );
  $form['ds_entity_type'] = array(
    '#type' => 'hidden',
    '#value' => $entity_type,
  );
  $form['ds_bundle'] = array(
    '#type' => 'hidden',
    '#value' => $bundle,
  );
  $form['ds_view_mode'] = array(
    '#type' => 'hidden',
    '#value' => $view_mode,
  );
}

/**
 * Create vertical tabs.
 */
function ds_field_ui_create_vertical_tabs(&$form) {

  // Add additional settings vertical tab.
  if (!isset($form['additional_settings'])) {
    $form['additional_settings'] = array(
      '#type' => 'vertical_tabs',
      '#theme_wrappers' => array('vertical_tabs'),
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    );
  }

  $view_mode_admin_access = \Drupal::currentUser()->hasPermission('admin_view_modes') && \Drupal::moduleHandler()->moduleExists('ds_ui');
  if (isset($form['modes'])) {
    $form['modes']['#group'] = 'additional_settings';
    $form['modes']['#weight'] = -10;
    if ($view_mode_admin_access) {
      $form['modes']['view_modes_custom']['#description'] = l(t('Manage view modes'), 'admin/structure/ds/view_modes');
    }
  }
  else {
    if ($view_mode_admin_access) {
      $form['modes'] = array(
        '#type' => 'details',
        '#title' => t('Custom display settings'),
        '#group' => 'additional_settings',
      );
      $form['modes']['view_modes_custom'] = array(
       '#markup' => l(t('Manage view modes'), 'admin/structure/ds/view_modes'),
      );
    }
  }
}

/**
 * Add Regions to 'Manage fields' or 'Manage display' screen.
 *
 * @param $form
 *   The form to add layout fieldset and extra Display Suite fields.
 * @param $form_state
 *   The current form state.
 */
function ds_field_ui_regions($form, $form_state) {

  // Get the entity_type, bundle and view mode.
  $entity_type = $form['#entity_type'];
  $bundle = $form['#bundle'];
  $view_mode = $form['#mode'];

  // Ignore field_group options.
  if (isset($form_state['no_field_group'])) {
    unset($form['fields']['_add_new_group']);
    $form['field_group']['#access'] = FALSE;
  }

  // Check layout.
  $layout = isset($form['#ds_layout']) ? $form['#ds_layout'] : FALSE;

  // Build an array which keys are the field names and
  // values are the region they are rendered in.
  $field_regions = array();

  // Change UI to add Region column if we have a layout.
  if ($layout) {

    foreach ($layout['settings']['regions'] as $region_name => $field_names) {
      foreach ($field_names as $field_name) {
        $field_regions[$field_name] = $region_name;
      }
    }

    $table = &$form['fields'];

    if ($view_mode != 'form') {
      $table['#header'] = array(
        t('Field'),
        t('Weight'),
        t('Parent'),
        t('Region'),
        t('Label'),
        array('data' => t('Format'), 'colspan' => 3),
      );
    }
    else {
      $table['#header'] = array(
        t('Label'),
        t('Weight'),
        t('Parent'),
        t('Region'),
        t('Name'),
        t('Field'),
        t('Widget'),
        array('data' => t('Operations'), 'colspan' => 2),
      );
    }

    // Remove label and format for views.
    if ($entity_type == 'ds_views') {
      $table['#header'][4] = '';
    }

    $table['#regions'] = array();
    $region_options = array();
    foreach ($layout['regions'] as $region_key => $region_title) {
      $region_options[$region_key] = $region_title;
      $table['#regions'][$region_key] = array(
        'title' => $region_title,
        'message' => t('No fields are displayed in this region'),
      );
    }

    // Let other modules alter the regions.
    $context = array(
      'entity_type' => $entity_type,
      'bundle' => $bundle,
      'view_mode' => $view_mode
    );
    $region_info = array(
      'region_options' => &$region_options,
      'table_regions' => &$table['#regions'],
    );
    \Drupal::moduleHandler()->alter('ds_layout_region', $context, $region_info);

    $region_options['hidden'] = $view_mode != 'form' ? t('Disabled') : t('Hidden');
    $table['#regions']['hidden'] = array(
      'title' => $view_mode != 'form' ? t('Disabled') : t('Hidden'),
      'message' => t('No fields are hidden.')
    );

    $region = array(
      '#type' => 'select',
      '#options' => $region_options,
      '#default_value' => 'hidden',
      '#attributes' => array(
        'class' => array('ds-field-region'),
      )
    );

    $limit_items = array(
      '#type' => 'textfield',
      '#size' => 2,
      '#weight' => 10,
      '#default_value' => '#',
      '#prefix' => '<div class="limit-float">',
      '#suffix' => '</div><div class="clearfix"></div>',
      '#attributes' => array(
        'alt' => t('Enter a number to limit the number of items or \'delta\' to print a specific delta (usually configured in views or found in entity->ds_delta). Leave empty to display them all. Note that depending on the formatter settings, this option might not always work.'),
        'title' => t('Enter a number to limit the number of items or \'delta\' to print a specific delta (usually configured in views or found in entity->ds_delta). Leave empty to display them all. Note that depending on the formatter settings, this option might not always work.'),
      ),
    );

    // Hide this if we plugin_settings_edit is not empty so it doesn't confuse users.
    if (!empty($form_state['plugin_settings_edit'])) {
      $limit_items['#access'] = FALSE;
    }

    // Update existing rows by changing rowHandler and adding regions.
    foreach (Element::children($table) as $name) {
      $row = &$table[$name];
      $row['#js_settings'] = array('rowHandler' => 'ds');
      $row['#region_callback'] = 'ds_field_ui_row_region';

      // Remove hidden format.
      if (isset($row['plugin']['type']['#options']['hidden'])) {
        unset($row['plugin']['type']['#options']['hidden']);
      }

      // Add label class.
      if (isset($row['label'])) {
        if (isset($form_state['plugin_settings']) && isset($form_state['plugin_settings'][$name]['ft'])) {
          if (!empty($form_state['plugin_settings'][$name]['ft']['lb'])) {
            $row['human_name']['#markup'] = String::checkPlain($form_state['plugin_settings'][$name]['ft']['lb']) . ' ' . t('(Original: !orig)', array('!orig' => $row['human_name']['#markup']));
          }
        }
      }

      // Limit items.
      $field_info = Field::fieldInfo()->getField($entity_type, $name);
      if (!empty($field_info) && $field_info->getCardinality() != 1 && $view_mode != 'form') {
        $row['plugin']['type']['#prefix'] = '<div class="limit-float">';
        $row['plugin']['type']['#suffix'] = '</div>';
        $row['plugin']['limit'] = $limit_items;
        $row['plugin']['limit']['#default_value'] = (isset($layout['settings']['limit']) && isset($layout['settings']['limit'][$name])) ? $layout['settings']['limit'][$name] : '#';
      }

      // Disable label and format for views.
      if ($entity_type == 'ds_views') {
        $row['label']['#access'] = FALSE;
      }

      // Add region.
      $split = ($view_mode != 'form') ? 7 : 6;
      if ($row['#row_type'] == 'group' && $view_mode == 'form') {
        $split = ($view_mode != 'form') ? 8 : 7;
      }
      $second = array_splice($row, $split);
      $row['region'] = $region;
      $row['region']['#default_value'] = (isset($field_regions[$name]) && isset($region_options[$field_regions[$name]])) ? $field_regions[$name] : 'hidden';
      $row = array_merge($row, $second);
    }
  }

  return $form;
}

/**
 * Returns the region to which a row in the Field UI screen belongs.
 *
 * @param $row
 *   The current row that is being rendered in the Field UI screen.
 *
 * @return string
 *   The region.
 */
function ds_field_ui_row_region($row) {
  return isset($row['region']['#value']) ? $row['region']['#value'] : 'hidden';
}

/**
 * Move the view modes so Field UI can handle them.
 */
function ds_field_ui_layouts_validate($form, &$form_state) {
  if (isset($form_state['values']['modes']['view_modes_custom'])) {
    $form_state['values']['view_modes_custom'] = $form_state['values']['modes']['view_modes_custom'];
  }
}

/**
 * Save the layout settings from the 'Manage display' screen.
 */
function ds_field_ui_layouts_save($form, &$form_state) {
  // Get default values.
  $entity_type = $form['#entity_type'];
  $bundle = $form['#bundle'];
  $view_mode = $form['#mode'];

  // Determine layout variables.
  $layout = $form_state['values']['layout'];
  $old_layout = $form_state['values']['old_layout'];
  $new_layout = ($layout != $old_layout) || empty($old_layout);

  // Save layout and add regions if necessary.
  $record = array();
  $record['id'] = $form['#export_id'];
  $record['entity_type'] = $entity_type;
  $record['bundle'] = $bundle;
  $record['view_mode'] = $view_mode;
  $record['layout'] = $layout;
  $record['settings'] = array();

  // Remove old layout if necessary.
  if ($new_layout && !empty($old_layout)) {
    \Drupal::config('ds.layout_settings.' . $entity_type . '.' . $bundle . '.' . $view_mode)->delete();
  }

  if ($new_layout && !empty($layout)) {
    // Let other modules alter the layout settings.
    \Drupal::moduleHandler()->alter('ds_layout_settings', $record, $form_state);

    // Move current visible fields into a default region, so
    // we keep their current settings.
    $layouts = Ds::getLayouts();
    $sl = $layouts[$layout];
    $first_region = key($sl['regions']);
    $record['settings']['regions'] = array();
    $record['settings']['fields'] = array();
    $record['settings']['classes'] = array();
    $record['settings']['wrappers'] = array();
    $record['settings']['layout_wrapper'] = 'div';
    $record['settings']['layout_attributes'] = '';
    $record['settings']['layout_attributes_merge'] = settings()->get('ds_layout_attributes_merge', TRUE);
    $record['settings']['layout_link_attribute'] = FALSE;
    $record['settings']['layout_link_custom'] = '';
    $fields = _ds_sort_fields($form_state['values']['fields'], 'weight');
    foreach ($fields as $field_key => $field) {

      // Ignore new fieldgroup, new field or existing field.
      if (in_array($field_key, array('_add_new_field', '_add_existing_field', '_add_new_group'))) {
        continue;
      }

      // Can either be form or display.
      if ((isset($field['type']) && $field['type'] != 'hidden') || $record['view_mode'] == 'form') {
        $record['settings']['regions'][$first_region][] = $field_key;
      }
    }
    // In case this is the full node view mode and if the comment module
    // is enabled for this content type, add it as well.
    if ($record['entity_type'] == 'node' && $record['view_mode'] == 'full' && \Drupal::moduleHandler()->moduleExists('comment')) {
      $record['settings']['regions'][$first_region][] = 'comments';
    }

    // Save the configuration.
    \Drupal::config('ds.layout_settings.' . $entity_type . '.' . $bundle . '.' . $view_mode)->setData($record)->save();
  }
  // Update existing layout.
  elseif (!empty($layout)) {

    $fields = _ds_sort_fields($form_state['values']['fields'], 'weight');

    foreach ($fields as $key => $field) {

      // Make sure we need to save anything for this field.
      if (_ds_field_valid($key, $field, $form_state, $view_mode)) {
        continue;
      }

      if (!isset($record['settings']['regions'][$field['region']])) {
        $record['settings']['regions'][$field['region']] = array();
      }
      $record['settings']['regions'][$field['region']][] = $key;

      // Save limit.
      $limit = isset($field['plugin']['limit']) ? trim($field['plugin']['limit']) : '';
      if (is_numeric($limit) || $limit === 'delta') {
        $record['settings']['limit'][$key] = $limit;
      }
    }

    // Save the region classes.
    $record['settings']['classes'] = array();
    foreach (array_keys($form['fields']['#regions']) as $region) {

      // Ignore hidden region.
      if ($region == 'hidden') {
        continue;
      }

      if (isset($form_state['values']['layout_class'])) {
        $record['settings']['classes']['layout_class'] = $form_state['values']['layout_class'];
      }

      // Additional classes on regions.
      if (isset($form_state['values'][$region])) {
        // Do not save empty string.
        $classes = is_array($form_state['values'][$region]) ? implode(' ', $form_state['values'][$region]) : array();
        if (!empty($classes)) {
          $record['settings']['classes'][$region] = $form_state['values'][$region];
        }
      }

      // Additional wrappers on regions.
      if (isset($form_state['values']['region_wrapper'][$region])) {
        $record['settings']['wrappers'][$region] = $form_state['values']['region_wrapper'][$region];
      }
    }

    // Layout wrapper
    $record['settings']['layout_wrapper'] = $form_state['values']['region_wrapper']['layout_wrapper'];
    $record['settings']['layout_attributes'] = filter_xss_admin($form_state['values']['region_wrapper']['layout_attributes']);
    $record['settings']['layout_attributes_merge'] = $form_state['values']['region_wrapper']['layout_attributes_merge'];

    // Link attribute.
    $record['settings']['layout_link_attribute'] = $form_state['values']['region_wrapper']['layout_link_attribute'];
    $record['settings']['layout_link_custom'] = $form_state['values']['region_wrapper']['layout_link_custom'];

    // Let other modules alter the layout settings.
    \Drupal::moduleHandler()->alter('ds_layout_settings', $record, $form_state);

    // Save the configuration.
    \Drupal::config('ds.layout_settings.' . $entity_type . '.' . $bundle . '.' . $view_mode)->setData($record)->save();

    // Clear entity info cache.
    entity_info_cache_clear();
  }
}

/**
 * Form validation handler for _ds_field_ui_fields().
 */
function ds_field_ui_fields_validate($form, &$form_state) {
  foreach (Element::children($form['fields']) as $key) {
    if (isset($form_state['values']['fields'][$key]['settings_edit_form']['settings']['ft'])) {
      $settings = $form_state['values']['fields'][$key]['settings_edit_form']['settings']['ft'];
      if (!empty($settings)) {
        $merge = isset($form_state['plugin_settings'][$key]['ft']) ? $form_state['plugin_settings'][$key]['ft'] : array();
        $form_state['plugin_settings'][$key]['ft'] = array_merge($merge, $settings);
      }
    }
  }
}

/**
 * Save the field settings from the 'Manage display' screen.
 */
function ds_field_ui_fields_save($form, &$form_state) {
  // Setup some variables.
  $entity_type = $form['#entity_type'];
  $bundle = $form['#bundle'];
  $view_mode = $form['#mode'];

  $field_settings = array();

  // Save settings for each field.
  $fields = $form['#ds_fields'];
  foreach ($fields as $field) {

    // Field settings.
    $field_values = $form_state['values']['fields'][$field];

    // In case the region is hidden, do not save.
    if (isset($field_values['region']) && $field_values['region'] == 'hidden') {
      continue;
    }

    // Build settings.
    $settings = array();
    $settings['weight'] = $field_values['weight'];
    $settings['label'] = $field_values['label'];
    $settings['formatter'] = $field_values['plugin']['type'];

    // Any formatter settings.
    if (isset($form_state['plugin_settings'][$field])) {
      $settings['plugin_settings'] = $form_state['plugin_settings'][$field];
    }

    $field_settings[$field] = $settings;
  }

  // Allow other modules to modify the field settings before they get saved.
  \Drupal::moduleHandler()->alter('ds_field_settings', $field_settings, $form, $form_state);

  // Save the record.
  if (!empty($field_settings)) {
    $config = array();
    $config['id'] = $form['#export_id'];
    $config['entity_type'] = $entity_type;
    $config['bundle'] = $bundle;
    $config['view_mode'] = $view_mode;
    $config['settings'] = $field_settings;
    \Drupal::config('ds.field_settings.' . $entity_type . '.' . $bundle . '.' . $view_mode)->setData($config)->save();
  }

  // Clear the ds_fields cache.
  cache()->invalidateTags(array('ds_fields_info' => TRUE));
}

/**
 * Clone a fields layout.
 */
function ds_field_ui_layout_clone($form, &$form_state) {

  $clone = $form_state['values']['clone'];
  list($ce, $cb, $cv) = explode('.', $clone);
  $entity_type = $form['#entity_type'];
  $bundle = $form['#bundle'];
  $view_mode = $form['#mode'];

  $layout = \Drupal::config('ds.layout_settings.' . $ce  . '.' . $cb . '.' . $cv)->get();
  $field_settings = \Drupal::config('ds.field_settings.' . $ce . '.' . $cb . '.' . $cv)->get();

  // Delete previous layout configuration
  \Drupal::config('ds.field_settings.' . $entity_type . '.' . $bundle . '.' . $view_mode)->delete();

  // Delete previous field configuration
  \Drupal::config('ds.field_settings.' . $entity_type . '.' . $bundle . '.' . $view_mode)->delete();

  // Save new layout record for ds.
  if ($layout) {
    $layout['id'] = $form['#export_id'];
    $layout['entity_type'] = $entity_type;
    $layout['bundle'] = $bundle;
    $layout['view_mode'] = $view_mode;

    // Let other modules alter the layout settings.
    \Drupal::moduleHandler()->alter('ds_layout_settings', $record, $form_state);

    // Save layout record.
    \Drupal::config('ds.layout_settings.' . $entity_type . '.' . $bundle . '.' . $view_mode)->setData($layout)->save();

    // Copy the view mode settings.
    _ds_field_ui_clone_view_mode_settings($entity_type, $bundle, $view_mode, $cv, $field_settings, $form['#export_id']);

    // Clear entity info cache.
    entity_info_cache_clear();

    // Show message.
    drupal_set_message(t('The layout has been cloned.'));

  }
  else {
    drupal_set_message(t('No layout was cloned.'));
  }
}

/**
 * Populates display settings for a new view mode from the another view mode.
 *
 * This is almost a straight copy from Field UI, but with the addition
 * that we can pass the view mode from which we want to clone from.
 */
function _ds_field_ui_clone_view_mode_settings($entity_type, $bundle, $view_mode, $copy_view_mode, $field_settings, $id) {
  // @todo fix this
  // $copy_display = entity_get_display($entity_type, $bundle, $view_mode)->createCopy($copy_view_mode);
  // $copy_display->save();

  // Update display settings for 'extra fields'.
  foreach (array_keys($field_settings['settings']['extra_fields']['display']) as $name) {
    if (isset($field_settings['settings']['extra_fields']['display'][$name][$copy_view_mode])) {
      $field_settings['settings']['extra_fields']['display'][$name][$view_mode] = $field_settings['extra_fields']['display'][$name][$copy_view_mode];
    }
  }

  $field_settings['id'] = $id;
  $field_settings['entity_type'] = $entity_type;
  $field_settings['bundle'] = $bundle;
  $field_settings['view_mode'] = $view_mode;

  \Drupal::config('ds.field_settings.' . $entity_type . '.' . $bundle . '.' . $view_mode)->setData($field_settings)->save();
}

/**
 * Creates a summary for the field format configuration summary.
 *
 * @param $field
 *   The configuration of the field.
 *
 * @return array $summary
 *   The summary
 */
function ds_field_settings_summary($plugin_instance, $settings, $form_state) {
  // Create the form
  $summary = $plugin_instance->settingsSummary($settings);

  // Add field template summary
  ds_field_formatter_settings_summary_alter($summary, array('field' => $plugin_instance), $form_state);

  if (empty($summary)) {
    return array();
  }

  return array(
    '#markup' => '<div class="field-plugin-summary">' . implode('<br />', $summary) . '</div>',
    '#cell_attributes' => array('class' => array('field-plugin-summary-cell')),
  );
}

/**
 * Creates a form for Display Suite fields.
 * .
 * @param $field
 *   The field definition.
 * @param $form_state
 *   The form_state
 *
 * @return mixed $form
 */
function ds_field_settings_form(\Drupal\ds\Plugin\DsField\DsFieldInterface $plugin_instance, &$form_state) {
  // Create the form
  $form = $plugin_instance->settingsForm(array(), $form_state);

  // Add field template settings to every field if enabled.
  if (\Drupal::config('ds.settings')->get('field_template')) {
    $context = array(
      'instance' => array(
        'entity_type' => $plugin_instance->getEntityTypeId(),
        'bundle' => $plugin_instance->bundle(),
        'field_name' => $plugin_instance->getName(),
      ),
      'view_mode' => $plugin_instance->viewMode(),
    );

    ds_field_template_settings_form($form, $form_state, $context);
  }

  return $form;
}

/**
 * Add fake field group value in.
 */
function _ds_field_group_field_ui_fix_notices($form, &$form_state) {
  $field_group = array(
    'group_name' => '',
    'label' => '',
  );
  $form_state['values']['fields']['_add_new_group'] = $field_group;
}

/**
 * Add the layouts fieldset on the Field UI screen.
 *
 * @param $entity_type
 *   The name of the entity type.
 * @param $bundle
 *   The name of the bundle
 * @param $view_mode
 *   The name of the view_mode
 * @param $form
 *   A collection of form properties.
 * @param $form_state
 *   The form_state
 */
function _ds_field_ui_table_layouts($entity_type, $bundle, $view_mode, &$form, $form_state) {

  $ds_layouts = Ds::getLayouts();
  $layout_options = array('' => t('- None -'));
  $optgroup = '';
  foreach ($ds_layouts as $key => $layout) {
    $optgroup = 'Display Suite';

    // Create new layout option group.
    if (!empty($layout['module'])) {
      $optgroup = drupal_ucfirst($layout['module']);
    }

    if (!isset($layout_options[$optgroup])) {
      $layout_options[$optgroup] = array();
    }

    // Stack the layout.
    $layout_options[$optgroup][$key] = $layout['label'];
  }

  // If there is only one $optgroup, move it to the root.
  if (count($layout_options) == 2) {
    $options = $layout_options[$optgroup];
    $layout_options = array_merge(array('' => t('- None -')), $options);
  }

  // Add layouts form.
  $form['ds_layouts'] = array(
    '#type' => 'details',
    '#title' => t('Layout for !bundle in !view_mode', array('!bundle' => str_replace('_', ' ', $bundle), '!view_mode' => str_replace('_', ' ', $view_mode))),
    '#collapsible' => TRUE,
    '#group' => 'additional_settings',
    '#collapsed' => FALSE,
    '#weight' => -100,
  );

  list($ce, $cb, $cv) = explode('.', $form['#export_id']);
  $layout = \Drupal::config('ds.layout_settings.' . $ce . '.' . $cb . '.' . $cv)->get();

  if (!empty($layout) && isset($layout['layout']) && isset($ds_layouts[$layout['layout']])) {
    $layout['regions'] = $ds_layouts[$layout['layout']]['regions'];
    $form['#ds_layout'] = $layout;
  }

  // Load the layout preview form
  $layout['layout_options'] = $layout_options;
  _ds_field_ui_table_layouts_preview($form, $form_state, $ds_layouts, $layout, $entity_type, $bundle, $view_mode);

  if (!empty($layout) && isset($layout['regions'])) {

    // Add wrappers
    $wrapper_options = array('div' => 'Div', 'span' => 'Span', 'section' => 'Section', 'article' => 'Article', 'header' => 'Header', 'footer' => 'Footer', 'aside' => 'Aside');
    $form['region_wrapper'] = array(
      '#group' => 'additional_settings',
      '#type' => 'details',
      '#title' => t('Custom wrappers'),
      '#description' => t('Choose a wrapper. All Display Suite layouts support this option.'),
      '#tree' => TRUE,
    );

    // Hide the fieldset in case of the reset layout.
    if ($layout['layout'] === 'ds_reset') {
      $form['region_wrapper']['#access'] = FALSE;
    }

    foreach (array_keys($layout['regions']) as $region) {
      $form['region_wrapper'][$region] = array(
        '#type' => 'select',
        '#options' => $wrapper_options,
        '#title' => t('Wrapper for @region', array('@region' => $layout['regions'][$region])),
        '#default_value' => isset($layout['settings']['wrappers'], $layout['settings']['wrappers'][$region]) ? $layout['settings']['wrappers'][$region] : 'div',
      );
    }

    $form['region_wrapper']['layout_wrapper'] = array(
      '#type' => 'select',
      '#options' => $wrapper_options,
      '#title' => t('Layout wrapper'),
      '#default_value' => isset($layout['settings']['layout_wrapper']) ? $layout['settings']['layout_wrapper'] : 'div',
      '#weight' => 10,
    );

    $form['region_wrapper']['layout_attributes'] = array(
      '#type' => 'textfield',
      '#title' => t('Layout attributes'),
      '#description' => 'E.g. role|navigation,data-something|some value',
      '#default_value' => isset($layout['settings']['layout_attributes']) ? $layout['settings']['layout_attributes'] : '',
      '#weight' => 11,
    );

    $form['region_wrapper']['layout_attributes_merge'] = array(
      '#type' => 'checkbox',
      '#title' => t('Merge other layout attributes'),
      '#description' => 'Certain modules might provide additional attributes for the template wrapper, e.g. RDFa. Disable this option if you prefer to specify these attributes above.',
      '#default_value' => isset($layout['settings']['layout_attributes_merge']) ? $layout['settings']['layout_attributes_merge'] : settings()->get('ds_layout_attributes_merge', TRUE),
      '#weight' => 12,
    );

    $form['region_wrapper']['layout_link_attribute'] = array(
      '#type' => 'select',
      '#options' => array(
        '' => t('No link'),
        'content' => t('Link to content'),
        'custom' => t('Custom'),
        'tokens' => t('Tokens')
      ),
      '#title' => t('Add link'),
      '#description' => t('This will add an onclick attribute on the layout wrapper.'),
      '#default_value' => isset($layout['settings']['layout_link_attribute']) ? $layout['settings']['layout_link_attribute'] : FALSE,
      '#weight' => 12,
    );

    $form['region_wrapper']['layout_link_custom'] = array(
      '#type' => 'textfield',
      '#title' => t('Custom link'),
      '#description' => t('You may use tokens for this link if you selected tokens.'),
      '#default_value' => isset($layout['settings']['layout_link_custom']) ? $layout['settings']['layout_link_custom'] : FALSE,
      '#weight' => 13,
      '#states' => array(
        'visible' => array(array(
          ':input[name="region_wrapper[layout_link_attribute]"]' => array(array("value" => "tokens"), array("value" => "custom")),
        )),
      ),
    );

    if (\Drupal::moduleHandler()->moduleExists('token')) {
      $form['region_wrapper']['tokens'] = array(
        '#title' => t('Tokens'),
        '#type' => 'container',
        '#weight' => 14,
        '#states' => array(
          'visible' => array(
            ':input[name="region_wrapper[layout_link_attribute]"]' => array("value" => "tokens"),
          ),
        ),
      );
      $form['region_wrapper']['tokens']['help'] = array(
        '#theme' => 'token_tree',
        '#token_types' => 'all',
        '#global_types' => FALSE,
        '#dialog' => TRUE,
      );
    }

    // Add extra classes for the regions to have more control while theming.
    $form['ds_classes'] = array(
      '#group' => 'additional_settings',
      '#type' => 'details',
      '#title' => t('Custom classes'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#access' => empty($chosen_layout['flexible']),
    );

    $classes_access = (\Drupal::currentUser()->hasPermission('admin_classes') && \Drupal::moduleHandler()->moduleExists('ds_ui'));
    $classes = _ds_classes();
    if (!empty($classes)) {

      $form['ds_classes']['layout_class'] = array(
        '#type' => 'select',
        '#multiple' => TRUE,
        '#options' => $classes,
        '#title' => t('Class for layout'),
        '#default_value' => isset($layout['settings']['classes']['layout_class']) ? $layout['settings']['classes']['layout_class'] : '',
      );

      foreach (array_keys($layout['regions']) as $region) {
        $form['ds_classes'][$region] = array(
          '#type' => 'select',
          '#multiple' => TRUE,
          '#options' => $classes,
          '#title' => t('Class for @region', array('@region' => $region)),
          '#default_value' => isset($layout['settings']['classes'][$region]) ? $layout['settings']['classes'][$region] : '',
        );
      }
      if ($classes_access) {
        $form['ds_classes']['info'] = array('#markup' => l(t('Manage region  and field CSS classes'), 'admin/structure/ds/classes', array('query' => drupal_get_destination())));
      }
    }
    else {
      if ($classes_access) {
        $form['ds_classes']['info'] = array('#markup' => '<p>' . t('You have not defined any CSS classes which can be used on regions.') . '</p><p>' . l(t('Manage region and field CSS classes'), 'admin/structure/ds/classes', array('query' => drupal_get_destination())) . '</p>');
      }
      else {
        $form['ds_classes']['#access'] = FALSE;
      }
    }
  }
  else {

    if ($view_mode != 'form') {
      // See if we can clone from another view mode.
      $options = array();
      $ds_layout_settings = config_get_storage_names_with_prefix('ds.layout_settings.' . $entity_type . '.' . $bundle);
      foreach ($ds_layout_settings as $config) {
        $row = \Drupal::config($config)->get();
        // Do not clone from form layouts.
        if ($row['view_mode'] == 'form') {
          continue;
        }
        if ($row['entity_type'] == $entity_type && $row['bundle'] == $bundle) {
          $options[$row['id']] = drupal_ucfirst(str_replace('_', ' ', $row['entity_type'])) . ' > ' . drupal_ucfirst(str_replace('_', ' ', $row['bundle'])) . ' > ' . drupal_ucfirst(str_replace('_', ' ', $row['view_mode']));
        }
      }

      if (!empty($options)) {

        // Clone from another layout.
        $form['ds_clone'] = array(
          '#type' => 'details',
          '#group' => 'additional_settings',
          '#title' => t('Clone layout'),
          '#collapsible' => TRUE,
          '#collapsed' => TRUE,
        );
        $form['ds_clone']['clone'] = array(
          '#title' => t('Select an existing layout to clone.'),
          '#type' => 'select',
          '#options' => $options,
          '#weight' => 20,
        );
        $form['ds_clone']['clone_submit'] = array(
          '#type' => 'submit',
          '#value' => t('Clone layout'),
          '#submit' => array('ds_field_ui_layout_clone'),
          '#weight' => 21,
        );
      }
    }
  }

  $form['ds_layouts']['id'] = array(
    '#type' => 'value',
    '#value' => isset($layout['id']) ? $layout['id'] : $form['#export_id'],
  );

  $form['ds_layouts']['old_layout'] = array(
    '#type' => 'value',
    '#value' => isset($layout['layout']) ? $layout['layout'] : 0,
  );

  // Add validate and submit handlers. Layout needs be first so
  // we can reset the type key for Field API fields.
  $form['#validate'][] = 'ds_field_ui_layouts_validate';
  $submit = $form['#submit'];
  $form['#submit'] = array('ds_field_ui_layouts_save');
  $form['#submit'] = array_merge($form['#submit'], $submit);
}

/**
 * Add the layout previews to the Field UI screen.
 *
 * @param $form
 *   A collection of form properties.
 * @param $form_state
 *   The state of the form
 * @param $ds_layouts
 *   Collection of all the layouts
 * @param $layout
 *   Current selected layout
 * @param $entity_type
 *   The name of the entity type.
 * @param $bundle
 *   The name of the bundle
 * @param $view_mode
 *   The name of the view_mode
 */
function _ds_field_ui_table_layouts_preview(&$form, &$form_state, $ds_layouts, $layout, $entity_type, $bundle, $view_mode) {
  $layout_string = '';

  $form['ds_layouts']['layout'] = array(
    '#type' => 'select',
    '#title' => t('Select a layout'),
    '#options' => $layout['layout_options'],
    '#default_value' => isset($layout['layout']) ? $layout['layout'] : '',
    '#prefix' => '<div class="ds-select-layout">',
    '#suffix' => '</div>',
    '#weight' => -1,
    '#ajax' => array(
      'callback' => 'ds_field_ui_table_layouts_preview_callback',
      'wrapper' => 'ds_layout_wrapper',
    ),
  );

  if (!isset($layout->layout)) {
    $form['ds_layouts']['layout']['#description'] = t("A layout must be selected to enable Display Suite functionality.");
  }

  $form['ds_layouts']['preview'] = array(
    '#type' => 'container',
    '#prefix' => '<div id="ds_layout_wrapper">',
    '#suffix' => '</div>',
    '#weight' => -3,
  );

  if (isset($layout['layout']) || isset($form_state['values']['layout'])) {
    $layout_string = isset($form_state['values']['layout']) ? $form_state['values']['layout'] : $layout['layout'];
  }

  if (!empty($layout_string)) {
    $chosen_layout = $ds_layouts[$layout_string];

    if (empty($chosen_layout['flexible'])) {

      $selected = '<strong>' . $chosen_layout['label'] . '</strong>';
      $selected .= '<br/>' . t('The default template can be found in %path', array('%path' => $chosen_layout['path']));
      $suggestions_array = array();
      $suggestions_array[0] = $layout_string . '--' . $entity_type;
      $suggestions_array[2] = $layout_string . '--' . $entity_type . '-' . $bundle;
      if (!isset($form_state['no_view_mode_suggestions']) && $view_mode != 'default') {
        $suggestions_array[1] = $layout_string . '--' . $entity_type . '-' . $view_mode;
        $suggestions_array[3] = $layout_string . '--' . $entity_type . '-' . $bundle . '-' . $view_mode;
      }

      ksort($suggestions_array);
      $suggestions = t('Template suggestions') . ':<ul>';
      $suggestions .= '<ul><li>' . implode('.html.twig</li><li>', $suggestions_array) . '.html.twig</li></ul>';
    }
    else {
      $suggestions = '';
      $selected = t('You have selected the flexible %layout_label layout.', array('%layout_label' => $chosen_layout['label'], '%path' => $chosen_layout['path']));
    }

    if (isset($form_state['values']['layout']) || (!empty($layout) && isset($layout['regions']))) {
      $fallback_image = drupal_get_path('module', 'ds') . '/images/preview.png';
      $current_layout = isset($form_state['values']['layout']) && (!isset($layout->layout) || $form_state['values']['layout'] != $layout->layout) ? t('Current layout (after save)') : t('Current layout');
      $image = (isset($chosen_layout['image']) && !empty($chosen_layout['image'])) ? $chosen_layout['path'] . '/' . $layout_string . '.png' : $fallback_image;

      $form['ds_layouts']['preview'] ['title'] = array(
        '#markup' => '<div class="ds-layout-preview-title">' . $current_layout . '</div>',
      );
      $form['ds_layouts']['preview'] ['image'] = array(
        '#markup' => '<div class="ds-layout-preview-image"><img src="' . base_path() . $image . '"/></div>',
      );
      $form['ds_layouts']['preview'] ['suggestions'] = array(
        '#markup' => '<div class="ds-layout-preview-suggestion"><p>' . $selected . '</p><p>' . t('!suggestions', array('!suggestions' => strtr($suggestions, '_', '-'))) . '</p></div>',
      );
    }

    if (isset($form_state['values']['layout']) && (!isset($layout['layout']) || $form_state['values']['layout'] != $layout['layout'])) {
      // Get admin path.
      $route = FieldUI::getOverviewRouteInfo($entity_type, $bundle);
      $route_parameters = $route['route_parameters'];
      $options = $route['options'];

      if ($view_mode != 'form') {
        $route_name = 'field_ui.display_overview_' . $entity_type;

      }
      else {
        $route_name = 'field_ui.form_display_overview_' . $entity_type;
      }

      $admin_path = \Drupal::service('url_generator')->generateFromRoute($route_name, $route_parameters, $options);
      if ($view_mode != 'default' && $view_mode != 'form') {
        $admin_path .= '/' . $view_mode;
      }

      $destination_url = '';

      // If regions aren't set we don't have to move fields.
      if (isset($layout['regions'])) {
        $route_name = 'ds.change_layout';
        $route_parameters = array(
          'entity_type' => $entity_type,
          'bundle' => $bundle,
          'display_mode' => $view_mode,
          'new_layout' => $layout_string,
        );
        $options = array();
        $destination_url = $admin_path;

        $form['#validate'][] = 'ds_field_ui_change_layout_validate';
      }


      $form['layout_changed_url'] = array(
        '#type' => 'value',
        '#value' => array(
          'route_name' => $route_name,
          'route_parameters' => $route_parameters,
          'destination_url' => $destination_url,
          'options' => $options,
        ),
      );

      $form['#submit'][] = 'ds_field_ui_change_layout_submit';
    }
  }
}

/**
 * Ajax callback for _ds_field_ui_table_layouts_preview().
 */
function ds_field_ui_table_layouts_preview_callback($form, $form_state) {
  return $form['ds_layouts']['preview'];
}

/**
 * Form validation handler for _ds_field_ui_table_layouts_preview().
 */
function ds_field_ui_change_layout_validate(&$form, &$form_state) {
  $key1 = array_search('ds_field_ui_layouts_save', $form['#submit']);
  $key2 = array_search('ds_field_ui_fields_save', $form['#submit']);
  unset($form['#submit'][$key1]);
  unset($form['#submit'][$key2]);
}

/**
 * Form submission handler for _ds_field_ui_table_layouts_preview().
 */
function ds_field_ui_change_layout_submit($form, &$form_state) {
  // Remove original destination
  \Drupal::request()->query->remove('destination');

  $destination = $form_state['values']['layout_changed_url'];

  $form_state['redirect_route'] = array(
    'route_name' => $destination['route_name'],
    'route_parameters' => $destination['route_parameters'],
    'options' => $destination['options'],
  );

  if (!empty($destination['destination_url'])) {
    $form_state['redirect_route']['options']['query']['destination'] = $destination['destination_url'];
  }
}

/**
 * Add the fields to the Field UI form.
 *
 * @param $entity_type
 *   The name of the entity type.
 * @param $bundle
 *   The name of the bundle
 * @param $view_mode
 *   The name of the view_mode
 * @param $form
 *   A collection of form properties.
 * @param $form_state
 *   A collection of form_state properties.
 */
function _ds_field_ui_fields($entity_type, $bundle, $view_mode, &$form, &$form_state) {

  // Do not add the fields if there is no layout.
  if (!isset($form['#ds_layout'])) {
    return;
  }

  // Get the fields and put them on the form.
  $fields = Ds::getFields($entity_type);

  // Ultimate alter on Field UI fields, only used for edge cases.
  $context = array(
    'entity_type' => $entity_type,
    'bundle' => $bundle,
    'view_mode' => $view_mode,
  );

  \Drupal::moduleHandler()->alter('ds_fields_ui', $fields, $context);

  // Get field settings.
  $field_settings = Ds::getFieldSettings($entity_type, $bundle, $view_mode, FALSE);
  $form['#field_settings'] = $field_settings;

  $table = &$form['fields'];
  $form['#ds_fields'] = array();

  $field_label_options = array(
    'above' => t('Above'),
    'inline' => t('Inline'),
    'hidden' => t('- Hidden -'),
  );
  \Drupal::moduleHandler()->alter('ds_label_options', $field_label_options);

  // Regions for fields.
  $field_regions = array();
  if (isset($form['#ds_layout']['settings']['fields'])) {
    $field_regions = $form['#ds_layout']['settings']['fields'];
  }

  foreach ($fields as $key => $field) {
    $configuration = array(
      'field' => $field,
      'field_name' => $key,
      'entity_type' => $entity_type,
      'bundle' => $bundle,
      'view_mode' => $view_mode,
    );

    // Check if we can display this field here.
    /** @var $plugin_instance \Drupal\ds\Plugin\DsField\DsFieldInterface */
    $plugin_instance = \Drupal::service('plugin.manager.ds')->createInstance($field['plugin_id'], $configuration);

    if (!$plugin_instance->isAllowed($bundle, $view_mode)) {
      continue;
    }

    // Don't filter out fields when $displays is empty
    if (!empty($displays)) {
      $continue = TRUE;
      foreach ($displays as $limitation) {
        list($limit_bundle, $limit_view_mode) = explode('|', $limitation);
        if ($limit_bundle == '*' || $limit_bundle == $bundle) {
          if ($limit_view_mode == '*' || $limit_view_mode == $view_mode) {
            $continue = FALSE;
          }
        }
      }

      if ($continue) {
        continue;
      }
    }

    $form['#ds_fields'][] = $key;

    // Check on formatter settings.
    $plugin_settings = array();
    if (isset($form_state['plugin_settings'][$key])) {
      $plugin_settings = $form_state['plugin_settings'][$key];
    }
    elseif (isset($field_settings[$key]['plugin_settings'])) {
      $plugin_settings = $field_settings[$key]['plugin_settings'];
      $form_state['plugin_settings'][$key] = $plugin_settings;
    }

    $plugin_instance->setConfiguration($plugin_settings);

    if (!isset($field_settings[$key]['ft']) && isset($field_settings[$key]['ft'])) {
      $form_state['plugin_settings'][$key]['ft'] = $field_settings[$key]['ft'];
    }

    $hidden = array('hidden' => t('- Hidden -'));

    // Get the formatters from the field instance
    $formatters = $plugin_instance->formatters();

    // This should be temporary. Don't want to copy stuff from the object to
    // the field each ajax refresh.
    if (!empty($formatters)) {
      $formatters = $hidden + $formatters;
    }
    else {
      $formatters = $hidden + array('default' => t('Default'));
    }

    $table[$key] = array(
      '#row_type' => 'field',
      '#js_settings' => array('field'),
      '#region_callback' => 'field_ui_display_overview_row_region',
      '#attributes' => array('class' => array('draggable', 'tabledrag-leaf')),
      'human_name' => array(
        '#markup' => String::checkPlain($field['title']),
      ),
      'weight' => array(
        '#type' => 'textfield',
        '#default_value' => isset($field_settings[$key]['weight']) ? $field_settings[$key]['weight'] : 0,
        '#size' => 3,
        '#attributes' => array('class' => array('field-weight')),
      ),
      'parent_wrapper' => array(
        'parent' => array(
          '#type' => 'select',
          '#empty_value' => '',
          '#options' => array(),
          '#attributes' => array('class' => array('field-parent')),
          '#parents' => array('fields', $key, 'parent'),
        ),
        'hidden_name' => array(
          '#type' => 'hidden',
          '#default_value' => $key,
          '#attributes' => array('class' => array('field-name')),
        ),
      ),
      'label' => array(
        '#type' => 'select',
        '#options' => $field_label_options,
        '#default_value' => isset($field_settings[$key]['label']) ? $field_settings[$key]['label'] : 'hidden',
      ),
      'plugin' => array(
        'type' => array(
          '#type' => 'select',
          '#options' => $formatters,
          '#default_value' => isset($field_settings[$key]['formatter']) ? $field_settings[$key]['formatter'] : 'hidden',
          '#attributes' => array('class' => array('field-plugin-type')),
        ),
      ),
      'settings_summary' => array(),
      'settings_edit' => array(),
    );

    // Don't show summary or cogwheel in hidden region.
    if (_ds_field_ui_check_hidden_region($key, $form_state, $field_regions)) {
      continue;
    }

    if ($form_state['plugin_settings_edit'] == $key) {
      $table[$key]['settings_summary']['#attributes']['colspan'] = 2;
      $settings_form = ds_field_settings_form($plugin_instance, $form_state);
      ds_field_row_form_format_construct($table, $key, $settings_form, $form_state);
    }
    else {
      // After saving, the settings are updated here as well. First we create
      // the element for the table cell.
      $summary = ds_field_settings_summary($plugin_instance, $plugin_settings, $form_state);

      if (!empty($summary)) {
        $table[$key]['settings_summary'] = $summary;
        ds_field_row_form_format_summary_construct($table, $key, $form_state);
      }
    }
  }

  // Add fields submit handler.
  $form['#submit'][] = 'ds_field_ui_fields_save';
}

/**
 * Alter the core field on the the Field UI form.
 *
 * @param $form
 *   A collection of form properties.
 * @param $form_state
 *   A collection of form_state properties.
 */
function _ds_field_ui_core_fields(&$form, &$form_state) {
  $entity_type = $form['#entity_type'];
  $bundle = $form['#bundle'];
  $view_mode = $form['#mode'];
  $entity_display = entity_get_display($entity_type, $bundle, $view_mode);

  // Gather type information.
  $instances = Field::fieldInfo()->getBundleInstances($entity_type, $bundle);

  $table = &$form['fields'];

  // Regions for fields.
  $field_regions = array();
  if (isset($form['#ds_layout']['settings']['fields'])) {
    $field_regions = $form['#ds_layout']['settings']['fields'];
  }

  // Field rows.
  foreach ($instances as $key => $instance) {

    // Don't show summary or cogwheel in hidden region.
    if (_ds_field_ui_check_hidden_region($key, $form_state, $field_regions)) {
      $table[$key]['settings_summary']['#markup'] = '';
      $table[$key]['settings_edit'] = array();
      continue;
    }

    $field = Field::fieldInfo()->getField($entity_type, $instance->getName());
    $display = entity_get_display($entity_type, $bundle, $view_mode);
    $component = $display->getComponent($key);

    // Check the currently selected formatter, and merge persisted values for
    // formatter settings.
    if (isset($form_state['values']['fields'][$key]['type'])) {
      $formatter_type = $form_state['values']['fields'][$key]['type'];
    }
    else {
      $formatter_type = $component['type'];
    }

    $settings = $entity_display->getComponent($key);
    if (isset($form_state['plugin_settings'][$key])) {
      $settings = array_merge($settings, $form_state['plugin_settings'][$key]);
    }
    $settings += \Drupal::service('plugin.manager.field.formatter')->getDefaultSettings($formatter_type);

    // Import field settings and merge with Field API settings.
    if (!isset($form_state['plugin_settings'][$key]) && !empty($form['#field_settings'][$key]['plugin_settings']['ft'])) {
      $form_state['plugin_settings'][$key] = $settings;
      $form_state['plugin_settings'][$key]['ft'] = $form['#field_settings'][$key]['plugin_settings']['ft'];
    }

    // Change default value or Field API format, so we can change the right
    // settings form when clicking on the cogwheel.
    $form['fields'][$key]['plugin']['type']['#default_value'] = $formatter_type;

    $instance->display[$view_mode]['type'] = $formatter_type;
    $instance->display[$view_mode]['settings'] = $settings;

    // Base button element for the various formatter settings actions.
    $base_button = array(
      '#submit' => array(
        array($form_state['build_info']['callback_object'], 'multistepSubmit'),
      ),
      '#ajax' => array(
        'callback' => array($form_state['build_info']['callback_object'], 'multistepAjax'),
        'wrapper' => 'field-display-overview-wrapper',
        'effect' => 'fade',
      ),
      '#field_name' => $key,
    );

    if ($form_state['plugin_settings_edit'] == $key) {

      // Get the corresponding formatter object.
      if ($settings && $settings['type'] != 'hidden') {
        $options = array(
          'field_definition' => $instance,
          'configuration' => array(
            'type' => $settings['type'],
            'settings' => $settings['settings'],
            'label' => $settings['label'],
            'weight' => $settings['weight'],
          ),
          'view_mode' => $view_mode,
        );

        $formatter = \Drupal::service('plugin.manager.field.formatter')->getInstance($options);
      }
      else {
        $formatter = NULL;
      }

      // Add the default formatter settings if any.
      $settings_form = array();
      if (isset($formatter)) {
        $settings_form = $formatter->settingsForm($form, $form_state);
      }

      // Add the field templates form when needed
      if (\Drupal::config('ds.settings')->get('field_template')) {
        $context = array(
          'instance' => $instance,
          'view_mode' => $view_mode,
        );

        ds_field_template_settings_form($settings_form, $form_state, $context);
      }

      // Allow other modules to alter the formatter settings form.
      // @todo check whether we can't make this cleaner, because we're
      // calling this twice now.
      $context = array(
        'formatter' => $formatter,
        'field' => $field,
        'instance' => $instance,
        'view_mode' => $view_mode,
        'form' => $form,
      );
      \Drupal::moduleHandler()->alter('field_formatter_settings_form', $settings_form, $form_state, $context);

      if ($settings_form) {
        $table[$key]['plugin']['#cell_attributes'] = array('colspan' => 3);
        $table[$key]['plugin']['settings_edit_form'] = array(
          '#type' => 'container',
          '#attributes' => array('class' => array('field-plugin-settings-edit-form')),
          '#parents' => array('fields', $key, 'settings_edit_form'),
          'label' => array(
            '#markup' => t('Format settings:'), //. ' <span class="formatter-name">' . $settings['label'] . '</span>',
          ),
          'settings' => $settings_form,
          'actions' => array(
            '#type' => 'actions',
            'save_settings' => $base_button + array(
              '#type' => 'submit',
              '#name' => $key . '_plugin_settings_update',
              '#value' => t('Update'),
              '#op' => 'update',
            ),
            'cancel_settings' => $base_button + array(
              '#type' => 'submit',
              '#name' => $key . '_plugin_settings_cancel',
              '#value' => t('Cancel'),
              '#op' => 'cancel',
              // Do not check errors for the 'Cancel' button, but make sure we
              // get the value of the 'formatter type' select.
              '#limit_validation_errors' => array(array('fields', $key, 'type')),
            ),
          ),
        );
        $table[$key]['#attributes']['class'][] = 'field-plugin-settings-editing';
        $table[$key]['plugin']['type']['#attributes']['class'] = array('visually-hidden');
      }
    }
    else {
      if (!empty($table[$key]['settings_summary'])) {
        $table[$key]['settings_edit'] = array();

        ds_field_row_form_format_summary_construct($table, $key, $form_state);
      }
    }
  }
}

/**
 * Helper function to check if we are in a hidden region or not.
 */
function _ds_field_ui_check_hidden_region($key, $form_state, $field_regions) {
  $continue = FALSE;
  if (isset($form_state['input']['fields'][$key]['region'])) {
    if ($form_state['input']['fields'][$key]['region'] == 'hidden') {
      $continue = TRUE;
    }
  }
  elseif (!isset($field_regions[$key]) || $field_regions[$key] == 'hidden') {
    $continue = TRUE;
  }

  return $continue;
}

/**
 * Helper function for building the formatter settings.
 */
function ds_field_row_form_format_construct(&$table, $key, $settings_form, $form_state) {

  $base_button = array(
    '#submit' => array(
      array($form_state['build_info']['callback_object'], 'multistepSubmit'),
    ),
    '#validate' => array(
      'ds_field_ui_fields_validate',
    ),
    '#ajax' => array(
      'callback' => array($form_state['build_info']['callback_object'], 'multistepAjax'),
      'wrapper' => 'field-display-overview-wrapper',
      'effect' => 'fade',
    ),
    '#field_name' => $key,
  );
  $table[$key]['plugin']['settings_edit'] = array(
    '#type' => 'container',
    '#attributes' => array('class' => array('field-plugin-settings-edit-form')),
    '#parents' => array('fields', $key, 'settings_edit_form'),
    '#weight' => -5,
    // Create a settings form where hooks can pick in.
    'settings' => $settings_form,
    'actions' => array(
      '#type' => 'actions',
      'save_settings' => $base_button + array(
        '#type' => 'submit',
        '#name' => $key . '_plugin_settings_update',
        '#value' => t('Update'),
        '#op' => 'update',
      ),
      'cancel_settings' => $base_button + array(
        '#type' => 'submit',
        '#name' => $key . '_plugin_settings_cancel',
        '#value' => t('Cancel'),
        '#op' => 'cancel',
        // Do not check errors for the 'Cancel' button.
        '#limit_validation_errors' => array(),
      ),
    ),
  );
  $table[$key]['#attributes']['class'][] = 'field-plugin-settings-editing';
  $table[$key]['plugin']['type']['#attributes']['class'] = array('visually-hidden');
}

/**
 * Helper function for formatter summary settings.
 */
function ds_field_row_form_format_summary_construct(&$table, $key, $form_state) {

  $base_button = array(
    '#submit' => array(
      array($form_state['build_info']['callback_object'], 'multistepSubmit'),
    ),
    '#ajax' => array(
      'callback' => array($form_state['build_info']['callback_object'], 'multistepAjax'),
      'wrapper' => 'field-display-overview-wrapper',
      'effect' => 'fade',
    ),
    '#field_name' => $key,
  );

  // Add the configure button.
  $table[$key]['settings_edit'] = $base_button + array(
    '#type' => 'image_button',
    '#name' => $key . '_plugin_settings_edit',
    '#src' => 'core/misc/configure-dark.png',
    '#attributes' => array('class' => array('field-plugin-settings-edit'), 'alt' => t('Edit')),
    '#op' => 'edit',
    // Do not check errors for the 'Edit' button.
    '#limit_validation_errors' => array(),
    '#prefix' => '<div class="field-plugin-settings-edit-wrapper">',
    '#suffix' => '</div>',
  );
}

/**
 * Utility function to check if we need to save anything for this field.
 */
function _ds_field_valid($key, $field, &$form_state, $view_mode = 'default') {
  $continue = FALSE;

  // Ignore the Field group module and the region to block plugin.
  if ($key == '_add_new_group' || $key == '_add_new_field' || $key == '_add_new_block_region') {
    $continue = TRUE;
  }

  // If the field is in hidden region, do not save. Check if the
  // field has a type key which means it's from Field API and
  // we need to reset that type to 'hidden' so it doesn't get
  // fired by Field API in the frontend.
  if (isset($field['region']) && $field['region'] == 'hidden') {
    if (isset($field['type']) && $view_mode != 'form') {
      $form_state['values']['fields'][$key]['type'] = 'hidden';
    }

    // In case of a form, fields will be set with #access to FALSE.
    if ($view_mode != 'form') {
      $continue = TRUE;
    }
  }

  return $continue;
}

/**
 * Utility function to return CSS classes.
 */
function _ds_classes($name = 'region') {
  static $classes = array();

  if (!isset($classes[$name])) {
    $classes[$name] = array();
    $custom_classes = \Drupal::config('ds.settings')->get('classes.' . $name);
    if (!empty($custom_classes)) {
      $classes[$name][''] = t('None');
      foreach ($custom_classes as $value) {
        $classes_splitted = explode("|", $value);
        $key = trim($classes_splitted[0]);
        $friendly_name = isset($classes_splitted[1]) ? trim($classes_splitted[1]) : $key;
        $classes[$name][String::checkPlain($key)] = $friendly_name;
      }
    }
    $name_clone = $name; // Prevent the name from being changed.
    \Drupal::moduleHandler()->alter('ds_classes', $classes[$name], $name_clone);
  }

  return $classes[$name];
}

/**
 * Utility function to sort a multidimensional array by a value in a sub-array.
 *
 * @param $a
 *   The array to sort.
 * @param $subkey
 *   The subkey to sort by.
 *
 * @return array
 *   The sorted array.
 */
function _ds_sort_fields($a, $subkey) {
  $c = array();
  foreach ($a as $k => $v) {
    if (isset($v[$subkey])) {
      $b[$k] = $v[$subkey];
    }
  }
  asort($b);
  foreach ($b as $key => $val) {
    $c[$key] = $a[$key];
  }
  return $c;
}
