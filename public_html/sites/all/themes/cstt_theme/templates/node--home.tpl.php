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

  <div class = "container-fluid">
  <div id="cstt-description" style="text-align:center; ">
    
      <h2 class = "descriptionalign" style="padding-bottom:0px; margin-bottom:-10px;" >Tailor your teaching with our <br>Computer Science Teaching Tips! </h2>
   
  </div>

  <div style="text-align:center;position:relative">

    <div id = "centralsearch" >
    

      <?php 
        $block = block_load('views','-exp-centralsearch-page');
        $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
        print drupal_render($dummyblock);
      ?>     

    </div>
    <div style="height:70%">
    <img style = "margin:auto; margin-top:0px height:100%" alt="CS Teaching Tips Logo" src="/../public_html/images/tree2.png" id="cstt-icon"> 
    </div>
  </div>
  </div>

  <!-- Selects Search Bar when page loads, and places placeholder text in it -->
  <script>
    var att = document.createAttribute("placeholder");
    att.value = "Search Tips";
    document.querySelector("#centralsearch #edit-search-api-views-fulltext").setAttributeNode(att);

    document.querySelector("#centralsearch #edit-search-api-views-fulltext").focus();
    document.querySelector("#centralsearch #edit-search-api-views-fulltext").select();
  </script>

</body>



</html>

