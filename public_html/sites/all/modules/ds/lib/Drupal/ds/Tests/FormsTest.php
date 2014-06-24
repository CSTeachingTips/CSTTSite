<?php

/**
 * @file
 * Definition of Drupal\ds\Tests\FormsTest.
 */

namespace Drupal\ds\Tests;

use Drupal\simpletest\WebTestBase;

class FormsTest extends BaseTest {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => t('Forms'),
      'description' => t('Tests for managing layouts on forms.'),
      'group' => t('Display Suite'),
    );
  }

  /**
   * Forms tests.
   */
  function testDSForms() {

    // Create a node.
    $node = $this->drupalCreateNode(array('type' => 'article'));

    // Configure teaser layout.
    $form = array(
      'layout' => 'ds_2col_stacked',
    );
    $form_assert = array(
      'regions' => array(
        'header' => '<td colspan="8">' . t('Header') . '</td>',
        'left' => '<td colspan="8">' . t('Left') . '</td>',
        'right' => '<td colspan="8">' . t('Right') . '</td>',
        'footer' => '<td colspan="8">' . t('Footer') . '</td>',
      ),
    );
    $this->dsSelectLayout($form, $form_assert, 'admin/structure/types/manage/article/form-display');

    $fields = array(
      'fields[title][region]' => 'header',
      'fields[body][region]' => 'left',
      'fields[field_image][region]' => 'right',
      'fields[field_tags][region]' => 'right',
    );
    $this->dsConfigureUI($fields, 'admin/structure/types/manage/article/form-display');

    // Inspect the node.
    $this->drupalGet('node/' . $node->id() . '/edit');
    $this->assertRaw('ds_2col_stacked', 'ds-form class added');
    $this->assertRaw('group-header', 'Template found (region header)');
    $this->assertRaw('group-left', 'Template found (region left)');
    $this->assertRaw('group-right', 'Template found (region right)');
    $this->assertRaw('group-footer', 'Template found (region footer)');
    $this->assertRaw('edit-title', 'Title field found');
    $this->assertRaw('button form-submit', 'Submit field found');
    $this->assertRaw('edit-field-tags-und', 'Tags field found');
    $this->assertRaw('edit-log', 'Revision log found');
  }
}
