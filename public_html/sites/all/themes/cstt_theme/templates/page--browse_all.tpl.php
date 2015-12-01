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
          <a class="navbar-brand" href="http://csteachingtips.org/"><img src="http://csteachingtips.org/images/navbar.png" id="cstt-logo"/></a>
      </div>

      <!-- NAV BAR -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
              <li><a href="http://csteachingtips.org/browse-all">Browse All Tips</a></li> 
              <li><a href="http://csteachingtips.org/tags">Tags</a>
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

<!-- Line break to even spacing from nav bar -->
<h1 style="margin-bottom:20px;"> <br> </h1>


<!-- the top panel -->
  <div tag = "exposedviews">
   <!-- Printing the exposed filters Views block for the browse all view -->
   <?php 
  $block = block_load('views','-exp-browse_all-page');
  $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
  print drupal_render($dummyblock);
  ?> 
  </div>

  <div tag = "exposed sort">
  <?php
    $block = block_load('search_api_sorts', 'search-sorts');
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

<!-- The footer -->
<div id="footer">
    <div class="container">  
        
        <div class="social col-xs-12 col-md-3" align="center">
            <ul id="navlist">
              <li id="facebook"><a href="https://www.facebook.com/csteachingtips"></a> </li>
              <li id="twitter"><a href="https://twitter.com/CSTeachingTips"></a> </li>
              <li id="google"><a href="https://plus.google.com/u/0/100805836868973386509/"></a> </li>
            </ul>
        </div>
      
        <div class = "col-xs-12 col-md-6" align = "center" id = "footerpadding">
        

          <p class ="text-muted"> To learn more or report problems with the site, please contact us at <a href = "mailto:admin@csteachingtips.org">admin@csteachingtips.org</a> </p>
          <p class="text-muted"> Built with <a href = "http://getbootstrap.com/">Bootstrap</a>.
          						 Footer Icons by <a href = "http://drbl.in/iOiy">Mohammed Alyousfi</a>.  
                                 Powered by <a href = "http://drupal.org/">Drupal</a>. 
                                 <br><p><a href="//www.iubenda.com/privacy-policy/119533" class="iubenda-white iubenda-embed" title="Privacy Policy">Privacy Policy</a><script type="text/javascript">(function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src = "//cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);</script>
          </p>   
          </div>
        
        <div class= "support-logos" align = "right" id = "footerpadding">
          <a href="http://www.nsf.gov/">
            <img alt= "National Science Foundation" src="http://csteachingtips.org/images/nsf_logo.png">
          </a>
          <a alt= "SageFox Consulting Group" href="http://www.sagefoxgroup.com/" style="margin-left:13px;">
            <img src="http://csteachingtips.org/images/sagefoxlogo_75tall.png">
          </a>
          <a href="http://www.hmc.edu">
            <img alt= "Harvey Mudd College" src="http://www.csteachingtips.org/images/HMClogo_75sq.png" style="margin-left:13px;">
          </a>

        </div>

          
        </div> 
</div>


