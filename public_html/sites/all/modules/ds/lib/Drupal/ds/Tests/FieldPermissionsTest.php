<?php

/**
 * @file
 * Definition of Drupal\ds\Tests\ViewModesTest.
 */

namespace Drupal\ds\Tests;

use Drupal\Core\Extension\ModuleHandler;

/**
 * Tests for Display Suite field permissions.
 */
class FieldPermissionsTest extends BaseTest {

  /**
   * Implements getInfo().
   */
  public static function getInfo() {
    return array(
      'name' => t('Field permissions'),
      'description' => t('Tests for testing field permissions.'),
      'group' => t('Display Suite'),
    );
  }

  function testFieldPermissions() {

    $fields = array(
      'fields[body][region]' => 'right',
      'fields[test_field][region]' => 'left',
    );

    \Drupal::config('ds.extras')->set('field_permissions', TRUE)->save();
    \Drupal::moduleHandler()->resetImplementations();

    $this->dsSelectLayout();
    $this->dsConfigureUI($fields);

    // Create a node.
    $settings = array('type' => 'article');
    $node = $this->drupalCreateNode($settings);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('group-right', 'Template found (region right)');
    $this->assertNoText('Test code field on node ' . $node->id(), 'Test code field not found');

    // Give permissions.
    $edit = array(
      'authenticated[view node_author on node]' => 1,
      'authenticated[view test_field on node]' => 1,
    );
    $this->drupalPostForm('admin/people/permissions', $edit, t('Save permissions'));
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('group-left', 'Template found (region left)');
    $this->assertText('Test code field on node ' . $node->id(), 'Test code field found');
  }
}
