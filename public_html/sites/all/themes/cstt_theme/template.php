<?php

/**
 * @file
 * template.php
 */


/**
* Customize the search form
*/
function cstt_theme_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'search_block_form') {
    $form['search_block_form']['#default_value'] = t('Search Tips'); // Set a default value for the textfield
    $form['search_block_form']['#attributes']['onblur'] = "if (this.value == '') {this.value = 'Search Tips';}";
    $form['search_block_form']['#attributes']['onfocus'] = "if (this.value == 'Search Tips') {this.value = 'Search Tips';}";
    
  }
}
?>