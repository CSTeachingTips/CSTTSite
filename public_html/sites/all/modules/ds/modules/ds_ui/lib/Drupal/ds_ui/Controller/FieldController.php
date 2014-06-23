<?php

/**
 * @file
 * Contains \Drupal\ds_ui\Controller\FieldController.
 */

namespace Drupal\ds_ui\Controller;

use Drupal\Component\Utility\String;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Route controller fields.
 */
class FieldController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The config storage.
   *
   * @var \Drupal\Core\Config\StorageInterface;
   */
  protected $storage;

  /**
   * Constructs a \Drupal\ds_ui\Routing\FieldController object.
   *
   * @param \Drupal\Core\Config\StorageInterface $storage
   *   The configuration storage.
   */
  public function __construct(StorageInterface $storage) {
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.storage'));
  }

  /**
   * Builds a list of fields
   */
  public function fieldList() {
    $custom_fields = $this->storage->listAll('ds.field.');
    if (!empty($custom_fields)) {

      $rows = array();
      foreach ($custom_fields as $config) {
        $field_value = $this->config($config)->get();
        $row = array();
        $row[] = String::checkPlain($field_value['label']);
        $row[] = isset($field_value['type_label']) ? $field_value['type_label'] : $this->t('Unknown');
        $row[] = $field_value['id'];
        $row[] = ucwords(str_replace('_', ' ', implode(', ', $field_value['entities'])));

        $operations = array();
        $operations['edit'] = array(
          'title' => $this->t('Edit'),
          'href' => 'admin/structure/ds/fields/manage/' . $field_value['id'],
        );
        $operations['delete'] = array(
          'title' => $this->t('Delete'),
          'href' => 'admin/structure/ds/fields/delete/' . $field_value['id'],
        );
        $row[] = array(
          'data' => array(
            '#type' => 'operations',
            '#subtype' => 'ds',
            '#links' => $operations,
          ),
        );

        $rows[] = $row;
      }

      $table = array(
        'header' => array(
          'Label',
          'Type',
          'Machine name',
          'Entities',
          'Operations',
        ),
        'rows' => $rows,
      );

      $output = theme('table', $table);
    }
    else {
      $output = $this->t('No custom fields have been defined.');
    }

    return $output;
  }

  /**
   * Redirect to the correct manage callback.
   */
  public function manageRedirect($field_key) {
    $redirect = '';
    $config = $this->config('ds.field.' . $field_key);
    if ($field = $config->get()) {
      $redirect = 'admin/structure/ds/fields/manage_' . $field['type'] . '/' . $field_key;
    }

    if (!$redirect) {
      drupal_set_message($this->t('Field not found'));
      $redirect = 'admin/structure/ds/fields';
    }

    return new RedirectResponse(url($redirect));
  }

}
