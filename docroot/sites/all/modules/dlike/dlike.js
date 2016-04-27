jQuery(document).bind('flagGlobalBeforeLinkUpdate', function(event, data) {
  updatestatus(data);
});

var updatestatus = function(data){
  var flag_name = data.flagName;
  var post_url = Drupal.settings.basePath+'dlike/' + data.entityType+ '/' + data.contentId + '/' + flag_name + '/append';
	jQuery.ajax({
		type:"POST",
		data:"method=ajax",
		url: post_url,
		success: function(html){
			jQuery('.dlike-'+data.entityType+'-append-'+data.contentId).html(html);
			Drupal.attachBehaviors();
		}
	});
}
