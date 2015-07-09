<!-- The Home Page -->
<html>

<head>
        <title>
        CS Teaching Tips: A project funded by the NSF (Grant # 1339404)
        </title>
        <meta name="description" content="Computer Science Teaching Tips is an NSF funded project for providing tips to Computer Science educators. Supported by Harvey Mudd College and Sagefox Consulting.">
</head>


<body style="background-color:#E6F5EB;">

  <!-- Line break to move past navbar -->
  <h5 style="margin-bottom:11px;"> <br> </h5>

  <div class = "container-fluid">
    <!-- Mission Statement -->
    <div id="cstt-description"> 
        <h2 class = "descriptionalign">Tailor your teaching with our <br>Computer Science Teaching Tips! </h2>     
    </div>

    <!-- Logo and Search Bar -->
    <div id = "treesearch">
      <div id = "centralsearch" >
        <?php 
          $block = block_load('views','-exp-centralsearch-page');
          $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
          print drupal_render($dummyblock);
        ?>     
      </div>
     
      <img alt="CS Teaching Tips Logo" src="http://www.csteachingtips.org/images/tree2.png" id="cstt-icon">    
    </div>


    <!-- Selects Search Bar when page loads, and places placeholder text in it -->
    <script>
      var att = document.createAttribute("placeholder");
      att.value = "Search Tips";
      document.querySelector("#centralsearch #edit-search-api-views-fulltext").setAttributeNode(att);

      document.querySelector("#centralsearch #edit-search-api-views-fulltext").focus();
      document.querySelector("#centralsearch #edit-search-api-views-fulltext").select();
    </script>

    <br>
  </div>

  
</body>



</html>

