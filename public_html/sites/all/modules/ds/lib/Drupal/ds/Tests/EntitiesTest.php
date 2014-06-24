<?php

/**
 * @file
 * Definition of Drupal\ds\Tests\EntitiesTest.
 */

namespace Drupal\ds\Tests;

use Drupal\Component\Utility\String;

class EntitiesTest extends BaseTest {

  /**
   * Implements getInfo().
   */
  public static function getInfo() {
    return array(
      'name' => t('Node display'),
      'description' => t('Tests for display of nodes and fields.'),
      'group' => t('Display Suite'),
    );
  }

  /**
   * Utility function to setup for all kinds of tests.
   *
   * @param $label
   *   How the body label must be set.
   */
  function entitiesTestSetup($label = 'above') {

    // Create a node.
    $settings = array('type' => 'article', 'promote' => 1);
    $node = $this->drupalCreateNode($settings);

    // Create field CSS classes.
    $edit = array('fields' => "test_field_class\ntest_field_class_2|Field class 2");
    $this->drupalPostForm('admin/structure/ds/classes', $edit, t('Save configuration'));

    // Create a token and php field.
    $token_field = array(
      'name' => 'Token field',
      'id' => 'token_field',
      'entities[node]' => '1',
      'code[value]' => '<div class="token-class">[node:title]</span>',
      'use_token' => '1',
    );
    $php_field = array(
      'name' => 'PHP field',
      'id' => 'php_field',
      'entities[node]' => '1',
      'code[value]' => "<?php echo 'I am a PHP field'; ?>",
      'use_token' => '0',
    );
    $this->dsCreateCodeField($token_field);
    $this->dsCreateCodeField($php_field);

    // Select layout.
    $this->dsSelectLayout();

    // Configure fields.
    $fields = array(
      'fields[dynamic_code_field:node-token_field][region]' => 'header',
      'fields[dynamic_code_field:node-php_field][region]' => 'left',
      'fields[body][region]' => 'right',
      'fields[node_link][region]' => 'footer',
      'fields[body][label]' => $label,
      'fields[node_submitted_by][region]' => 'header',
    );
    $this->dsConfigureUI($fields);

    return $node;
  }

  /**
   * Utility function to clear field settings.
   */
  function entitiesClearFieldSettings() {
    $field_settings = config_get_storage_names_with_prefix('ds_field_settings');
    foreach ($field_settings as $config) {
      \Drupal::config($config)->delete();
    }
    cache()->deleteTags(array('ds_fields_info' => TRUE));
    cache()->delete('ds_field_settings');
  }

  /**
   * Set the label.
   */
  function entitiesSetLabelClass($label, $text = '', $class = '', $hide_colon = FALSE) {
    $edit = array(
      'fields[body][label]' => $label,
    );
    if (!empty($text)) {
      $edit['fields[body][settings_edit_form][settings][ft][lb]'] = $text;
    }
    if (!empty($class)) {
      $edit['fields[body][settings_edit_form][settings][ft][classes][]'] = $class;
    }
    if ($hide_colon) {
      $edit['fields[body][settings_edit_form][settings][ft][lb-col]'] = '1';
    }
    $this->dsEditFormatterSettings($edit);
  }

