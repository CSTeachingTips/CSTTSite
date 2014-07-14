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