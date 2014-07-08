<!-- just the side panel -->
<div class="col-xs-12 col-md-3">
<div class="panel panel-default">

  <div class="panel-heading">
    <h3 class="panel-title">Filters</h3>
  </div>
  <div class="panel-body">
   
   <?php 
  $block = block_load('views','-exp-browse_all-page');
  $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
  print drupal_render($dummyblock);
  ?>
  
  </div>
</div>
</div>
<!--- end sidebar -->

<div class="col-xs-12 col-md-9">


<?php print render($page['content']); ?>

</div>