  /**
   * Test basic node display fields.
   */
  function testDSNodeEntity() {

    $node = $this->entitiesTestSetup();
    $node_author = user_load($node->uid);

    // Look at node and verify token and block field.
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('view-mode-full', 'Template file found (in full view mode)');
    $this->assertRaw('<div class="token-class">' . $node->title . '</span>', t('Token field found'));
    $this->assertRaw('I am a PHP field', t('PHP field found'));
    $this->assertRaw('group-header', 'Template found (region header)');
    $this->assertRaw('group-footer', 'Template found (region footer)');
    $this->assertRaw('group-left', 'Template found (region left)');
    $this->assertRaw('group-right', 'Template found (region right)');
    $this->assertPattern('/<div[^>]*>Submitted[^<]*<a[^>]+href="\/user\/' . $node_author->uid . '"[^>]*>' . String::checkPlain($node_author->name) . '<\/a>.<\/div>/', t('Submitted by line found'));

    // Configure teaser layout.
    $teaser = array(
      'layout' => 'ds_2col',
    );
    $teaser_assert = array(
      'regions' => array(
        'left' => '<td colspan="8">' . t('Left') . '</td>',
        'right' => '<td colspan="8">' . t('Right') . '</td>',
      ),
    );
    $this->dsSelectLayout($teaser, $teaser_assert, 'admin/structure/types/manage/article/display/teaser');

    $fields = array(
      'fields[dynamic_code_field:node-token_field][region]' => 'left',
      'fields[dynamic_code_field:node-php_field][region]' => 'left',
      'fields[body][region]' => 'right',
      'fields[node_links][region]' => 'right',
    );
    $this->dsConfigureUI($fields, 'admin/structure/types/manage/article/display/teaser');

    // Switch view mode on full node page.
    $edit = array('ds_switch' => 'teaser');
    $this->drupalPostForm('node/' . $node->id() . '/edit', $edit, t('Save and keep published'));
    $this->assertRaw('view-mode-teaser', 'Switched to teaser mode');
    $this->assertRaw('group-left', 'Template found (region left)');
    $this->assertRaw('group-right', 'Template found (region right)');
    $this->assertNoRaw('group-header', 'Template found (no region header)');
    $this->assertNoRaw('group-footer', 'Template found (no region footer)');

    $edit = array('ds_switch' => '');
    $this->drupalPostForm('node/' . $node->id() . '/edit', $edit, t('Save and keep published'));
    $this->assertRaw('view-mode-full', 'Switched to full mode again');

    // Test all options of a block field.
    /*$block = array(
      'name' => 'Test block field',
      'field' => 'test_block_field',
      'entities[node]' => '1',
      'block' => 'node|recent',
      'block_render' => DS_BLOCK_TEMPLATE,
    );
    $this->dsCreateBlockField($block);
    $fields = array(
      'fields[dynamic_block_field:node-test_block_field][region]' => 'left',
      'fields[dynamic_code_field:node-token_field][region]' => 'hidden',
      'fields[dynamic_code_field:node-php_field][region]' => 'hidden',
      'fields[body][region]' => 'hidden',
      'fields[links][region]' => 'hidden',
    );
    $this->dsConfigureUI($fields);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('Recent content</h2>');

    $block = array(
      'block_render' => DS_BLOCK_TITLE_CONTENT,
    );
    $this->dsCreateBlockField($block, 'admin/structure/ds/fields/manage_block/test_block_field', FALSE);
    $this->drupalGet('node/' . $node->id());
    $this->assertNoRaw('<h2>Recent content</h2>');
    $this->assertRaw('Recent content');

    $block = array(
      'block_render' => DS_BLOCK_CONTENT,
    );
    $this->dsCreateBlockField($block, 'admin/structure/ds/fields/manage_block/test_block_field', FALSE);
    $this->drupalGet('node/' . $node->id());
    $this->assertNoRaw('<h2>Recent content</h2>');
    $this->assertNoRaw('Recent content');*/

    // Test revisions. Enable the revision view mode
    $edit = array('view_modes_custom[revision]' => '1');
    $this->drupalPostForm('admin/structure/types/manage/article/display', $edit, t('Save'));

    // Select layout and configure fields.
    $edit = array(
      'layout' => 'ds_2col',
    );
    $assert = array(
      'regions' => array(
        'left' => '<td colspan="8">' . t('Left') . '</td>',
        'right' => '<td colspan="8">' . t('Right') . '</td>',
      ),
    );
    $this->dsSelectLayout($edit, $assert, 'admin/structure/types/manage/article/display/revision');
    $edit = array(
      'fields[body][region]' => 'left',
      'fields[node_links][region]' => 'right',
      'fields[node_author][region]' => 'right',
    );
    $this->dsConfigureUI($edit, 'admin/structure/types/manage/article/display/revision');

    // Create revision of the node.
    $edit = array(
      'revision' => TRUE,
      'log' => 'Test revision',
    );
    $this->drupalPostForm('node/' . $node->id() . '/edit', $edit, t('Save and keep published'));
    $this->assertText('Revisions');

    // Assert revision is using 2 col template.
    $this->drupalGet('node/' . $node->id() . '/revisions/1/view');
    $this->assertText('Body:', 'Body label');

    // Assert full view is using stacked template.
    $this->drupalGet('node/' . $node->id());
    $this->assertNoText('Body:', 'Body label');

    // Test formatter limit on article with tags.
    $edit = array(
      'ds_switch' => '',
      'field_tags[und]' => 'Tag 1, Tag 2'
    );
    $this->drupalPostForm('node/' . $node->id() . '/edit', $edit, t('Save and keep published'));
    $edit = array(
      'fields[field_tags][region]' => 'right',
    );
    $this->dsConfigureUI($edit, 'admin/structure/types/manage/article/display');
    $this->drupalGet('node/' . $node->id());
    $this->assertText('Tag 1');
    $this->assertText('Tag 2');
    $edit = array(
      'fields[field_tags][plugin][limit]' => '1',
    );
    $this->dsConfigureUI($edit, 'admin/structure/types/manage/article/display');
    $this->drupalGet('node/' . $node->id());
    $this->assertText('Tag 1');
    $this->assertNoText('Tag 2');

    // Test \Drupal\Component\Utility\String::checkPlain() on ds_render_field() with the title field.
    $edit = array(
      'fields[title][region]' => 'right',
    );
    $this->dsConfigureUI($edit, 'admin/structure/types/manage/article/display');
    $edit = array(
      'title' => 'Hi, I am an article <script>alert(\'with a javascript tag in the title\');</script>',
    );
    $this->drupalPostForm('node/' . $node->id() . '/edit', $edit, t('Save and keep published'));
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('<h2>Hi, I am an article &lt;script&gt;alert(&#039;with a javascript tag in the title&#039;);&lt;/script&gt;</h2>');
  }

