<?php

/**
 * Argument handler to override taxonomy paths.
 */
class views_tpo_handler_taxonomy_term_name extends views_handler_argument_string {

  function term_field() {
    return 'name';
  }

  function path($term, $display) {
    $this->view->set_display($display);
    $display_path = $this->view->display[$display]->handler->get_option('path');

    // Handle path case.
    $term_path = $this->case_transform($term->{$this->term_field()}, 'path_case');

    // Handle URL spaces replacement.
    if (!empty($this->options['transform_dash'])) {
      $term_path = strtr($term_path, ' ', '-');
    }

    return $display_path .'/'. $term_path;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);

    $view = $this->view;
    $display = $view->display[$form_state['display_id']];
    $types = views_object_types();
    $option = $types[$form_state['type']]['plural'];

    if (isset($display->display_options['path']) && !$display->handler->is_defaulted($option)) {
      $form['views_term_path_override'] = array(
        '#type' => 'checkbox',
        '#title' => t('Override taxonomy term path'),
        '#description' => t('Override the taxonomy term paths with the current display path for the selected vocabularies. Use the Taxonomy term validator to select the vocabularies.'),
        '#default_value' => $this->options['views_term_path_override'],
      );
    }
  }

  function options_validate($form, &$form_state) {
    $view = $this->view;
    $settings = _views_tpo_view_settings($view)->settings;
    $options = $form_state['values']['options'];
    $this->vocabularies = taxonomy_get_vocabularies();

    if ($options['views_term_path_override']) {
      $current_display = $form_state['display_id'];
      $errors = array();

      // Only one path overriding taxonomy term argument is allowed.
      $types = views_object_types();
      $arguments = $view->display[$current_display]->handler->get_option($types[$form_state['type']]['plural']);
      $count = 0;
      foreach ($arguments as $field => $info) {
        if ($field == $this->options['id']) {
          $info = $options;
        }
        $count += isset($info['views_term_path_override']) ? $info['views_term_path_override'] : 0;
      }
      if ($count > 1) {
        $errors[] = t('Only one path overriding taxonomy term argument is allowed.');
      }

      // Check that the specified settings do not conflict with the current
      // ones.
      foreach ($this->get_vids($options) as $vid => $enabled) {
        if ($enabled && isset($settings[$vid]) && ($settings[$vid]->view != $view->name || $settings[$vid]->display != $current_display)) {
          $args = array(
            '%vocabulary' => $this->vocabularies[$vid]->name,
            '%view' => $settings[$vid]->view,
            '%display' => $settings[$vid]->display,
          );
          $errors[] = t('The vocabulary %vocabulary is already overridden by the view %view (%display).', $args);
        }
      }

      // Check that the correct argument type is selected if the taxonomy term
      // validator is used.
      if ($options['validate_type'] == 'taxonomy_term' && $options['validate_argument_type'] != 'name') {
        $errors[] = t('The taxonomy term validator needs the <em>Term name or synonym</em> argument type.');
      }

      if (!empty($errors)) {
        form_set_error('options][views_term_path_override', implode('<br/> ', $errors));
      }
    }
  }

  function options_submit($form, &$form_state) {
    $options = $form_state['values']['options'];
    $view = $this->view;
    $current_display = $form_state['display_id'];
    $settings =& $view->views_term_path_override->settings;
    $restore =& $view->views_term_path_override->restore;
    
    foreach ($this->get_vids($options) as $vid => $enabled) {
      if ($enabled && $options['views_term_path_override']) {
        $settings[$vid] = (object) array(
          'field' => $form_state['id'],
          'view' => $view->name,
          'display' => $current_display,
        );
      }
      elseif (isset($settings[$vid]) && $settings[$vid]->view == $view->name && $settings[$vid]->display == $current_display) {
        unset($settings[$vid]);
      }
      // Keep track of the original module.
      if ($this->vocabularies[$vid]->module != 'views_tpo') {
        $restore[$vid] = $this->vocabularies[$vid]->module;
      }
    }
  }

  function get_vids($options) {
    // Pick vocabularies to be overridden from the taxonomy term validator.
    if ($options['validate_type'] == 'taxonomy_term') {
      if (count(array_filter($options['validate_argument_vocabulary']))) {
        return $options['validate_argument_vocabulary'];
      }
    }

    // If no vocabulary has been explicitly specified through the taxonomy term
    // validator, select all of them.
    $vids = array_keys($options['validate_argument_vocabulary']);
    return array_combine($vids, array_fill(0, count($vids), TRUE));
  }
}
