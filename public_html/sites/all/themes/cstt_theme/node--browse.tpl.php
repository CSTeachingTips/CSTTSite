
			
<!--Printing the content from the views module -->
			  <?php $block = block_load('views','browse_all-block');
              $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
              print drupal_render($dummyblock); 
              ?>