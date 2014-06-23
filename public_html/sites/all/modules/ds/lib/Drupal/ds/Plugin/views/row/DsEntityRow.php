<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\views\row\DsEntityRow.
 */

namespace Drupal\ds\Plugin\views\row;

use Drupal\Component\Utility\String;
use Drupal\views\Plugin\views\row\EntityRow;

/**
 * Generic entity row plugin to provide a common base for all entity types.
 *
 * @ViewsRow(
 *   id = "ds_entity",
 *   derivative = "Drupal\ds\Plugin\Derivative\DsEntityRow"
 * )
 */
class DsEntityRow extends EntityRow {

  /**
   * Contains an array of render arrays, one for each rendered entity.
   *
   * @var array
   */
  protected $build = array();

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['alternating_fieldset'] = array(
      'contains' => array(
        'alternating' => array('default' => FALSE, 'bool' => TRUE),
        'allpages' => array('default' => FALSE, 'bool' => TRUE),
        'item' => array(
          'default' => array(),
        ),
      ),
    );
    $options['grouping_fieldset'] = array(
      'contains' => array(
        'group' => array('default' => FALSE, 'bool' => TRUE),
        'group_field' => array('default' => ''),
        'group_field_function' => array('default' => ''),
      ),
    );
    $options['advanced_fieldset'] = array(
      'contains' => array(
        'advanced' => array('default' => FALSE, 'bool' => TRUE),
      ),
    );
    $options['switch_fieldset'] = array(
      'contains' => array(
        'switch' => array('default' => FALSE, 'bool' => TRUE),
      ),
    );
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, &$form_state) {
    parent::buildOptionsForm($form, $form_state);

    // Use view mode of display settings.
    if ($this->entityType == 'node' && \Drupal::config('ds.extras')->get('switch_view_mode')) {
      $form['switch_fieldset'] = array(
        '#type' => 'details',
        '#title' => t('Use view mode of display settings'),
        '#collapsible' => TRUE,
        '#collapsed' => !$this->options['switch_fieldset']['switch'],
        );
      $form['switch_fieldset']['switch'] = array(
        '#type' => 'checkbox',
        '#title' => t('Use view mode of display settings'),
        '#default_value' => $this->options['switch_fieldset']['switch'],
        '#description' => t('Use the alternative view mode selected in the display settings tab.')
      );
    }

    // Alternating view modes.
    $form['alternating_fieldset'] = array(
      '#type' => 'details',
      '#title' => t('Alternating view mode'),
      '#collapsible' => TRUE,
      '#collapsed' => !$this->options['alternating_fieldset']['alternating'],
    );
    $form['alternating_fieldset']['alternating'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use the changing view mode selector'),
      '#default_value' => $this->options['alternating_fieldset']['alternating'],
    );
    $form['alternating_fieldset']['allpages'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use this configuration on every page. Otherwhise the default view mode is used as soon you browse away from the first page of this view.'),
      '#default_value' => (isset($this->options['alternating_fieldset']['allpages'])) ? $this->options['alternating_fieldset']['allpages'] : FALSE,
    );

    $pager = $this->view->display_handler->getPlugin('pager');
    $limit = (isset($pager->options['items_per_page'])) ? $pager->options['items_per_page'] : 0;
    if ($limit == 0 || $limit > 20) {
      $form['alternating_fieldset']['disabled'] = array(
        '#markup' => t('This option is disabled because you have unlimited items or listing more than 20 items.'),
      );
      $form['alternating_fieldset']['alternating']['#disabled'] = TRUE;
      $form['alternating_fieldset']['allpages']['#disabled'] = TRUE;
    }
    else {
      $i = 1;
      $a = 0;
      while ($limit != 0) {
        $form['alternating_fieldset']['item_' . $a] = array(
          '#title' => t('Item @nr', array('@nr' => $i)),
          '#type' => 'select',
          '#default_value' => (isset($this->options['alternating_fieldset']['item_' . $a])) ? $this->options['alternating_fieldset']['item_' . $a] : 'teaser',
          '#options' => $this->buildViewModeOptions(),
        );
        $limit--;
        $a++;
        $i++;
      }
    }

    // Grouping rows
    $sorts = $this->view->display_handler->getOption('sorts');
    $groupable = !empty($sorts) && $this->options['grouping_fieldset']['group'];

    $form['grouping_fieldset'] = array(
      '#type' => 'details',
      '#title' => t('Group data'),
      '#collapsible' => TRUE,
      '#collapsed' => !$groupable,
    );
    $form['grouping_fieldset']['group'] = array(
      '#type' => 'checkbox',
      '#title' => t('Group data on a field. The value of this field will be displayed too.'),
      '#default_value' => $groupable,
    );

    if (!empty($sorts)) {
      $sort_options = array();
      foreach ($sorts as $sort) {
        $sort_name = drupal_ucfirst($sort['field']);
        $sort_options[$sort['table'] . '|' . $sort['field']] = $sort_name;
      }

      $form['grouping_fieldset']['group_field'] = array(
        '#type' => 'select',
        '#options' => $sort_options,
        '#default_value' => isset($this->options['grouping_fieldset']['group_field']) ? $this->options['grouping_fieldset']['group_field'] : '',
      );
      $form['grouping_fieldset']['group_field_function'] = array(
        '#type' => 'textfield',
        '#title' => 'Heading function',
        '#description' => String::checkPlain(t('The value of the field can be in a very raw format (eg, date created). Enter a custom function which you can use to format that value. The value and the object will be passed into that function eg. custom_function($raw_value, $object);')),
        '#default_value' => isset($this->options['grouping_fieldset']['group_field_function']) ? $this->options['grouping_fieldset']['group_field_function'] : '',
      );
    }
    else {
      $form['grouping_fieldset']['group']['#disabled'] = TRUE;
      $form['grouping_fieldset']['group']['#description'] = t('Grouping is disabled because you do not have any sort fields.');
    }

    // Advanced function.
    $form['advanced_fieldset'] = array(
      '#type' => 'details',
      '#title' => t('Advanced view mode'),
      '#collapsible' => TRUE,
      '#collapsed' => !$this->options['advanced_fieldset']['advanced'],
    );
    $form['advanced_fieldset']['advanced'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use the advanced view mode selector'),
      '#description' => t('This gives you the opportunity to have full control of a list for really advanced features.<br /> There is no UI for this, you need to create a hook named like this: hook_ds_views_row_render_entity($entity, $view_mode).', array('@VIEWSNAME' => $this->view->storage->id())),
      '#default_value' => $this->options['advanced_fieldset']['advanced'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function submitOptionsForm(&$form, &$form_state) {
    $form_state['values']['row_options']['alternating'] = $form_state['values']['row_options']['alternating_fieldset']['alternating'];
  }

  /**
   * {@inheritdoc}
   */
  public function preRender($result) {
    parent::preRender($result);

    if ($result) {
      // Get all entities which will be used to render in rows.
      $i = 0;
      $grouping = array();
      $rendered = FALSE;

      foreach ($result as $row) {
        $group_value_content = '';
        $entity = $row->_entity;
        $entity_id = $entity->id();

        // Default view mode.
        $view_mode = $this->options['view_mode'];

        // Display settings view mode.
        if ($this->options['switch_fieldset']['switch']) {
          if (!empty($entity->ds_switch->value)) {
            $view_mode = $entity->ds_switch->value;
          }
        }

        // Change the view mode per row.
        if ($this->options['alternating']) {
          // Check for paging to determine the view mode.
          $page = \Drupal::request()->get('page');
          if (!empty($page) && isset($this->options['alternating_fieldset']['allpages']) && !$this->options['alternating_fieldset']['allpages']) {
            $view_mode = $this->options['view_mode'];
          }
          else {
            $view_mode = isset($this->options['alternating_fieldset']['item_' . $i]) ? $this->options['alternating_fieldset']['item_' . $i] : $this->options['view_mode'];
          }
          $i++;
        }

        // The advanced selector invokes hook_ds_views_row_render_entity.
        if ($this->options['advanced_fieldset']['advanced']) {
          $modules = \Drupal::moduleHandler()->getImplementations('ds_views_row_render_entity');
          foreach ($modules as $module) {
            if ($content =  \Drupal::moduleHandler()->invoke($module, 'ds_views_row_render_entity', array($entity, $view_mode))) {
              $this->build[$entity_id] = $content;
              $rendered = TRUE;
            }
          }
        }

        // Give modules a chance to alter the $view_mode. Use $view_mode by ref.
        $view_name = $this->view->storage->id();
        $context = array(
          'entity' => $entity,
          'view_name' => $view_name,
          'display' => $this->view->getDisplay(),
        );
        \Drupal::moduleHandler()->alter('ds_views_view_mode', $view_mode, $context);

        if (!$rendered) {
          if (!empty($view_mode)) {
            $this->build[$entity_id] = entity_view($entity, $view_mode);
          }
          else {
            $this->build[$entity_id] = entity_view($entity, 'full');
          }
        }

        $context = array(
          'row' => $row,
          'view' => &$this->view,
          'view_mode' => $view_mode,
        );
        \Drupal::moduleHandler()->alter('ds_views_row_render_entity', $this->build[$entity_id], $context);

        // Keep a static grouping for this view.
        if ($this->options['grouping_fieldset']['group']) {

          $group_field = $this->options['grouping_fieldset']['group_field'];

          // New way of creating the alias.
          if (strpos($group_field, '|') !== FALSE) {
            list(, $ffield) = explode('|', $group_field);
            $group_field = $this->view->sort[$ffield]->tableAlias . '_' . $this->view->sort[$ffield]->realField;
          }

          // Note, the keys in the $row object are cut of at 60 chars.
          // see views_plugin_query_default.inc.
          if (drupal_strlen($group_field) > 60) {
            $group_field = drupal_substr($group_field, 0, 60);
          }

          $raw_group_value = isset($row->{$group_field}) ? $row->{$group_field} : '';
          $group_value = $raw_group_value;

          // Special function to format the heading value.
          if (!empty($this->options['grouping_fieldset']['group_field_function'])) {
            $function = $this->options['grouping_fieldset']['group_field_function'];
            if (function_exists($function)) {
              $group_value = $function($raw_group_value, $row->{$this->field_alias});
            }
          }

          if (!isset($grouping[$group_value])) {
            $group_value_content = array(
              '#markup' => '<h2 class="grouping-title">' . $group_value . '</h2>',
              '#weight' => -5,
            );
            $grouping[$group_value] = $group_value;
          }
        }

        // Grouping.
        if (!empty($grouping)) {
          if (!empty($group_value_content)) {
            $this->build[$entity_id] = array(
              'title' => $group_value_content,
              'content' => $this->build[$entity_id],
            );
          }
        }
      }
    }
  }
}
