<!-- Formats the Views Exposed filters on the Browse All page -->
<?php

/**
 * @file
 * This template handles the layout of a custom views exposed filter form.
 *
 * Specifically, it formats the sidebar on the browse all page for tips.
 *
 * Variables available:
 * - $widgets: An array of exposed form widgets. Each widget contains:
 * - $widget->label: The visible label to print. May be optional.
 * - $widget->operator: The operator for the widget. May be optional.
 * - $widget->widget: The widget itself.
 * - $sort_by: The select box to sort the view using an exposed form.
 * - $sort_order: The select box with the ASC, DESC options to define order. May be optional.
 * - $items_per_page: The select box with the available items per page. May be optional.
 * - $offset: A textfield to define the offset of the view. May be optional.
 * - $reset_button: A button to reset the exposed filter applied. May be optional.
 * - $button: The submit button for the form.
 *
 * @ingroup views_templates
 */
?>


<?php if (!empty($q)): ?>
  <?php
    // This ensures that, if clean URLs are off, the 'q' is added first so that
    // it shows up first in the URL.
    print $q;
  ?>
<?php endif; ?>

<!-- HTML to place exposed filters in a panel -->
<div class="views-exposed-form">
  <div class="panel panel-default">
      <div class="panel-body">
        <div class="views-exposed-widgets clearfix">
        
         <!-- Looping over the exposed filters that need to be printed -->
         <?php foreach ($widgets as $id => $widget): ?>
           <div id="<?php print $widget->id; ?>-wrapper" class="views-exposed-widget views-widget-<?php print $id; ?>">
           
            <!-- Prints the label of a Views exposed filter -->           
            <?php if (!empty($widget->label)): ?>
              <label for="<?php print $widget->id; ?>">
                <?php print $widget->label; ?>
              </label>
            <?php endif; ?>

            <?php if (!empty($widget->operator)): ?>
              <div class="views-operator">
                <?php print $widget->operator; ?>
              </div>
            <?php endif; ?>
            <!-- Prints the Views exposed filter -->       
            <div class="views-widget">
              <?php print $widget->widget; ?>
            </div>
        
            <?php if (!empty($widget->description)): ?>
              <div class="description">
                <?php print $widget->description; ?>
              </div>
            <?php endif; ?>

          </div>
        <?php endforeach; ?>
        <!-- Prints the submit button for the views exposed filters -->
        <div class="buttonplacementfix" id = "buttonplacementfix">
         <?php print $button; ?>
        </div>
        <!-- Prints the reset button for the views exposed filters -->
        <?php if (!empty($reset_button)): ?>
          <div class="buttonplacementfix" id = "buttonplacementfix">
           <?php print $reset_button; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

    <?php if (!empty($sort_by)): ?>

      <!-- HTML for the line of text below the filters bar -->

      <div class = "pull-right" id = "sortorder">
      <div class="views-exposed-widget views-widget-sort-by">
        <!-- Printing the Views exposed sort order -->
        <?php print $sort_by; ?>
      </div>
      </div>



    <?php endif; ?>
    <?php if (!empty($items_per_page)): ?>
      <div class="views-exposed-widget views-widget-per-page">
        <?php print $items_per_page; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($offset)): ?>
      <div class="views-exposed-widget views-widget-offset">
        <?php print $offset; ?>
      </div>
    <?php endif; ?>

  </div>
</div>
