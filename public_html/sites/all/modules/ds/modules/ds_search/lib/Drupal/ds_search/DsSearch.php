<?php
/**
 * @file
 * General trait for the Display SUite search module
 */

namespace Drupal\ds_search;

use Drupal\Component\Utility\String;

trait DsSearch {

  /**
   * Generated the general default settings.
   */
  public function generalDefaultSettings(&$configuration) {
    $configuration['view_mode'] = 'search_result';
    $configuration['search_variables'] = 'none';
    $configuration['show_title'] = FALSE;
    $configuration['highlight'] = FALSE;
    $configuration['highlight_selector'] = '.view-mode-search_result';
    $configuration['limit'] = 10;
  }

  /**
   * Generates a reusable settings form
   */
  public function generalConfigurationForm(array $form, array &$form_state, $configuration, $entity_type) {
    // Load global form elements for both DsNodeSearch and DsUserSearch
    $view_modes = entity_get_view_modes($entity_type);

    $options = array();
    foreach ($view_modes as $id => $view_mode) {
      $options[$id] = $view_mode['label'];
    }

    $form['general'] = array(
      '#type' => 'details',
      '#title' => t('General'),
    );
    $form['general']['view_mode'] = array(
      '#type' => 'select',
      '#title' => t('View mode'),
      '#description' => 'Select another view mode in favor of the default search view mode.',
      '#default_value' => $configuration['view_mode'],
      '#options' => $options,
    );
    $form['general']['search_variables'] = array(
      '#type' => 'radios',
      '#options' => array(
        'none' => t('None'),
        'search_totals' => t('Total results'),
        'search_totals_plus_keywords' => t('Total results + keywords'),
        'search_totals_from_to_end' => t('Totals + start to end')
      ),
      '#title' => t('Extra variables'),
      '#description' => t('Choose an extra variable to display on the results screen.'),
      '#default_value' => $configuration['search_variables'],
    );
    $form['general']['show_title'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show title'),
      '#description' => t('Display the "Search results" title.'),
      '#default_value' => $configuration['show_title'],
    );
    $form['general']['highlight'] = array(
      '#type' => 'checkbox',
      '#title' => t('Highlight search word'),
      '#description' => t('Use jQuery to highlight the word in the results.'),
      '#default_value' => $configuration['highlight'],
    );
    $form['general']['highlight_selector'] = array(
      '#type' => 'textfield',
      '#title' => t('HTML selector'),
      '#description' => t('Enter the css selector, if not sure, leave this by default.'),
      '#default_value' => $configuration['highlight_selector'],
      '#states' => array(
        'visible' => array(
          'input[name="highlight"]' => array('checked' => TRUE),
        ),
      ),
    );
    $form['general']['limit'] = array(
      '#type' => 'number',
      '#min' => 0,
      '#default_value' => $this->configuration['limit'],
      '#title' => t('Search limit'),
      '#description' => t('The number of items to display per page. Enter 0 for no limit.'),
    );

    return $form;
  }

  /**
   * Submits the general settings.
   */
  public function generalSubmitConfigurationForm(&$configuration, &$form_state, $save_view_mode = FALSE) {
    $configuration['view_mode'] = $form_state['values']['view_mode'];
    $configuration['search_variables'] = $form_state['values']['search_variables'];
    $configuration['show_title'] = $form_state['values']['show_title'];
    $configuration['highlight'] = $form_state['values']['highlight'];
    $configuration['highlight_selector'] = $form_state['values']['highlight_selector'];
    $configuration['limit'] = $form_state['values']['limit'];

    if ($save_view_mode) {
      // Also save the view_mode in config, it's used by the Display Suite fields.
      \Drupal::config('ds_search.settings')->set('view_mode', $form_state['values']['view_mode'])->save();
    }
  }

  /**
   * Build shared page variables.
   *
   * @param $build
   *   The build array.
   */
  public function buildSharedPageVariables(&$build, $configuration) {

    // Search results title.
    if ($configuration['show_title']) {
      $build['search_title'] = array('#markup' => '<h2>' . t('Search results') . '</h2>');
    }

    // Extra variables.
    if ($configuration['search_variables'] != 'none') {
      $build['search_extra'] = array('#markup' => '<div class="ds-search-extra">' . $this->fetchExtraVariables(arg(2), $configuration) . '</div>');
    }

    // Search results.
    $build['search_results'] = array();

    // Pager.
    $build['search_pager'] = array('#theme' => 'pager');

    // CSS and JS.
    if ($configuration['highlight']) {
      $build['#attached'] = array(
        'css' => array(
          drupal_get_path('module', 'ds_search') . '/css/search.theme.css',
        ),
        'js' => array(
          drupal_get_path('module', 'ds_search') . '/js/search.js',
          array(
            'type' => 'setting',
            'data' => array(
              'ds_search' => array(
                'selector' => String::checkPlain($configuration['highlight_selector']),
                'search' => String::checkPlain(arg(2)),
              ),
            ),
          ),
        ),
      );
    }
  }

  /**
   * Return the extra variables.
   */
  private function fetchExtraVariables($arg_keys = NULL, $configuration) {
    // Define the number of results being shown on a page.
    // We rely on the apache solr rows for now.
    $items_per_page = $configuration['limit'];

    // Get the current page.
    $current_page = isset($_REQUEST['page']) ? $_REQUEST['page']+1 : 1;

    // Get the total number of results from the $GLOBALS.
    $total = isset($GLOBALS['pager_total_items'][0]) ? $GLOBALS['pager_total_items'][0] : 0;

    // Perform calculation
    $start = $items_per_page * $current_page - ($items_per_page - 1);
    $end = $items_per_page * $current_page;
    if ($end > $total) $end = $total;

    // Get the search keys.
    $keys = parent::getKeyWords();

    // Send the right extra variable.
    switch ($configuration['search_variables']) {
      case 'search_totals':
        return \Drupal::translation()->formatPlural($total, 'One result', 'Total results: @total.', array('@total' => $total));
        break;

      case 'search_totals_plus_keywords':
        return \Drupal::translation()->formatPlural($total, 'Your search for "<strong>@search</strong>" gave back 1 result.',
          'Your search for "<strong>@search</strong>" gave back @count results.',
          array('@search' => $keys));
        break;

      case 'search_totals_from_to_end':
        return \Drupal::translation()->formatPlural($total, 'Displaying @start - @end of 1 result.',
          'Displaying @start - @end of @count results.',
          array('@start' => $start, '@end' => $end));
        break;
    }
  }
}