  /**
   * Tests on field templates.
   */
  function testDSFieldTemplate() {

    // Get a node.
    $node = $this->entitiesTestSetup('hidden');
    $body_field = $node->body[$node->langcode][0]['value'];

    // -------------------------
    // Default theming function.
    // -------------------------
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"field field-name-body field-type-text-with-summary field-label-hidden\"><div class=\"field-items\"><div class=\"field-item even\" property=\"content:encoded\"><p>" . $body_field . "</p>
</div></div></div>");

    $this->entitiesSetLabelClass('above');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"field field-name-body field-type-text-with-summary field-label-above\"><div class=\"field-label\">Body:&nbsp;</div><div class=\"field-items\"><div class=\"field-item even\" property=\"content:encoded\"><p>" . $body_field . "</p>
</div></div></div>");

    $this->entitiesSetLabelClass('above', 'My body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"field field-name-body field-type-text-with-summary field-label-above\"><div class=\"field-label\">My body:&nbsp;</div><div class=\"field-items\"><div class=\"field-item even\" property=\"content:encoded\"><p>" . $body_field . "</p>
</div></div></div>");

    $this->entitiesSetLabelClass('hidden', '', 'test_field_class');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"field field-name-body field-type-text-with-summary field-label-hidden test_field_class\"><div class=\"field-items\"><div class=\"field-item even\" property=\"content:encoded\"><p>" . $body_field . "</p>
</div></div></div>");

    $this->entitiesClearFieldSettings();

    // -----------------------
    // Reset theming function.
    // -----------------------
    $edit = array(
      'fs1[ft-default]' => 'theme_ds_field_reset',
    );
    $this->drupalPostForm('admin/structure/ds/list/extras', $edit, t('Save configuration'));
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('above');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"label-above\">Body:&nbsp;</div><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('inline');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"label-inline\">Body:&nbsp;</div><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('above', 'My body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"label-above\">My body:&nbsp;</div><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('inline', 'My body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"label-inline\">My body:&nbsp;</div><p>" . $body_field . "</p>");

    \Drupal::config('ds.extras')->set('ft-kill-colon', TRUE)->save();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"label-inline\">My body</div><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('hidden');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <p>" . $body_field . "</p>");
    $this->entitiesClearFieldSettings();

    // ----------------------
    // Custom field function.
    // ----------------------

    // With outer wrapper.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][func]' => 'theme_ds_field_expert',
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
    );
    $this->dsEditFormatterSettings($edit);

    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div><p>" . $body_field . "</p>
