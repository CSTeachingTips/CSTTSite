
		


<body>

<br> 

<!-- large div containing side panel and all thumbnails -->
<div>

<!-- just the side panel -->
<div class="col-xs-2 col-md-2">
<div class="panel panel-default">

  <div class="panel-heading">
    <h3 class="panel-title">Filters</h3>
    
  </div>
  <div class="panel-body">
    <!-- Printing the filters block -->
    <?php 
	$block = block_load('views','-exp-browse_all-page_1');
	$dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
	print drupal_render($dummyblock);
	?>
  </div>

</div>
</div>

<!-- Tip Panels --> 
<div class="col-xs-10 col-md-10">

<!-- Printing the entire views block of tips-->
<?php 
$block = block_load('views','browse_all-block');
$dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
print drupal_render($dummyblock);
?>
  

  </div>



<!-- end tip panels -->
<div class="col-xs-12 col-md-12">
<p style="font-size:175px;"> <br> </p>
</div>

</div>

</body>


			  