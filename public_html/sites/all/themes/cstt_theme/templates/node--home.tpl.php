<!-- The Home Page -->
<html>

<head>
        <title>
        CS Teaching Tips: A project funded by the NSF (Grant # 1339404)
        </title>
        <meta name="description" content="Computer Science Teaching Tips is an NSF funded project for providing tips to Computer Science educators. Supported by Harvey Mudd College and Sagefox Consulting.">
</head>


<body style="background-color:#E6F5EB;">
  <br>

  <div id="cstt-description" style="text-align:center; ">
    
      <h2 class = "descriptionalign" style="padding-bottom:0px; margin-bottom:-10px;" >Tailor your teaching with our <br>Computer Science Teaching Tips! </h2>
   
  </div>

  <div style="text-align:center;margin-bottom:3%;position:relative;">

    <div id = "centralsearch" style="position:absolute;max-width:500px;left:0;right:0;margin: 0 auto;margin-top:160px;z-index:2;">

      <?php 
        $block = block_load('views','-exp-centralsearch-page');
        $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
        print drupal_render($dummyblock);
      ?>     

    </div>

    <img style = "margin:auto" alt="CS Teaching Tips Logo" src="/../public_html/images/tree1.png" id="cstt-icon"> 
  </div>
  

</body>



</html>

