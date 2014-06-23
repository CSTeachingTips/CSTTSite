<?php

/**
 * @file
 * Definition of Drupal\ds\Tests\HooksTest.
 */

namespace Drupal\ds\Tests;

/**
 * Tests for Display Suite hooks.
 */
class HooksTest extends BaseTest {

  /**
   * Implements getInfo().
   */
  public static function getInfo() {
    return array(
      'name' => t('Hooks'),
      'description' => t('Tests for hooks: ds_fields, ds_fields_alter, ds_layouts.'),
      'group' => t('Display Suite'),
    );
  }

  /**
   * Test fields hooks.
   */
  function testDSFields() {

    $this->dsSelectLayout();

    // Find the two extra fields from the test module on the node type.
    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->assertText('Test code field from plugin', 'Test field found on node.');
    $this->assertText('Field altered', 'Test field altered found on node.');

    $empty = array();
    $edit = array('layout' => 'ds_2col_stacked');
    $this->dsSelectLayout($edit, $empty, 'admin/config/people/accounts/display');

    // Fields can not be found on user.
    $this->drupalGet('admin/config/people/accounts/display');
    $this->assertNoText('Test code field from plugin', 'Test field not found on user.');
    $this->assertNoText('Field altered', 'Test field altered not found on user.');

    // Select layout.
    $this->dsSelectLayout();

    $fields = array(
      'fields[node_author][region]' => 'left',
      'fields[node_links][region]' => 'left',
      'fields[body][region]' => 'right',
      'fields[test_field][region]' => 'right',
      'fields[test_field_empty_string][region]' => 'right',
      'fields[test_field_empty_string][label]' => 'inline',
      'fields[test_field_false][region]' => 'right',
      'fields[test_field_false][label]' => 'inline',
      'fields[test_field_null][region]' => 'right',
      'fields[test_field_null][label]' => 'inline',
      'fields[test_field_nothing][region]' => 'right',
      'fields[test_field_nothing][label]' => 'inline',
      'fields[test_field_zero_int][region]' => 'right',
      'fields[test_field_zero_int][label]' => 'inline',
      'fields[test_field_zero_string][region]' => 'right',
      'fields[test_field_zero_string][label]' => 'inline',
      'fields[test_field_zero_float][region]' => 'right',
      'fields[test_field_zero_float][label]' => 'inline',
    );

    $this->dsSelectLayout();
    $this->dsConfigureUI($fields);

    // Create a node.
    $settings = array('type' => 'article');
    $node = $this->drupalCreateNode($settings);
    $this->drupalGet('node/' . $node->id());

    $this->assertRaw('group-left', 'Template found (region left)');
    $this->assertRaw('group-right', 'Template found (region right)');
    $this->assertText('Test code field on node ' . $node->id(), 'Test code field found');
    $this->assertNoText('Test code field that returns an empty string', 'Test code field that returns an empty string is not visible.');
    $this->assertNoText('Test code field that returns FALSE', 'Test code field that returns FALSE is not visible.');
    $this->assertNoText('Test code field that returns NULL', 'Test code field that returns NULL is not visible.');
    $this->assertNoText('Test code field that returns nothing', 'Test code field that returns nothing is not visible.');
    $this->assertNoText('Test code field that returns an empty array', 'Test code field that returns an empty array is not visible.');
    $this->assertText('Test code field that returns zero as an integer', 'Test code field that returns zero as an integer is visible.');
    $this->assertText('Test code field that returns zero as a string', 'Test code field that returns zero as a string is visible.');
    $this->assertText('Test code field that returns zero as a floating point number', 'Test code field that returns zero as a floating point number is visible.');
  }

  /**
   * Test layouts hook.
   */
  function testDSLayouts() {

    // Assert our 2 tests layouts are found.
    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->assertRaw('Test One column', 'Test One column layout found');
    $this->assertRaw('Test Two column', 'Test Two column layout found');

    $layout = array(
      'layout' => 'dstest_2col',
    );

    $assert = array(
      'regions' => array(
        'left' => '<td colspan="8">' . t('Left') . '</td>',
        'right' => '<td colspan="8">' . t('Right') . '</td>',
      ),
    );

    $fields = array(
      'fields[node_author][region]' => 'left',
      'fields[node_links][region]' => 'left',
      'fields[body][region]' => 'right',
    );

    $this->dsSelectLayout($layout, $assert);
    $this->dsConfigureUI($fields);

    // Create a node.
    $settings = array('type' => 'article');
    $node = $this->drupalCreateNode($settings);

    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('group-left', 'Template found (region left)');
    $this->assertRaw('group-right', 'Template found (region right)');
    $this->assertRaw('dstest_2col.css', 'Css file included');

    // Alter a region
    $settings = array(
      'type' => 'article',
      'title' => 'Alter me!',
    );
    $node = $this->drupalCreateNode($settings);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('cool!', 'Region altered');
  }
}
