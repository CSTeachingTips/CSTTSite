<?php
/*
 * All you need to do is include an alias found
 * in local.aliases.drushrc.php as the 'parent'
 * for this environment and it will inherit those
 * settings.
 */
$aliases['local'] = array(
  'parent' => '@local.my_site',
);

$aliases['develop'] = array(
  'parent' => '@local.defaults',
  'uri' => 'http://dev.link.com',
  'root' => '/path/to/root',
  'remote-host' => '<ip>',
  'remote-user' => 'root',
  'path-aliases' => array(
    '%files' => 'sites/default/files',
    '%dump-dir' => '/root/tmp'
  ),
);