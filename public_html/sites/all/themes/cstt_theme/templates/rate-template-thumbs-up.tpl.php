
<div class = "ratestar">

<?php

/**
 * @file
 * Custom rate widget theme
 */
 
// print '<div class="rate-label">' . $display_options['title'] . '</div>';



// If the user has voted, print relevant info and print the solid green star
if (isset($results['user_vote'])) {

  print '<div class="rate-description">' . $display_options['description'] . '</div>';
 
  print '<img class = "rate-clicked" src = "http://csteachingtips.org/images/starhover-1.png">';
  if ($info) {
  	print '<div class="rate-info">' . $info . '</div>'; 
  }

}


// If the user hasn't voted, print the star with a white filling.
else {

if ($display_options['description']) {
  print '<div class="rate-description">' . $display_options['description'] . '</div>';
}
print $up_button;
if ($info) {
  print '<div class="rate-info">' . $info . '</div>';
}
}
?>

</div>