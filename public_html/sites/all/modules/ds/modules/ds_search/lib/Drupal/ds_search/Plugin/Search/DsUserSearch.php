<?php

/**
 * @file
 * Contains \Drupal\ds_search\Plugin\Search\UserSearch.
 */

namespace Drupal\ds_search\Plugin\Search;

use Drupal\ds_search\DsSearch;
use Drupal\Core\Access\AccessibleInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\search\Plugin\ConfigurableSearchPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Handles searching for node entities using the Search module index.
 *
 * @SearchPlugin(
 *   id = "ds_user_search",
 *   title = @Translation("Users (Display Suite)")
 * )
 */
class DsUserSearch extends ConfigurableSearchPluginBase implements AccessibleInterface{

  use DsSearch;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  static public function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static(
      $container->get('database'),
      $container->get('entity.manager'),
      $container->get('module_handler'),
      $container->get('current_user'),
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * Creates a UserSearch object.
   *
   * @param Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(Connection $database, EntityManagerInterface $entity_manager, ModuleHandlerInterface $module_handler, AccountInterface $current_user, array $configuration, $plugin_id, array $plugin_definition) {
    $this->database = $database;
    $this->entityManager = $entity_manager;
    $this->moduleHandler = $module_handler;
    $this->currentUser = $current_user;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function access($operation = 'view', AccountInterface $account = NULL) {
    return !empty($account) && $account->hasPermission('access user profiles');
  }

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $results = array();
    if (!$this->isSearchExecutable()) {
      return $results;
    }
    $keys = $this->keywords;
    // Replace wildcards with MySQL/PostgreSQL wildcards.
    $keys = preg_replace('!\*+!', '%', $keys);
    $query = $this->database
      ->select('users')
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender');
    $query->fields('users', array('uid'));
    if ($this->currentUser->hasPermission('administer users')) {
      // Administrators can also search in the otherwise private email field, and
      // they don't need to be restricted to only active users.
      $query->fields('users', array('mail'));
      $query->condition($query->orConditionGroup()
          ->condition('name', '%' . $this->database->escapeLike($keys) . '%', 'LIKE')
          ->condition('mail', '%' . $this->database->escapeLike($keys) . '%', 'LIKE')
      );
    }
    else {
      // Regular users can only search via usernames, and we do not show them
      // blocked accounts.
      $query->condition('name', '%' . $this->database->escapeLike($keys) . '%', 'LIKE')
        ->condition('status', 1);
    }

    // Add limit
    if (!empty($this->configuration['limit'])) {
      $query->limit($this->configuration['limit']);
    }

    $uids = $query
      ->execute()
      ->fetchCol();
    $accounts = $this->entityManager->getStorageController('user')->loadMultiple($uids);

    foreach ($accounts as $account) {
      $result = array(
        'user' => $account,
        'title' => $account->getUsername(),
        'link' => url('user/' . $account->id(), array('absolute' => TRUE)),
      );
      if ($this->currentUser->hasPermission('administer users')) {
        $result['title'] .= ' (' . $account->getEmail() . ')';
      }
      $results[] = $result;
    }

    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function buildResults() {
    $results = $this->execute();

    // Build shared variables.
    $build = array('#type' => 'user');
    $this->buildSharedPageVariables($build, $this->configuration);

    $i = 0;
    foreach ($results as $result) {
      $data = entity_view($result['user'], $this->configuration['view_mode']);
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

    return $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, array &$form_state) {
    // Add general settings
    $form = $this->generalConfigurationForm($form, $form_state, $this->configuration, 'user');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, array &$form_state) {
    // Submits general settings
    $this->generalSubmitConfigurationForm($this->configuration, $form_state);
  }

}
