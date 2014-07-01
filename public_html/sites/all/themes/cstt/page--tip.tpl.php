<?php

/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>




<html>

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
        <link rel="stylesheet" href="style.css">

</head>


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

  <div class="jumbotron" id="tip">
    <h1> <?php print $title; ?> </h1>
    <h6><br></h6>
    <p class="text-muted" id="category">
      Category:
      <?php print render($terms); ?>
       <?php print render($content['field_categories']); ?>

    </p>
  </div>

  <div class="container">
    <div class="col-xs-8 col-md-8">
      <ul class="extra-info">
        <?php print render($page['content']); ?>
      </ul>
    </div>

    <div class="col-xs-4 col-md-4">
      <p class="text-muted">
        Source: <a href="#">Source</a>
      </p>
      <p class="text-muted" id="tag">
        Tags: <a href="#">#tag</a> <a href="#">#tag</a> <a href="#">#tag</a>
      </p>  
    </div>
  </div>

  <br>

  <div class="container">
    <div class="col-xs-12">
         <h4>More Tips</h4>

        <div class="well">
            <div id="myCarousel" class="carousel slide">
                
                <!-- Carousel items -->
                <div class="carousel-inner">
                    <div class="item active">
                        <div class="row">
                            <div class="col-xs-4 col-md-4">
                              <a href="#x" class="thumbnail" id="extra-tip">
                                <center>
                                <div class="caption">
                                  <h3>Another Tip</h3>
                                </div>
                                </center>
                              </a>
                            </div>
                            <div class="col-xs-4 col-md-4">
                              <a href="#x" class="thumbnail" id="extra-tip">
                                <center>
                                <div class="caption">
                                  <h3>Another Tip</h3>
                                </div>
                                </center>
                              </a>
                            </div>
                            <div class="col-xs-4 col-md-4">
                              <a href="#x" class="thumbnail" id="extra-tip">
                                <center>
                                <div class="caption">
                                  <h3>Another Tip</h3>
                                </div>
                                </center>
                              </a>
                            </div>
                        </div>
                        <!--/row-->
                    </div>
                    <!--/item-->
                    <div class="item">
                        <div class="row">
                            <div class="col-xs-4 col-md-4">
                              <a href="#x" class="thumbnail" id="extra-tip">
                                <center>
                                <div class="caption">
                                  <h3>Another Tip</h3>
                                </div>
                                </center>
                              </a>
                            </div>
                            <div class="col-xs-4 col-md-4">
                              <a href="#x" class="thumbnail" id="extra-tip">
                                <center>
                                <div class="caption">
                                  <h3>Another Tip</h3>
                                </div>
                                </center>
                              </a>
                            </div>
                            <div class="col-xs-4 col-md-4">
                              <a href="#x" class="thumbnail" id="extra-tip">
                                <center>
                                <div class="caption">
                                  <h3>Another Tip</h3>
                                </div>
                                </center>
                              </a>
                            </div>
                        </div> 
                        <!--/row-->
                    </div>
                    <!--/item-->
                    <div class="item">
                        <div class="row">
                            <div class="col-xs-4 col-md-4">
                              <a href="#x" class="thumbnail" id="extra-tip">
                                <center>
                                <div class="caption">
                                  <h3>Another Tip</h3>
                                </div>
                                </center>
                              </a>
                            </div>
                            <div class="col-xs-4 col-md-4">
                              <a href="#x" class="thumbnail" id="extra-tip">
                                <center>
                                <div class="caption">
                                  <h3>Another Tip</h3>
                                </div>
                                </center>
                              </a>
                            </div>
                            <div class="col-xs-4 col-md-4">
                              <a href="#x" class="thumbnail" id="extra-tip">
                                <center>
                                <div class="caption">
                                  <h3>Another Tip</h3>
                                </div>
                                </center>
                              </a>
                            </div>
                        </div>
                        <!--/row-->
                    </div>
                    <!--/item-->
                </div>
                <!--/carousel-inner--> <a class="left carousel-control" href="#myCarousel" data-slide="prev">‹</a>

                <a class="right carousel-control" href="#myCarousel" data-slide="next">›</a>
            </div>
            <!--/myCarousel-->
        </div>
        <!--/well-->
    </div>
  </div>

  <br> <br> <br>

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
            <img src="images/nsf_logo.png">
          </a>
          <a href="http://www.sagefoxgroup.com/" style="margin-left:13px;">
            <img src="images/sagefoxlogo_75tall.png">
          </a>
          <a href="http://www.hmc.edu">
            <img src="images/HMClogo_75sq.png" style="margin-left:13px;">
          </a>
        </div>
    </div>      
</div>

<br>


  <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
    $('#myCarousel').carousel({
    interval: 10000
    })
      
      $('#myCarousel').on('slid.bs.carousel', function() {
      //alert("slid");
    });
    
    
    });

  </script>

</body>

</html>