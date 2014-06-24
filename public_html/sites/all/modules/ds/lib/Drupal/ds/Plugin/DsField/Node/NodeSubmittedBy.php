<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\Node\NodeSubmittedBy.
 */

namespace Drupal\ds\Plugin\DsField\Node;

use Drupal\ds\Plugin\DsField\Date;

/**
 * Plugin that renders the submitted by field.
 *
 * @DsField(
 *   id = "node_submitted_by",
 *   title = @Translation("Submitted by"),
 *   entity_type = "node",
 *   provider = "node"
 * )
 */
class NodeSubmittedBy extends Date {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $field = $this->getFieldConfiguration();
    $account = $this->entity()->getOwner();
    switch ($field['formatter']) {
      case 'ds_time_ago':
        $interval = REQUEST_TIME - $this->entity()->created->value;
        $user_name = array(
          '#theme' => 'username',
          '#account' => 'account',
        );
        return array(
          '#markup' => t('Submitted !interval ago by !user.', array('!interval' => \Drupal::service('date')->formatInterval($interval), '!user' => drupal_render($user_name))),
        );
      default:
        $date_format = str_replace('ds_post_date_', '', $field['formatter']);
        $user_name = array(
          '#theme' => 'username',
          '#account' => $account,
        );
        return array(
          '#markup' => t('Submitted by !user on !date.', array('!user' => drupal_render($user_name), '!date' => format_date($this->entity()->created->value, $date_format))),
        );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function formatters() {
    // Fetch all the date formatters
    $date_formatters = parent::formatters();

    // Add a "time ago" formatter
    $date_formatters['ds_time_ago'] = t('Time ago');

    return $date_formatters;
  }

}
