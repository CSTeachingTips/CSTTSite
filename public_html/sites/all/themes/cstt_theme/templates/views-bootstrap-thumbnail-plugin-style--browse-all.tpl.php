<?php
/**
 * @file views-bootstrap-thumbnail-plugin-style.tpl.php
 * Default simple view template to display Bootstrap Thumbnails.
 *
 * - $rows contains a nested array of rows. Each row contains an array of
 *   columns.
 * - $column_type contains a number (default Bootstrap grid system column type).
 *
 * @ingroup views_templates
 */
?>


<?php 
  $block = block_load('views','-exp-browse_all-page_1');
  $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
  print drupal_render($dummyblock);
  ?>



<div class="col-xs-9 col-md-9">
<div id="views-bootstrap-thumbnail-<?php print $id ?>" class="<?php print $classes ?>">
  <?php if ($options['alignment'] == 'horizontal'): ?>

    <?php foreach ($items as $row): ?>
      <div class="row">
        <?php foreach ($row['content'] as $column): ?>
          <div class="col col-lg-<?php print $column_type ?>">
            <div class="thumbnail">
              <?php print $column['content'] ?>
            </div>
          </div>
        <?php endforeach ?>
      </div>
    <?php endforeach ?>

  <?php else: ?>

    <div class="row">
      <?php foreach ($items as $column): ?>
        <div class="col col-lg-<?php print $column_type ?>">
          <?php foreach ($column['content'] as $row): ?>
            <div class="thumbnail">
              <?php print $row['content'] ?>
            </div>
          <?php endforeach ?>
        </div>
      <?php endforeach ?>
    </div>

  <?php endif ?>
</div>
</div>


<br> <br> <br>