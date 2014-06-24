<?php
/**
 * @file
 * Registry file for Display Suite.
 */

use Drupal\ds\Ds;

/**
 * Implements hook_theme().
 */
function _ds_theme() {
  $theme_functions = array();

  // Layouts
  $layouts = Ds::getLayouts();
  foreach ($layouts as $key => $layout) {

    $theme_functions[$key] = array(
      'render element' => 'elements',
      'template' => strtr($key, '_', '-'),
      'path' => $layout['path'],
    );
  }

  // Field templates
  if (\Drupal::config('ds.settings')->get('field_template')) {
    $field_functions = \Drupal::moduleHandler()->invokeAll('ds_field_theme_functions_info');
    foreach ($field_functions as $key => $label) {
      $theme_functions[$key] = array(
        'render element' => 'element',
        'function' => $key,
        'base hook' => 'field',
      );
    }
  }

  return $theme_functions;
}

/**
 * Implements hook_module_implements_alter().
 */
function _ds_module_implements_alter(&$implementations, $hook) {

  // node_entity_display_alter() disables all labels on all fields
  // when the view mode is 'search_index'. If you set display modes for
  // this view mode by hand, then the hook isn't needed. Since this
  // may be called dozens of times on some pages, it's worth disabling it.
  if ($hook == 'entity_display_alter') {
    unset($implementations['node']);
  }

  // Because it's possible to turn on/off field templates,
  // we'll unset hooks here so ds_preprocess_field() doesn't run.
  $ft_hooks = array(
    'ds_field_settings_alter',
    'form_ds_classes_form_alter',
    'form_field_ui_field_edit_form_alter',
  );
  if (!\Drupal::config('ds.settings')->get('field_template') && in_array($hook, $ft_hooks)) {
    unset($implementations['ds']);
  }
}

/**
 * Remove or rename layout & field settings on entity machine name update.
 *
 * @param $entity_type
 *   The name of the entity type.
 * @param $info
 *   The entity info.
 * @param $action
 *   The action, either update or delete.
 */
function _ds_entity_type_update($entity_type, $info, $action) {

  // Delete settings.
  if ($action == 'delete') {
    $layout_settings = config_get_storage_names_with_prefix('ds.layout_settings.' . $entity_type . '.' . $info->type);
    foreach ($layout_settings as $config) {
      \Drupal::config($config)->delete();
    }

    $field_settings = config_get_storage_names_with_prefix('ds.field_settings.' . $entity_type . '.' . $info->type);
    foreach ($field_settings as $config) {
      \Drupal::config($config)->delete();
    }
  }

  // Update settings.
  if ($action == 'update') {
    $layout_settings = config_get_storage_names_with_prefix('ds.layout_settings.' . $entity_type . '.' . $info->old_type);
    foreach ($layout_settings as $config) {
      $new_id = $entity_type . '.' . $info->type . '.' . $config['view_mode'];
      if (\Drupal::config($config)->rename($new_id)) {
        $new_config = \Drupal::config($new_id)->get();
        \Drupal::config($new_config)->set('id', $new_id)->set('bundle', $info->type)->save();
      }
    }

    $field_settings = config_get_storage_names_with_prefix('ds.field_settings.' . $entity_type . '.' . $info->old_type);
    foreach ($field_settings as $config) {
      $new_id = $entity_type . '.' . $info->type . '.' . $config['view_mode'];
      if (\Drupal::config($config)->rename($new_id)) {
        $new_config = Drupal::config($new_id)->get();
        \Drupal::config($new_config)->set('id', $new_id)->set('bundle', $info->type)->save();
      }
    }
  }

  // Clear cache.
  cache()->invalidateTags(array('ds_fields_info' => TRUE));
  cache()->invalidateTags(array('ds_field_settings' => TRUE));
  field_info_cache_clear();
}

/**
 * Implements hook_theme_registry_alter().
 */
function _ds_theme_registry_alter(&$theme_registry) {

  $layouts = Ds::getLayouts();
  foreach ($layouts as $key => $info) {
    // Only add preprocess functions if entity exposes theme function.
    if (isset($theme_registry[$key])) {
      $theme_registry[$key]['preprocess functions'][] = 'ds_preprocess_ds_layout';
    }
  }

  // Remove ds_preprocess_field in case field templates is not enabled.
  if (!\Drupal::config('ds.settings')->get('field_template')) {
    $key = array_search('ds_preprocess_field', $theme_registry['field']['preprocess functions']);
    if (!empty($key)) {
      unset($theme_registry['field']['preprocess functions'][$key]);
    }
  }
}

/**
 * Implements ds_entity_bundle_info_alter().
 */
function _ds_entity_bundle_info_alter(&$bundles) {

  // Add layout if configured to bundles.
  $ds_layouts = Ds::getLayouts();
  $ds_layout_settings = config_get_storage_names_with_prefix('ds.layout_settings');
  foreach ($ds_layout_settings as $config) {
    $ds_layout_setting = \Drupal::config($config)->get();

    // Don't store any configurations with layouts that don't exist anymore.
    if (!isset($ds_layouts[$ds_layout_setting['layout']])) {
      continue;
    }

    // Make sure the entity type exists and has bundles defined.
    if (!isset($bundles[$ds_layout_setting['entity_type']]) || !isset($bundles[$ds_layout_setting['entity_type']][$ds_layout_setting['bundle']])) {
      continue;
    }

    $layout = $ds_layouts[$ds_layout_setting['layout']];
    $layout['layout'] = $ds_layout_setting['layout'];
    $layout['settings'] = $ds_layout_setting['settings'];
    $bundles[$ds_layout_setting['entity_type']][$ds_layout_setting['bundle']]['layouts'][$ds_layout_setting['view_mode']] = $layout;
  }
}

