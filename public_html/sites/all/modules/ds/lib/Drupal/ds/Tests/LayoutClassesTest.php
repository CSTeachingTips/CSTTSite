<?php

/**
 * @file
 * Definition of Drupal\ds\Tests\LayoutClassesTest.
 */

namespace Drupal\ds\Tests;

/**
 * Test managing of layouts and CSS classes
 */
class LayoutClassesTest extends BaseTest {

  /**
   * Implements getInfo().
   */
  public static function getInfo() {
    return array(
      'name' => t('Layouts'),
      'description' => t('Tests for managing layouts and classes on Field UI screen.'),
      'group' => t('Display Suite'),
    );
  }

  /**
   * Test selecting layouts, classes, region to block and fields.
   */
  function testDStestLayouts() {

    // Check that the ds_3col_equal_width layout is not available (through the alter).
    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->assertNoRaw('ds_3col_stacked_equal_width', 'ds_3col_stacked_equal_width not available');

    // Create code and block field.
    $this->dsCreateCodeField();
    $this->dsCreateBlockField();

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
      'fields[dynamic_code_field:node-test_field][region]' => 'left',
      'fields[dynamic_block_field:node-test_block_field][region]' => 'left',
      'fields[node_submitted_by][region]' => 'left',
      'fields[ds_extras_extra_test_field][region]' => 'header',
    );

    // Setup first layout.
    $this->dsSelectLayout($layout, $assert);
    $this->dsConfigureClasses();
    $this->dsSelectClasses();
    $this->dsConfigureUI($fields);

    // Assert the two extra fields are found.
    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->assertRaw('ds_extras_extra_test_field');
    $this->assertRaw('ds_extras_second_field');

    // Assert we have configuration.
    $count = count(config_get_storage_names_with_prefix('ds.layout_settings.node.article.default'));
    $this->assertEqual($count, 1, t('Configuration file found for layout settings for node article'));

    // Lookup settings and verify.
    $data = \Drupal::config('ds.layout_settings.node.article.default')->get('settings');
    $this->assertTrue(in_array('ds_extras_extra_test_field', $data['regions']['header']), t('Extra field is in header'));
    $this->assertTrue(in_array('node_post_date', $data['regions']['header']), t('Post date is in header'));
    $this->assertTrue(in_array('dynamic_code_field:node-test_field', $data['regions']['left']), t('Test field is in left'));
    $this->assertTrue(in_array('node_author', $data['regions']['left']), t('Author is in left'));
    $this->assertTrue(in_array('node_links', $data['regions']['left']), t('Links is in left'));
    $this->assertTrue(in_array('dynamic_block_field:node-test_block_field', $data['regions']['left']), t('Test block field is in left'));
    $this->assertTrue(in_array('body', $data['regions']['right']), t('Body is in right'));
    $this->assertTrue(in_array('node_comments', $data['regions']['footer']), t('Comments is in footer'));
    $this->assertTrue(in_array('class_name_1', $data['classes']['header']), t('Class name 1 is in header'));
    $this->assertTrue(empty($data['classes']['left']), t('Left has no classes'));
    $this->assertTrue(empty($data['classes']['right']), t('Right has classes'));
    $this->assertTrue(in_array('class_name_2', $data['classes']['footer']), t('Class name 2 is in header'));

    // Create a article node and verify settings.
    $settings = array(
      'type' => 'article',
    );
    $node = $this->drupalCreateNode($settings);
    $this->drupalGet('node/' . $node->id());

    // Assert regions.
    $this->assertRaw('group-header', 'Template found (region header)');
    $this->assertRaw('group-header class_name_1', 'Class found (class_name_1)');
    $this->assertRaw('group-left', 'Template found (region left)');
    $this->assertRaw('group-right', 'Template found (region right)');
    $this->assertRaw('group-footer', 'Template found (region footer)');
    $this->assertRaw('group-footer class_name_2', 'Class found (class_name_2)');

    // Assert custom fields.
    // @todo code field is broken at the moment
    $this->assertRaw('field-name-test-field', t('Custom field found'));
    $this->assertRaw('Test field', t('Custom field found'));
    $this->assertRaw('field-name-dynamic-block-field:node-test-block-field', t('Custom block field found'));
    // @todo title isn't set, cause we are dealing with the block itself not the instance
    //$this->assertRaw('Recent content</h2>', t('Custom block field found'));
    $this->assertRaw('Submitted by', t('Submitted field found'));
    $this->assertText('This is an extra field made available through "Extra fields" functionality.');

