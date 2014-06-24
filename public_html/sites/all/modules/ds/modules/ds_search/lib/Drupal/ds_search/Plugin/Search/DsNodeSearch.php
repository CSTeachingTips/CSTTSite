<?php

/**
 * @file
 * Contains \Drupal\ds_search\Plugin\Search\NodeSearch.
 */

namespace Drupal\ds_search\Plugin\Search;

use Drupal\ds_search\DsSearch;
use Drupal\node\Plugin\Search\NodeSearch;

/**
 * Handles searching for node entities using the Search module index.
 *
 * @SearchPlugin(
 *   id = "ds_node_search",
 *   title = @Translation("Content (Display Suite)")
 * )
 */
class DsNodeSearch extends NodeSearch {

  use DsSearch;

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $results = array();
    if (!$this->isSearchExecutable()) {
      return $results;
    }
    $keys = $this->keywords;

    // Build matching conditions.
    $query = $this->database
      ->select('search_index', 'i', array('target' => 'slave'))
      ->extend('Drupal\search\SearchQuery')
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender');
    $query->join('node_field_data', 'n', 'n.nid = i.sid');
    $query->condition('n.status', 1)
      ->addTag('node_access')
      ->searchExpression($keys, $this->getPluginId());

    // Handle advanced search filters in the f query string.
    // \Drupal::request()->query->get('f') is an array that looks like this in
    // the URL: ?f[]=type:page&f[]=term:27&f[]=term:13&f[]=langcode:en
    // So $parameters['f'] looks like:
    // array('type:page', 'term:27', 'term:13', 'langcode:en');
    // We need to parse this out into query conditions.
    $parameters = $this->getParameters();
    if (!empty($parameters['f']) && is_array($parameters['f'])) {
      $filters = array();
      // Match any query value that is an expected option and a value
      // separated by ':' like 'term:27'.
      $pattern = '/^(' . implode('|', array_keys($this->advanced)) . '):([^ ]*)/i';
      foreach ($parameters['f'] as $item) {
        if (preg_match($pattern, $item, $m)) {
          // Use the matched value as the array key to eliminate duplicates.
          $filters[$m[1]][$m[2]] = $m[2];
        }
      }
      // Now turn these into query conditions. This assumes that everything in
      // $filters is a known type of advanced search.
      foreach ($filters as $option => $matched) {
        $info = $this->advanced[$option];
        // Insert additional conditions. By default, all use the OR operator.
        $operator = empty($info['operator']) ? 'OR' : $info['operator'];
        $where = new Condition($operator);
        foreach ($matched as $value) {
          $where->condition($info['column'], $value);
        }
        $query->condition($where);
        if (!empty($info['join'])) {
          $query->join($info['join']['table'], $info['join']['alias'], $info['join']['condition']);
        }
      }
    }
    // Only continue if the first pass query matches.
    if (!$query->executeFirstPass()) {
      return array();
    }

    // Add the ranking expressions.
    $this->addNodeRankings($query);

    // Add the language code of the indexed item to the result of the query,
    // since the node will be rendered using the respective language.
    $query = $query->fields('i', array('langcode'));

    // Add limit
    if (!empty($this->configuration['limit'])) {
      $query->limit($this->configuration['limit']);
    }

    // Load results.
    $find = $query->execute();

    $node_storage = $this->entityManager->getStorageController('node');
    $node_render = $this->entityManager->getViewBuilder('node');

    foreach ($find as $item) {
      // Render the node.
      $node = $node_storage->load($item->sid)->getTranslation($item->langcode);
      $build = $node_render->view($node, $this->configuration['view_mode'], $item->langcode);
      unset($build['#theme']);
      $node->rendered = drupal_render($build);

      // Fetch comment count for snippet.
      $node->rendered .= ' ' . $this->moduleHandler->invoke('comment', 'node_update_index', array($node, $item->langcode));

      $node->search_extra = $this->moduleHandler->invokeAll('node_search_result', array($node, $item->langcode));
      $node->snippet = search_excerpt($keys, $node->rendered, $item->langcode);

      $results[] = array(
        'node' => $node,
      );
    }
    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function buildResults() {
    $results = $this->execute();

    // Build shared variables.
    $build = array('#type' => 'node');
    $this->buildSharedPageVariables($build, $this->configuration);

    $i = 0;
    foreach ($results as $result) {
      $data = entity_view($result['node'], $this->configuration['view_mode']);
      $build['search_results'][$i] = $data;
      $i++;
    }

    return array(
      '#theme' => 'ds_search_page',
      '#build' => $build,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // Fetch default for nodes
    $configuration = parent::defaultConfiguration();

    // Set general defaults
    $this->generalDefaultSettings($configuration);

    // Add node specific Display Suite settings
    $configuration['advanced_search'] = FALSE;

    return $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, array &$form_state) {
    // Fetch form from node search
    $form = parent::buildConfigurationForm($form, $form_state);

    // Add general settings
    $form = $this->generalConfigurationForm($form, $form_state, $this->configuration, 'node');

    // Add node specific settings
    $form['node'] = array(
      '#type' => 'details',
      '#title' => t('Node'),
    );
    $form['node']['advanced_search'] = array(
      '#type' => 'checkbox',
      '#title' => t('Advanced'),
      '#description' => t('Enable the advanced search form.'),
      '#default_value' => $this->configuration['advanced_search'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, array &$form_state) {
    parent::submitConfigurationForm($form, $form_state);

    // Submits general settings
    $this->generalSubmitConfigurationForm($this->configuration, $form_state, TRUE);

    // Submits node specific settings
    $this->configuration['advanced_search'] = $form_state['values']['advanced_search'];
  }

  /**
   * {@inheritdoc}
   */
  public function searchFormAlter(array &$form, array &$form_state) {
    if ($this->configuration['advanced_search']) {
      return parent::searchFormAlter($form, $form_state);
    }
  }
}