</div>  </div>");

    // With outer div wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][ow-cl]' => 'ow-class'
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class\"><p>" . $body_field . "</p>
</div>  </div>");

    // With outer span wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'span',
      'fields[body][settings_edit_form][settings][ft][ow-cl]' => 'ow-class-2'
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <span class=\"ow-class-2\"><p>" . $body_field . "</p>
</span>  </div>");

    // Clear field settings.
    $this->entitiesClearFieldSettings();

    // With outer wrapper and field items wrapper.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][func]' => 'theme_ds_field_expert',
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][fis]' => '1',
      'fields[body][settings_edit_form][settings][ft][fis-el]' => 'div'
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div><div><p>" . $body_field . "</p>
</div></div>  </div>");

    // With outer wrapper and field items div wrapper with class.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][fis]' => '1',
      'fields[body][settings_edit_form][settings][ft][fis-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][fis-cl]' => 'fi-class'
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div><div class=\"fi-class\"><p>" . $body_field . "</p>
</div></div>  </div>");

    // With outer wrapper and field items span wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][fis]' => '1',
      'fields[body][settings_edit_form][settings][ft][fis-el]' => 'span',
      'fields[body][settings_edit_form][settings][ft][fis-cl]' => 'fi-class'
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div><span class=\"fi-class\"><p>" . $body_field . "</p>
</span></div>  </div>");

    // With outer wrapper class and field items span wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][settings][ft][fis]' => '1',
      'fields[body][settings_edit_form][settings][ft][fis-el]' => 'span',
      'fields[body][settings_edit_form][settings][ft][fis-cl]' => 'fi-class'
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class\"><span class=\"fi-class\"><p>" . $body_field . "</p>
</span></div>  </div>");

    // With outer wrapper span class and field items span wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'span',
      'fields[body][settings_edit_form][settings][ft][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][settings][ft][fis]' => '1',
      'fields[body][settings_edit_form][settings][ft][fis-el]' => 'span',
      'fields[body][settings_edit_form][settings][ft][fis-cl]' => 'fi-class-2'
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <span class=\"ow-class\"><span class=\"fi-class-2\"><p>" . $body_field . "</p>
</span></span>  </div>");

    // Clear field settings.
    $this->entitiesClearFieldSettings();

    // With field item div wrapper.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][func]' => 'theme_ds_field_expert',
      'fields[body][settings_edit_form][settings][ft][fi]' => '1',
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div><p>" . $body_field . "</p>
</div>  </div>");

    // With field item span wrapper.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][fi]' => '1',
      'fields[body][settings_edit_form][settings][ft][fi-el]' => 'span',
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <span><p>" . $body_field . "</p>
</span>  </div>");

    // With field item span wrapper and class and odd even.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][fi]' => '1',
      'fields[body][settings_edit_form][settings][ft][fi-el]' => 'span',
      'fields[body][settings_edit_form][settings][ft][fi-cl]' => 'fi-class',
      'fields[body][settings_edit_form][settings][ft][fi-odd-even]' => '1',
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <span class=\"even fi-class\"><p>" . $body_field . "</p>
</span>  </div>");

    // With fis and fi.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][fis]' => '1',
      'fields[body][settings_edit_form][settings][ft][fis-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][fis-cl]' => 'fi-class-2',
      'fields[body][settings_edit_form][settings][ft][fi]' => '1',
      'fields[body][settings_edit_form][settings][ft][fi-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][fi-cl]' => 'fi-class',
      'fields[body][settings_edit_form][settings][ft][fi-odd-even]' => '1',
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"fi-class-2\"><div class=\"even fi-class\"><p>" . $body_field . "</p>
</div></div>  </div>");
    // With all wrappers.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][settings][ft][fis]' => '1',
      'fields[body][settings_edit_form][settings][ft][fis-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][fis-cl]' => 'fi-class-2',
      'fields[body][settings_edit_form][settings][ft][fi]' => '1',
      'fields[body][settings_edit_form][settings][ft][fi-el]' => 'span',
      'fields[body][settings_edit_form][settings][ft][fi-cl]' => 'fi-class',
      'fields[body][settings_edit_form][settings][ft][fi-odd-even]' => '1',
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class\"><div class=\"fi-class-2\"><span class=\"even fi-class\"><p>" . $body_field . "</p>
</span></div></div>  </div>");

    // With all wrappers and attributes.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][settings][ft][ow-at]' => 'name="ow-att"',
      'fields[body][settings_edit_form][settings][ft][fis]' => '1',
      'fields[body][settings_edit_form][settings][ft][fis-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][fis-cl]' => 'fi-class-2',
      'fields[body][settings_edit_form][settings][ft][fis-at]' => 'name="fis-att"',
      'fields[body][settings_edit_form][settings][ft][fi]' => '1',
      'fields[body][settings_edit_form][settings][ft][fi-el]' => 'span',
      'fields[body][settings_edit_form][settings][ft][fi-cl]' => 'fi-class',
      'fields[body][settings_edit_form][settings][ft][fi-at]' => 'name="fi-at"',
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class\" name=\"ow-att\"><div class=\"fi-class-2\" name=\"fis-att\"><span class=\"even fi-class\" name=\"fi-at\"><p>" . $body_field . "</p>
</span></div></div>  </div>");

    // Remove attributes.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][settings][ft][ow-at]' => '',
      'fields[body][settings_edit_form][settings][ft][fis]' => '1',
      'fields[body][settings_edit_form][settings][ft][fis-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][fis-cl]' => 'fi-class-2',
      'fields[body][settings_edit_form][settings][ft][fis-at]' => '',
      'fields[body][settings_edit_form][settings][ft][fi]' => '1',
      'fields[body][settings_edit_form][settings][ft][fi-el]' => 'span',
      'fields[body][settings_edit_form][settings][ft][fi-cl]' => 'fi-class',
      'fields[body][settings_edit_form][settings][ft][fi-at]' => '',
    );
    $this->dsEditFormatterSettings($edit);

    // Label tests with custom function.
    $this->entitiesSetLabelClass('above');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class\"><div class=\"label-above\">Body:&nbsp;</div><div class=\"fi-class-2\"><span class=\"even fi-class\"><p>" . $body_field . "</p>
