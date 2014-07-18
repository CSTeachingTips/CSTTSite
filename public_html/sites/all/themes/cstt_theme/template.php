<?php

/**
 * @file
 * template.php
 */



/**
* Allows the use of user-login.tpl.php to theme the login page
*/
function cstt_theme_theme() {
  $items = array();
  // create custom user-login.tpl.php
  $items['user_login'] = array(
  'render element' => 'form',
  'path' => drupal_get_path('theme', 'cstt_theme') . '/templates',
  'template' => 'user-login',
  'preprocess functions' => array(
  'cstt_theme_preprocess_user_login'
  ),
 );
return $items;
}

//Encoding urls such that the taxonomy_links module works properly
function cstt_theme_drupal_encode_path($path) {
  return str_replace(array('%2F','%2C','%3D','%3F','%26'),array('/',',','=','?','&'), rawurlencode($path));
}



