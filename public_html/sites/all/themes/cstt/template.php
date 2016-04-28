<?php
/**
 * @file
 * The primary PHP file for this theme.
 */

function cstt_preprocess_page(&$variables) {
	if (drupal_is_front_page()) {
		$variables['title'] = FALSE;
	}
}
