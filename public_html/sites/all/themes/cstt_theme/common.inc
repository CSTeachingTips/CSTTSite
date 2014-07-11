<?php
function drupal_encode_path($path) {
  return str_replace(array('%2F','%2C','%3D','%3F','%26'),array('/',',','=','?','&'), rawurlencode($path));
}