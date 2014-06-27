<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 * @see html.tpl.php
 *
 * @ingroup themeable
 */
?>

<html>


<!-- ======================= VERSION 2 =================
 == This version has all of the content over 2 screens==
 ======================================================= 
-->

<head>
        <title>

        </title>
        <meta name="description" content="CS Teaching Tips does things">
        <meta property="og:type" content="website" />
        <meta property="og:url" content="m" />
        <meta property="og:image" content="m" />
        <meta property="og:title" content="!"/>
        <meta property="og:site_name" content="hi!" />
        <meta property="og:description" content="hi!" />
        <meta property="fb:app_id" content="560604524019670"/>
        <meta name="viewport" content="width=device-width, initial-scale = 1.0">
        <meta charset="utf-8">
        <link rel = "stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">
        <link rel "stylesheet" href = "style.css">
</head>
  
<!-- Navbar goes here -->
<body>
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><img src="http://csteachingtips.org/images/CS-teaching-tips-01.png"/></a>
      </div>

      <!-- NAV BAR -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tips<b class="caret"></b></a>
                <ul class="dropdown-menu">
                      <li><a href="#">Delivering Content</a></li>
                      <li><a href="#">Managing & Assessing</a></li>
                      <li><a href="#">Organizing Curriculum</a></li>
                      <li class="divider"></li>
                      <li><a href="#">Browse All</a></li>
                  </ul>
              </li>
              <li><a href="#">About</a></li>
              <li><a href="#">Contribute</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
              <li><a href="#">Login</a></li>
          </ul>
          
             <?php $block = block_load('search','form');
              $dummysearch = _block_get_renderable_array(_block_render_blocks(array($block)));
              print drupal_render($dummysearch); 
              ?>
              
        
      </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>


<!-- End of navbar -->

<!--  Home screen center button and mission statement -->

<br> 
<br>
<div class="jumbotron">
  <br>
  <h1> 
    <img src ="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xfa1/t1.0-1/p50x50/1900081_632479410157781_1725500249_n.png"> 
      CS Teaching Tips 
  </h1>
    <p>
      <em>From personal stories to published papers, we've done the reading for you. Connect and teach like never before. 
      <br> <br> 
      </em>
    </p>
    <p>
      <a class="btn btn-primary btn-lg" role="button">Start Browsing Tips <span class="glyphicon glyphicon-chevron-right"> </span>
      </a>
    </p>
    <br> 
</div>

<!-- end of center button and mission statement -->



<!-- Space. TODO: Find a better way to insert whitespace.
 Using a ton of line breaks is probably bad form, but the <div style="position ..."> 
 was really buggy, and it broke bootstrap's grid layout. --> 

<br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> 



<!-- Start of Panels --> 

<div>

 <!-- Organizing Curriculum--> 
  <div class="col-xs-4 col-md-4">
    <a href="#" class="thumbnail">
      <center>
        <img src="http://i1273.photobucket.com/albums/y403/dylankbaker/forcedlaugh_150sq_zpse6ca30f9.jpg">
          <div class="caption">
            <h3> Organizing Curriculum </h3>
          </div>
      </center> 
    </a>
  </div>


 <!-- Delivering Content --> 

  <div class="col-xs-4 col-md-4">
    <a href="#" class="thumbnail">
      <center>
        <img src="http://i1273.photobucket.com/albums/y403/dylankbaker/forcedlaugh_150sq_zpse6ca30f9.jpg">
          <div class="caption"> 
            <h3> Delivering Content </h3> 
          </div>
      </center> 
    </a> 
  </div>


<!-- Managing and Assessing--> 
  <div class="col-xs-4 col-md-4">
    <a href="#" class="thumbnail">
      <center>
        <img src="http://i1273.photobucket.com/albums/y403/dylankbaker/forcedlaugh_150sq_zpse6ca30f9.jpg">  
          <div class="caption">
            <h3> Managing and Assessing </h3>
          </div>
      </center> 
    </a>
  </div>

</div>


<!-- end panels -->

<br>


<!-- Footer -->

<h1> <br> <br> <br> <br> <br> <br> <br> <br> </h1>

<!-- put the things in line — use "inline"--> 

<div id="footer">
      <div class="container">
        <div align="center" style="display:block;">
          <p class="text-muted">Connect:</p>
          
          <ul class="social-media">
            <li class="facebook"><a href="https://www.facebook.com/harveymuddcollege"></a></li>
            <li class="twitter"><a href="https://twitter.com/harveymudd"></a></li>
            <li class="google"><a href="http://www.flickr.com/photos/harvey-mudd-college/"></a></li>
          </ul>
        
        </div>
        <br>
        <div align="center" style="display:block;">
          <p class="text-muted">Powered by:</p>
          <a href="http://www.nsf.gov/">
            <img src="http://csteachingtips.org/images/nsf_logo.png">
          </a>
          <a href="http://www.sagefoxgroup.com/" style="margin-left:13px;">
            <img src="http://csteachingtips.org/images/sagefoxlogo_75tall.png">
          </a>
          <a href="http://www.hmc.edu">
            <img src="http://csteachingtips.org/images/HMClogo_75sq.png" style="margin-left:13px;">
          </a>
        </div>
      </div>
</div>

<!-- end footer -->

<!-- A little extra space before the end of the page -->

<br>
    

    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>


</body>

</html>





  <div id="page-wrapper"><div id="page">

    <div id="header"><div class="section clearfix">

      <?php print render($page['header']); ?>

    </div></div> <!-- /.section, /#header -->


    <div id="footer"><div class="section">
      <?php print render($page['footer']); ?>
    </div></div> <!-- /.section, /#footer -->

  </div></div> <!-- /#page, /#page-wrapper -->
