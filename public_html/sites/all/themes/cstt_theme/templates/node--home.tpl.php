<html>


<!-- ======================= VERSION 2 =================
 == This version has all of the content over 2 screens==
 ======================================================= 
-->

<head>
        <title>

        </title>
        <meta name="description" content="CS Teaching Tips is an NSF funded project for providing tips to Computer Science teachers.">
        <meta property="og:type" content="website" />
        <meta property="og:url" content="m" />
        <meta property="og:image" content="m" />
        <meta property="og:title" content="!"/>
        <meta property="og:site_name" content="CS Teaching Tips!" />
        <meta property="og:description" content="hi!" />
        <meta property="fb:app_id" content="560604524019670"/>
        <meta name="viewport" content="width=device-width, initial-scale = 1.0">
        <meta charset="utf-8">
        <link rel = "stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        
</head>
  
<body>


  <!-- Navbar -->
  <!-- linnea and sammy made it show up on all pages -->
  <!-- End Navbar -->


  <!-- Center button and mission statement -->

  <br>
  <div class="jumbotron">

    <!-- Whitespace -->
    <p style="font-size:50px"> <br> </p>

    <h1> 
      <img src="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xfa1/t1.0-1/p50x50/1900081_632479410157781_1725500249_n.png" id="cstt-icon"> 
        CS Teaching Tips 
    </h1>

    <p>
      <em> From personal stories to published papers, we've done the reading for you. <br>
      Connect and teach like never before. </em>
    </p>

    <!-- Whitespace -->
    <p style="font-size:15px"> <br> </p>

    <p>
      <a class="btn btn-primary btn-lg" role="button">Start Browsing Tips <span class="glyphicon glyphicon-chevron-right"> </span>
      </a>
    </p>
    
    <!-- Whitespace -->
    <p style="font-size:25px"> <br> </p>

  </div>

  <!-- End center button and mission statement -->


  <!-- Whitespace -->
  <p style="font-size:40px"> <br> </p>


  <!-- Down Arrow (for jumping to category panels) -->

  <center>
    <a href="#category-panels">
      <span class="glyphicon glyphicon-chevron-down text-muted" style="font-size: 2em">
    </a>
    </span>
  </center>

  <!-- End Down Arrow -->


  <!-- Whitespace -->
  <p style="font-size:110px"> <br> </p>


  <!-- Category Panels --> 

  <!-- id useful for bookmark & anchoring -->
  <div id="category-panels">

    <!-- Organizing Curriculum--> 

    <div class="col-xs-4 col-md-4">
      <a href="#" class="thumbnail">
        <center>
          <br>
          <img src="http://csteachingtips.org/images/placeholder.png">
          <div class="caption">
            <h3> Organizing <br> Curriculum </h3>
          </div>
        </center> 
      </a>
    </div>


    <!-- Delivering Content --> 

    <div class="col-xs-4 col-md-4">
      <a href="#" class="thumbnail">
        <center>
          <br>
          <img src="http://csteachingtips.org/images/placeholder.png">
            <div class="caption"> 
              <h3> Delivering <br> Content </h3> 
            </div>
        </center> 
      </a> 
    </div>


    <!-- Managing and Assessing--> 
    
    <div class="col-xs-4 col-md-4">
      <a href="#" class="thumbnail">
        <center>
          <br>
          <img src="http://csteachingtips.org/images/placeholder.png">  
            <div class="caption">
              <h3> <span class="nobr"> Managing and </span> <br> Assessing </h3>
            </div>
        </center> 
      </a>
    </div>

  </div>

  <!-- End Category Panels -->


  <!-- Whitespace -->
  <p style="font-size:201px"> <br> <br> </p>


  <!-- Footer -->
  <!-- linnea and sammy made the footer show up on all pages -->
  <!-- End Footer -->
    

  <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>

  <!-- !!!! We should put this in a separate file and link to it  like the others !!!! -->
  <script>
    $(function() {

        function filterPath(string) {
            return string
            .replace(/^\//,'')
            .replace(/(index|default).[a-zA-Z]{3,4}$/,'')
            .replace(/\/$/,'');
        }

        var locationPath = filterPath(location.pathname);
        var scrollElem = scrollableElement('html', 'body');

        // Any links with hash tags in them (can't do ^= because of fully qualified URL potential)
        $('a[href*=#]').each(function() {

            // Ensure it's a same-page link
            var thisPath = filterPath(this.pathname) || locationPath;
            if (  locationPath == thisPath
                && (location.hostname == this.hostname || !this.hostname)
                && this.hash.replace(/#/,'') ) {

                    // Ensure target exists
                    var $target = $(this.hash), target = this.hash;
                    if (target) {

                        // Find location of target
                        var targetOffset = $target.offset().top;
                        $(this).click(function(event) {

                            // Prevent jump-down
                            event.preventDefault();

                            // Animate to target
                            $(scrollElem).animate({scrollTop: targetOffset}, 400, function() {

                                // Set hash in URL after animation successful
                                location.hash = target;

                            });
                        });
                    }
            }

        });

        // Use the first element that is "scrollable"  (cross-browser fix?)
        function scrollableElement(els) {
            for (var i = 0, argLength = arguments.length; i <argLength; i++) {
                var el = arguments[i],
                $scrollElement = $(el);
                if ($scrollElement.scrollTop()> 0) {
                    return el;
                } else {
                    $scrollElement.scrollTop(1);
                    var isScrollable = $scrollElement.scrollTop()> 0;
                    $scrollElement.scrollTop(0);
                    if (isScrollable) {
                        return el;
                    }
                }
            }
            return [];
        }

    });
  </script>

</body>

</html>

