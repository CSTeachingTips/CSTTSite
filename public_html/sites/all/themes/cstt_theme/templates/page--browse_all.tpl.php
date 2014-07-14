
 <nav class="navbar navbar-default navbar-fixed-top" role="navigation" id="navbar">
      <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://csteachingtips.org"><img src="http://www.csteachingtips.org/images/csteachingtips.png" id="cstt-logo"/></a>
      </div>

      <!-- NAV BAR -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tips<b class="caret"></b></a>
                <ul class="dropdown-menu">
                      <li><a href="http://csteachingtips.org/browse-all?field_category_tid%5B%5D=2&keys=">Organizing Curriculum</a></li>
                      <li><a href="http://csteachingtips.org/browse-all?field_category_tid%5B%5D=3&keys=">Delivering Content</a></li>
                      <li><a href="http://csteachingtips.org/browse-all?field_category_tid%5B%5D=4&keys=">Managing & Assessing</a></li>
                      <li class="divider"></li>
                      <li><a href="http://csteachingtips.org/browse-all">Browse All</a></li>
                  </ul>
              </li>
              <li><a href="http://csteachingtips.org/about">About</a></li>
              <li><a href="http://csteachingtips.org/contribute">Contribute</a></li>
          </ul>
              
            <div id = "fixingsearchform">
              <?php 
  $block = block_load('views','-exp-clone_of_browse_all-page');
  $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
  print drupal_render($dummyblock);
  ?>           
           </div>
             
          
      </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

    <h3> <br> </h3>


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

<p style="font-size:200px"> <br> <br> </p>


<h3> <br> </h3>

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
          <p class="text-muted">Powered by:</p>
          <a href="http://www.nsf.gov/">
            <img src="http://www.csteachingtips.org/images/nsf_logo.png">
          </a>
          <a href="http://www.sagefoxgroup.com/" style="margin-left:13px;">
            <img src="http://www.csteachingtips.org/images/sagefoxlogo_75tall.png">
          </a>
          <a href="http://www.hmc.edu">
            <img src="http://www.csteachingtips.org/images/HMClogo_75sq.png" style="margin-left:13px;">
          </a>

        <h6> <br> </h6>
          <p class="text-muted"> Built with <a href = "http://getbootstrap.com/">Bootstrap</a>. 
                                 Icons from <a href = "http://glyphicons.com/">Glyphicons</a>. 
                                 Powered by <a href = "http://drupal.org/">Drupal</a>. 
          </p>

        </div>
    </div>  
      <h3> <br> </h3>
</div>