</span></div></div>  </div>");

    $this->entitiesSetLabelClass('inline');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class\"><div class=\"label-inline\">Body:&nbsp;</div><div class=\"fi-class-2\"><span class=\"even fi-class\"><p>" . $body_field . "</p>
</span></div></div>  </div>");

    $this->entitiesSetLabelClass('above', 'My body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class\"><div class=\"label-above\">My body:&nbsp;</div><div class=\"fi-class-2\"><span class=\"even fi-class\"><p>" . $body_field . "</p>
</span></div></div>  </div>");

    $this->entitiesSetLabelClass('inline', 'My body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class\"><div class=\"label-inline\">My body:&nbsp;</div><div class=\"fi-class-2\"><span class=\"even fi-class\"><p>" . $body_field . "</p>
</span></div></div>  </div>");

    $this->entitiesSetLabelClass('inline', 'My body', '', TRUE);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class\"><div class=\"label-inline\">My body</div><div class=\"fi-class-2\"><span class=\"even fi-class\"><p>" . $body_field . "</p>
</span></div></div>  </div>");

    $this->entitiesSetLabelClass('hidden');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class\"><div class=\"fi-class-2\"><span class=\"even fi-class\"><p>" . $body_field . "</p>
</span></div></div>  </div>");

    // Test default classes on outer wrapper.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][settings][ft][ow-def-cl]' => '1',
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class field field-name-body field-type-text-with-summary field-label-hidden\"><div class=\"fi-class-2\"><span class=\"even fi-class\"><p>" . $body_field . "</p>
</span></div></div>  </div>");

    // Test default attributes on field item.
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][ow]' => '1',
      'fields[body][settings_edit_form][settings][ft][ow-el]' => 'div',
      'fields[body][settings_edit_form][settings][ft][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][settings][ft][ow-def-cl]' => '1',
      'fields[body][settings_edit_form][settings][ft][fi-def-at]' => '1',
    );
    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    <div class=\"ow-class field field-name-body field-type-text-with-summary field-label-hidden\"><div class=\"fi-class-2\"><span class=\"even fi-class\"  property=\"content:encoded\"><p>" . $body_field . "</p>
</span></div></div>  </div>");

    // Use the test field theming function to test that this function is
    // registered in the theme registry through ds_extras_theme().
    $edit = array(
      'fields[body][settings_edit_form][settings][ft][func]' => 'ds_test_theming_function',
    );

    $this->dsEditFormatterSettings($edit);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
    Testing field output through custom function  </div>");
  }
}