    // Test HTML5 wrappers
    $this->assertNoRaw('<header class="group-header', 'Header not found.');
    $this->assertNoRaw('<footer class="group-right', 'Footer not found.');
    $this->assertNoRaw('<article', 'Article not found.');
    $wrappers = array(
      'region_wrapper[header]' => 'header',
      'region_wrapper[right]' => 'footer',
      'region_wrapper[layout_wrapper]' => 'article',
    );
    $this->dsConfigureUI($wrappers);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('<header class="group-header', 'Header found.');
    $this->assertRaw('<footer class="group-right', 'Footer found.');
    $this->assertRaw('<article', 'Article found.');

    // Let's create a block field, enable the full mode first.
    $edit = array('display_modes_custom[full]' => '1');
    $this->drupalPostForm('admin/structure/types/manage/article/display', $edit, t('Save'));

    // Select layout.
    $layout = array(
      'layout' => 'ds_2col',
    );

    $assert = array(
      'regions' => array(
        'left' => '<td colspan="8">' . t('Left') . '</td>',
        'right' => '<td colspan="8">' . t('Right') . '</td>',
      ),
    );
    $this->dsSelectLayout($layout, $assert, 'admin/structure/types/manage/article/display/full');

    // Create new block field.
    $edit = array(
      'new_block_region' => 'Block region',
      'new_block_region_key' => 'block_region',
    );
    $this->drupalPostForm('admin/structure/types/manage/article/display/full', $edit, t('Save'));
    $this->assertRaw('<td colspan="8">' . t('Block region') . '</td>', 'Block region found');

    // Configure fields
    $fields = array(
      'fields[node_author][region]' => 'left',
      'fields[node_links][region]' => 'left',
      'fields[body][region]' => 'right',
      'fields[dynamic_code_field:node-test_field][region]' => 'block_region',
    );
    $this->dsConfigureUI($fields, 'admin/structure/types/manage/article/display/full');

    // Set block in sidebar

    // @todo fix this

    /*
    $edit = array(
      'blocks[ds_extras_block_region][region]' => 'sidebar_first',
    );
    $this->drupalPostForm('admin/structure/block', $edit, t('Save blocks'));

    // Assert the block is on the node page.
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('Block region</h2>', 'Block region found');
    $this->assertText('Test code field on node ' . $node->id(), 'Post date in block');
    */

    // Change layout via admin/structure/ds/layout-change.
    // First verify that header and footer are not here.
    $this->drupalGet('admin/structure/types/manage/article/display/full');
    $this->assertNoRaw('<td colspan="8">' . t('Header') . '</td>', 'Header region not found');
    $this->assertNoRaw('<td colspan="8">' . t('Footer') . '</td>', 'Footer region not found');

    // Remap the regions.
    $edit = array(
      'ds_left' => 'header',
      'ds_right' => 'footer',
      'ds_block_region' => 'footer',
    );
    $this->drupalPostForm('admin/structure/ds/change-layout/node/article/full/ds_2col_stacked', $edit, t('Save'), array('query' => array('destination' => 'admin/structure/types/manage/article/display/full')));

    // Verify new regions.
    $this->assertRaw('<td colspan="8">' . t('Header') . '</td>', 'Header region found');
    $this->assertRaw('<td colspan="8">' . t('Footer') . '</td>', 'Footer region found');
    $this->assertRaw('<td colspan="8">' . t('Block region') . '</td>', 'Block region found');

    // Verify settings.
    $data = \Drupal::config('ds.layout_settings.node.article.full')->get('settings');
    $this->assertTrue(in_array('node_author', $data['regions']['header']), t('Author is in header'));
    $this->assertTrue(in_array('node_links', $data['regions']['header']), t('Links field is in header'));
    $this->assertTrue(in_array('body', $data['regions']['footer']), t('Body field is in footer'));
    $this->assertTrue(in_array('dynamic_code_field:node-test_field', $data['regions']['footer']), t('Test field is in footer'));

    // Test that a default view mode with no layout is not affected by a disabled view mode.
    $edit = array(
      'layout' => '',
      'display_modes_custom[full]' => FALSE,
    );
    $this->drupalPostForm('admin/structure/types/manage/article/display', $edit, t('Save'));
    $this->drupalGet('node/' . $node->id());
    $this->assertNoText('Test code field on node 1', 'No ds field from full view mode layout');
  }
}
