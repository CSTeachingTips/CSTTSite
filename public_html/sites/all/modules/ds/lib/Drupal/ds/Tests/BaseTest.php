<?php

/**
 * @file
 * Definition of Drupal\ds\Tests\BaseTest.
 */

namespace Drupal\ds\Tests;

use Drupal\simpletest\WebTestBase;

abstract class BaseTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('field_ui', 'comment', 'block', 'ds', 'ds_extras', 'search', 'ds_search', 'ds_forms', 'ds_ui', 'ds_test', 'views', 'views_ui');

  /**
   * The chosen installation profile
   *
   * @var string
   */
  protected $profile = 'standard';

  /**
   * The admin user with Display Suite configuration permissions enabled
   */
  protected $admin_user;

  /**
   * Implementation of setUp().
   */
  function setUp() {
    parent::setUp();

    $this->admin_user = $this->drupalCreateUser(array('admin classes', 'admin fields', 'admin display suite', 'ds_switch article', 'access administration pages', 'administer content types', 'administer users', 'administer comments', 'administer nodes', 'bypass node access', 'administer blocks', 'search content', 'use advanced search', 'administer search', 'access user profiles', 'administer permissions', 'administer node fields', 'administer node display', 'administer node form display', 'administer user fields', 'administer user display', 'administer user form display', 'administer comment fields', 'administer comment display', 'administer comment form display', 'administer views'));
    $this->drupalLogin($this->admin_user);
  }

  /**
   * Select a layout.
   */
  function dsSelectLayout($edit = array(), $assert = array(), $url = 'admin/structure/types/manage/article/display', $options = array()) {

    $edit += array(
      'layout' => 'ds_2col_stacked',
    );

    $this->drupalPostForm($url, $edit, t('Save'), $options);

    $assert += array(
      'regions' => array(
        'header' => '<td colspan="8">' . t('Header') . '</td>',
        'left' => '<td colspan="8">' . t('Left') . '</td>',
        'right' => '<td colspan="8">' . t('Right') . '</td>',
        'footer' => '<td colspan="8">' . t('Footer') . '</td>',
      ),
    );

    foreach ($assert['regions'] as $region => $raw) {
      $this->assertRaw($region, t('Region !region found', array('!region' => $region)));
    }
  }

  /**
   * Configure classes
   */
  function dsConfigureClasses($edit = array()) {

    $edit += array(
      'regions' => "class_name_1\nclass_name_2|Friendly name"
    );

    $this->drupalPostForm('admin/structure/ds/classes', $edit, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'), t('CSS classes configuration saved'));
    $this->assertRaw('class_name_1', 'Class name 1 found');
    $this->assertRaw('class_name_2', 'Class name 1 found');
  }

  /**
   * Configure classes on a layout.
   */
  function dsSelectClasses($edit = array(), $url = 'admin/structure/types/manage/article/display') {

    $edit += array(
      "header[]" => 'class_name_1',
      "footer[]" => 'class_name_2',
    );

    $this->drupalPostForm($url, $edit, t('Save'));
  }

  /**
   * Configure Field UI.
   */
  function dsConfigureUI($edit, $url = 'admin/structure/types/manage/article/display') {
    $this->drupalPostForm($url, $edit, t('Save'));
  }

  /**
   * Edit field formatter settings
   */
  function dsEditFormatterSettings($edit, $url = 'admin/structure/types/manage/article/display', $element_value = 'edit body') {
    $this->drupalPostForm($url, array(), $element_value);
    $this->drupalPostForm(NULL, $edit, t('Update'));
    $this->drupalPostForm(NULL, array(), t('Save'));
  }

  /**
   * Create a code field.
   *
   * @param $edit
   *   An optional array of field properties.
   */
  function dsCreateCodeField($edit = array(), $url = 'admin/structure/ds/fields/manage_code') {

    $edit += array(
      'name' => 'Test field',
      'id' => 'test_field',
      'entities[node]' => '1',
      'code[value]' => 'Test field',
      'use_token' => '0',
    );

    $this->drupalPostForm($url, $edit, t('Save'));
    $this->assertText(t('The field ' . $edit['name'] . ' has been saved'), t('!name field has been saved', array('!name' => $edit['name'])));
  }

  /**
   * Create a block field.
   *
   * @param $edit
   *   An optional array of field properties.
   */
  function dsCreateBlockField($edit = array(), $url = 'admin/structure/ds/fields/manage_block', $first = TRUE) {

    $edit += array(
      'name' => 'Test block field',
      'entities[node]' => '1',
      'block' => 'node_recent_block',
    );

    if ($first) {
      $edit += array('id' => 'test_block_field');
    }

    $this->drupalPostForm($url, $edit, t('Save'));
    $this->assertText(t('The field ' . $edit['name'] . ' has been saved'), t('!name field has been saved', array('!name' => $edit['name'])));
  }
}
