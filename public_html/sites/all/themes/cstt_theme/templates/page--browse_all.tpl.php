<!-- The Browse All Page -->

<!-- Nav bar for browse all page  -->
 <nav class="navbar navbar-default navbar-fixed-top" role="navigation" id = "navbar">
      <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://csteachingtips.org"><img src="http://i1194.photobucket.com/albums/aa379/Kahnyri/navbar.png" id="cstt-logo"/></a>
      </div>

      <!-- NAV BAR -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
              <li><a href="http://csteachingtips.org/browse-all">Browse All Tips</a></li> 
              <li><a href="http://csteachingtips.org/about">About</a></li>
              <li><a href="http://csteachingtips.org/contribute">Contribute Tips</a></li>
          </ul>
          <ul class = "nav navbar-nav navbar-right">    
              <!-- Printing the Views exposed form block that contains the search bar for View Clone of Browse All -->
              <?php 
              $block = block_load('views','-exp-clone_of_browse_all-page');
              $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
              print drupal_render($dummyblock);
              ?>           
          </ul>
                   
      </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

    <h3> <br> </h3>


<!-- the top panel -->

  
  <div tag = "exposedviews">
   <!-- Printing the exposed filters Views block for the browse all view -->
   <?php 
  $block = block_load('views','-exp-browse_all-page');
  $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
  print drupal_render($dummyblock);
  ?>
  </div>
  

<!--- end top panel -->


<div class="views-content">

<!-- Printing the content of the page, which in this case is the View Browse All -->
<?php print render($page['content']); ?>

</div>

<p style="font-size:15px"> <br> <br> </p>


<h3> <br> </h3>

<!-- The footer for the browse all page -->
<div id="footer">
    <div class="container">  
        
        <div class="social" align="center">
          <p class="text-muted">Connect:</p>
            <ul id="navlist">
              <li id="facebook"><a href="https://www.facebook.com/csteachingtips"></a> </li>
              <li id="twitter"><a href="https://twitter.com/CSTeachingTips"></a> </li>
              <li id="google"><a href="https://plus.google.com/u/0/103378302928119467203/"></a> </li>
            </ul>
        </div>
        
        <h3> <br> </h3>
        
        <div align="center">
          <p class="text-muted">Supported by:</p>
          <a href="http://www.nsf.gov/">
            <img alt= "National Science Foundation" src="http://www.csteachingtips.org/images/nsf_logo.png">
          </a>
          <a href="http://www.sagefoxgroup.com/" style="margin-left:13px;">
            <img alt= "SageFox Consulting Group" src="http://www.csteachingtips.org/images/sagefoxlogo_75tall.png">
          </a>
          <a href="http://www.hmc.edu">
            <img alt= "Harvey Mudd College" src="http://www.csteachingtips.org/images/HMClogo_75sq.png" style="margin-left:13px;">
          </a>

        <h6> <br> </h6>
          <p class ="text-muted"> For more information or to report a bug, contact us at <a href = "mailto:admin@csteachingtips.org">admin@csteachingtips.org</a> </p>
          <p class="text-muted"> Built with <a href = "http://getbootstrap.com/">Bootstrap</a>. 
                                 Icons from <a href = "http://glyphicons.com/">Glyphicons</a>. 
                                 Powered by <a href = "http://drupal.org/">Drupal</a>. 
          </p>


        </div>
    </div>  
      <h3> <br> </h3>
</div>




