<!-- The Tip Profile Page -->
<?php

/**
 * @file
 * Tip profile page format.
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
    Computer Science Teaching Tips tip page.
  </title>
  <meta name="description" content="Computer Science Teaching tips tip page."> 
  <meta property="og:image" content="http://csteachingtips.org/images/tree.png">   
</head>


<h4> <br> </h4>

<body>

  <div id="tip">
    <div class="container">
    <ul>
      <h2 class ="tipspace"> 
      <?php print $title; ?> </h2>
      </ul>
      <div class = "col-md-7">
      </div>
      <div class="socialmediabuttons col-md-5 col-xs-12">
        <div id = "socialdiv" >


          <div id = "socialtop" >  
          <!-- Printing the Twitter button -->         
          <?php $urlTwitter = "https://twitter.com/share?url=" . $node_url . "&text=" . $title . " http://csteachingtips.org" . $node_url; ?>
          <a href="<?php echo $urlTwitter; ?>" target="_blank"><img class = "twittershare" src="http://csteachingtips.org/images/tshare.png" alt="Post to Twitter"/></a>
      
          <!-- Printing the Facebook button -->
          <?php $urlFacebook = "https://www.facebook.com/sharer/sharer.php?u=www.csteachingtips.org" . $node_url; ?>
          <a href="<?php echo $urlFacebook; ?>" target="_blank"><img class = "facebookshare" src="http://csteachingtips.org/images/fshare.png" alt="Share on Facebook"/></a>

          </div>
          <div id = "socialbottom" >
          <!-- Printing the Google+ button -->
          <?php $urlGoogle = "https://plus.google.com/share?url=" . "http://csteachingtips.org" . $node_url; ?>
          <a href="<?php echo $urlGoogle; ?>" target="_blank"><img class = "googleplusshare" src="http://csteachingtips.org/images/gshare.png" alt="Share on Google+" /></a>  
         
          <!-- Printing the Copy URL button -->
          <input type="image" class = "copyURL" src="http://csteachingtips.org/images/linkshare.png" value="Copy URL" onclick="linkButton();" style="vertical-align:middle;"/>
          </div>

          <!-- Script for making Copy URL box display -->
          <script>

            function linkButton() {
              if (document.getElementById('urlDiv').style.display == "block") {
                hideDiv();
              }
              else {
                showDiv();
              }
            }

            function showDiv() {
              document.getElementById('urlDiv').style.display = "block";
              document.getElementById('url').focus();
              document.getElementById('url').select();
            }

            function hideDiv() {
              document.getElementById('urlDiv').style.display = "none";
            }

          </script>
    
           
        </div> 

        <!-- Printing the Copy URL popup box -->
        <div id="urlDiv" class="col-xs-12">
          <div  id="urlBox" style = "text-align:center;"> 

            <p >Press CTRL-C to copy the link to this tip. </p>
            <input id="url" type="text" value=<?php echo "http://csteachingtips.org".$node_url; ?> readonly>
            <br><br>
            <input type = "button" value="Close" id="close" onclick="hideDiv()">
            <br>

          </div>
        </div>
           
      </div>
    </div> 

                
  </div>

    <div class = "colorstripe">
    </div>

    <div class = "container">

      <div class="col-xs-12 col-md-8">
        <!-- Printing the body/additional info field for content type tip -->
        <?php print render($content['body']); ?>
      </div>
       
      
      <div class = "col-xs-12 col-md-4 tipside tipsideDesktop"> 
	      <!-- Printing the tags field for content type tip if the tip has tags, otherwise printing the string Tags -->
        <?php if (isset($content['field_tags'])) {
          print render($content['field_tags']);
        }
        else {
          print '<div class = "tagslabel"> Tags: </div>';
        }
        ?>
        <br>
        <div class = "tagslabel"> Similar Tips: </div>     
          <!-- Printing a Views block configured to show similar tips -->
          <?php $block = block_load('views','similar_tips-block');
          $dummysearch = _block_get_renderable_array(_block_render_blocks(array($block)));
          print drupal_render($dummysearch);
          ?>       
       </div>
       
      <br> 
      <div class = "container">
      <div class = "sources col-md-12">
          <div class="col-xs-6 col-md-4">
          <!-- Printing the external source field for content type tip -->
          <?php print render($content['field_source']); ?>
          </div>

          <div class="col-xs-6 col-md-4">
          <!-- Printing taxonomy terms from the vocabulary source field for content type tip -->
          <?php print render($content['field_source_taxonomy']); ?>
          </div>    
      </div>
      
    </div>
    <br>

      <div class = "col-xs-12 col-md-4 tipside tipsideMobile"> 
        <!-- Printing the tags field for content type tip if the tip has tags, otherwise printing the string Tags -->
        <?php if (isset($content['field_tags'])) {
          print render($content['field_tags']);
        }
        else {
          print '<div class = "tagslabel"> Tags: </div>';
        }
        ?>
        <br>
        <div class = "tagslabel"> Similar Tips: </div>     
          <!-- Printing a Views block configured to show similar tips -->
          <?php $block = block_load('views','similar_tips-block');
          $dummysearch = _block_get_renderable_array(_block_render_blocks(array($block)));
          print drupal_render($dummysearch);
          ?>       
      </div>
      
    </div>

<h4><br></h4>
<div class="container">
  <?php print render($content['disqus']); ?>
</div>

<div class = "container">
  <div class = "col-md-1">
  </div>
  <div  class = "col-md-10">
    
  </div>
  <div class = "col-md-1">
  </div>
</div>

<br> <br> <br> <br>

</body>

</html>
