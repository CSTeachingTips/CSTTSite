<?php

/**
 * @file
 * Definition of Drupal\ds\Tests\ExportablesTest.
 */

namespace Drupal\ds\Tests;

class ExportablesTest extends BaseTest {

  /**
   * Implements getInfo().
   */
  public static function getInfo() {
    return array(
      'name' => t('Exportables'),
      'description' => t('Tests for exportables in Display Suite.'),
      'group' => t('Display Suite'),
    );
  }

  /**
   * Enables the exportables module.
   */
  function dsExportablesSetup() {
    module_enable(array('ds_exportables_test'));
    drupal_flush_all_caches();
  }

  // Test layout and field settings configuration.
  function testDSExportablesLayoutFieldsettings() {
    $this->dsExportablesSetup();

    $this->drupalGet('admin/structure/types/manage/article/display');

    $settings = array(
      'type' => 'article',
      'title' => 'Exportable'
    );
    $node = $this->drupalCreateNode($settings);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('group-left', 'Left region found');
    $this->assertRaw('group-right', 'Right region found');
    $this->assertNoRaw('group-header', 'No header region found');
    $this->assertNoRaw('group-footer', 'No footer region found');
    $this->assertRaw('<h3><a href="'. url('node/1') . '" class="active">Exportable</a></h3>', t('Default title with h3 found'));
    $this->assertRaw('<a href="' . url('node/1') . '" class="active">Read more</a>', t('Default read more found'));

    // Override default layout.
    $layout = array(
      'layout' => 'ds_2col_stacked',
    );

    $assert = array(
      'regions' => array(
        'header' => '<td colspan="8">' . t('Header') . '</td>',
        'left' => '<td colspan="8">' . t('Left') . '</td>',
        'right' => '<td colspan="8">' . t('Right') . '</td>',
        'footer' => '<td colspan="8">' . t('Footer') . '</td>',
      ),
    );

    $fields = array(
      'fields[node_post_date][region]' => 'header',
      'fields[node_author][region]' => 'left',
      'fields[node_links][region]' => 'left',
      'fields[body][region]' => 'right',
      'fields[node_comments][region]' => 'footer',
    );

    $this->dsSelectLayout($layout, $assert);
    $this->dsConfigureUI($fields);

    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('group-left', 'Left region found');
    $this->assertRaw('group-right', 'Left region found');
    $this->assertRaw('group-header', 'Left region found');
    $this->assertRaw('group-footer', 'Left region found');
  }

  // Test custom field config.
  function testDSExportablesCustomFields() {
    $this->dsExportablesSetup();

    // Look for default custom field.
    $this->drupalGet('admin/structure/ds/fields');
    $this->assertText('Exportable field');
    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->assertText('Exportable field');
  }
}