/**
 * Implements hook_ds_layout_info().
 */
function _ds_ds_layout_info() {
  $path = drupal_get_path('module', 'ds');
  $layouts = array(
    'ds_1col' => array(
      'label' => t('One column'),
      'path' => $path . '/layouts/ds_1col',
      'regions' => array(
        'ds_content' => t('Content'),
      ),
      'image' => TRUE,
    ),
    'ds_1col_wrapper' => array(
       'label' => t('One column + wrapper'),
        'path' => $path . '/layouts/ds_1col_wrapper',
        'regions' => array(
        'ds_content' => t('Content'),
      ),
      'image' => TRUE,
    ),
    'ds_2col' => array(
      'label' => t('Two column'),
      'path' => $path . '/layouts/ds_2col',
      'regions' => array(
        'left' => t('Left'),
        'right' => t('Right')
      ),
      'css' => TRUE,
      'image' => TRUE,
    ),
    'ds_2col_fluid' => array(
      'label' => t('Fluid two column'),
      'path' => $path . '/layouts/ds_2col_fluid',
      'regions' => array(
        'left' => t('Left'),
        'right' => t('Right')
      ),
      'css' => TRUE,
      'image' => TRUE,
    ),
    'ds_2col_stacked' => array(
      'label' => t('Two column stacked'),
      'path' => $path . '/layouts/ds_2col_stacked',
      'regions' => array(
        'header' => t('Header'),
        'left' => t('Left'),
        'right' => t('Right'),
        'footer' => t('Footer'),
      ),
      'css' => TRUE,
      'image' => TRUE,
    ),
    'ds_2col_stacked_fluid' => array(
      'label' => t('Fluid two column stacked'),
      'path' => $path . '/layouts/ds_2col_stacked_fluid',
      'regions' => array(
        'header' => t('Header'),
        'left' => t('Left'),
        'right' => t('Right'),
        'footer' => t('Footer'),
      ),
      'css' => TRUE,
      'image' => TRUE,
    ),
    'ds_3col' => array(
      'label' => t('Three column - 25/50/25'),
      'path' => $path . '/layouts/ds_3col',
      'regions' => array(
        'left' => t('Left'),
        'middle' => t('Middle'),
        'right' => t('Right'),
      ),
      'css' => TRUE,
      'image' => TRUE,
    ),
    'ds_3col_equal_width' => array(
      'label' => t('Three column - equal width'),
      'path' => $path . '/layouts/ds_3col_equal_width',
      'regions' => array(
        'left' => t('Left'),
        'middle' => t('Middle'),
        'right' => t('Right'),
      ),
      'css' => TRUE,
      'image' => TRUE,
    ),
    'ds_3col_stacked' => array(
      'label' => t('Three column stacked - 25/50/25'),
      'path' => $path . '/layouts/ds_3col_stacked',
      'regions' => array(
        'header' => t('Header'),
        'left' => t('Left'),
        'middle' => t('Middle'),
        'right' => t('Right'),
        'footer' => t('Footer'),
      ),
      'css' => TRUE,
      'image' => TRUE,
    ),
    'ds_3col_stacked_fluid' => array(
      'label' => t('Fluid three column stacked - 25/50/25'),
      'path' => $path . '/layouts/ds_3col_stacked_fluid',
      'regions' => array(
        'header' => t('Header'),
        'left' => t('Left'),
        'middle' => t('Middle'),
        'right' => t('Right'),
        'footer' => t('Footer'),
      ),
      'css' => TRUE,
      'image' => TRUE,
    ),
    'ds_3col_stacked_equal_width' => array(
      'label' => t('Three column stacked - equal width'),
      'path' => $path . '/layouts/ds_3col_stacked_equal_width',
      'regions' => array(
        'header' => t('Header'),
        'left' => t('Left'),
        'middle' => t('Middle'),
        'right' => t('Right'),
        'footer' => t('Footer'),
      ),
      'css' => TRUE,
      'image' => TRUE,
    ),
    'ds_4col' => array(
      'label' => t('Four column - equal width'),
      'path' => $path . '/layouts/ds_4col',
      'regions' => array(
        'first' => t('First'),
        'second' => t('Second'),
        'third' => t('Third'),
        'fourth' => t('Fourth'),
      ),
      'css' => TRUE,
      'image' => TRUE,
    ),
    'ds_reset' => array(
      'label' => t('Reset'),
      'path' => $path . '/layouts/ds_reset',
      'regions' => array(
        'ds_content' => t('Content'),
      ),
      'image' => TRUE,
    ),
  );

  // Get layouts defined in the active theme and base theme (if applicable).
  $themes = list_themes();
  $theme = \Drupal::config('system.theme')->get('default');

  $base_theme = array();
  $ancestor = $theme;
  while ($ancestor && isset($themes[$ancestor]->base_theme)) {
    $ancestor = $themes[$ancestor]->base_theme;
    $base_theme[] = $themes[$ancestor];
  }

  foreach (array_reverse($base_theme) as $base) {
    _ds_layouts_scan_theme($base->name, $layouts);
  }
  _ds_layouts_scan_theme($theme, $layouts);

  return $layouts;
}

function _ds_layouts_scan_theme($theme, &$layouts) {
  $theme_layouts = file_scan_directory(drupal_get_path('theme', $theme) . '/ds_layouts', '/inc$/');
  foreach ($theme_layouts as $file => $values) {
    include_once(DRUPAL_ROOT . '/' . $file);
    $function = 'ds_' . $values->name;
    $layouts[$values->name] = $function();
    $layouts[$values->name]['path'] = str_replace('/' . $values->filename, '', $file);
  }
}
