<!-- Responsible for printing the nav bar and footer on all pages except 
the Browse All page. In a more typical Drupal site, this template would
be used to print content on almost every page of the site. See 
/modules/system/page.tpl.php for the default implementation of this file -->

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
<?php include "analyticstracking.php"; ?>
<?php include_once("analyticstracking.php") ?>

<head prefix="og: http://ogp.me/ns#">
<meta property="og:image" content="http://csteachingtips.org/images/tree.png">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="400">
<meta property="og:image:height" content="417">
</head>

<!-- The nav bar -->
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
          
          <a class="navbar-brand" href="http://csteachingtips.org/"><img alt="CS Teaching Tips Logo" src="http://csteachingtips.org/images/navbar.png" id="cstt-logo"/></a>
      </div>

      <!-- NAV BAR -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
              <li><a href="http://csteachingtips.org/browse-all">Browse All Tips</a></li>
              <li><a href="http://csteachingtips.org/tags">Tags</a></li>
              <li><a href="http://csteachingtips.org/about">About</a></li>
              <li><a href="http://csteachingtips.org/contribute">Contribute Tips</a></li>
          </ul>
          <ul class = "nav navbar-nav navbar-right">    
              <!-- Printing the Views exposed form block for the View Clone of Browse All Page -->
              <?php 
              $block = block_load('views','-exp-clone_of_browse_all-page');
              $dummyblock = _block_get_renderable_array(_block_render_blocks(array($block)));
              print drupal_render($dummyblock);
              ?>           
          </ul>
             
          
      </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>



<!-- CONTENT -->
<div>
 <!-- Printing the content of a Drupal page - Important -->
 <?php print render($page['content']); ?>
</div>
<!-- END CONTENT -->
    
<h3> <br> </h3>

<!-- The footer -->


<!-- Checking if page is front page, and making the footer green if so -->
<?php 
  $footercolor = "#ffffff";
  if ($is_front) {
    $footercolor = "#E6F5EB";
  }
  ?>

<div id="footer" style=<?php echo "background-color:".$footercolor.";"; ?>>

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
          <a href="http://www.sagefoxgroup.com/" style="margin-left:13px;">
            <img alt= "SageFox Consulting Group" src="http://csteachingtips.org/images/sagefoxlogo_75tall.png">
          </a>
          <a href="http://www.hmc.edu">
            <img alt= "Harvey Mudd College" src="http://www.csteachingtips.org/images/HMClogo_75sq.png" style="margin-left:13px;">
          </a>

        </div>

          
        </div> 
</div>
