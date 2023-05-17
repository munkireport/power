<div class="col-lg-4 col-md-6">
	<div class="card" id="power-battery-health-widget">
		<div class="card-header" data-container="body" data-i18n="[title]power.widget.health.tooltip">
			<i class="fa fa-medkit"></i>
			<span data-i18n="power.widget.health.title"></span> 
			<a href="/show/listing/power/batteries" class="pull-right"><i class="fa fa-list"></i></a>
			% <!-- The percent sign is supposed to be here -->
		</div>
		<div class="card-body text-center"></div>
	</div><!-- /card -->
</div><!-- /col -->

<script>
$(document).on('appUpdate', function(e, lang) {

	var body = $('#power-battery-health-widget div.card-body');

	$.getJSON( appUrl + '/module/power/get_stats', function( data ) {

		// Clear previous content
		body.empty();

		// Todo: add to config
		var entries = [
			{name: '< 80%', link: 'max_percent < 80%', count: 0, class:'btn-danger', id: 'danger'},
			{name: '80% +', link: '80% max_percent 90%', count: 0, class:'btn-warning', id: 'warning'},
			{name: '90% +', link: 'max_percent > 90%', count: 0, class:'btn-success', id: 'success'}
		]

		// Calculate entries
		if(data.length){

			// Add count to entries
			$.each(entries, function(i, o){
				o.count = data[0][o.id];
			})

			// render entries
			$.each(entries, function(i, o){
				body.append('<a href="'+appUrl+'/show/listing/power/batteries/#'+encodeURIComponent(o.link)+'" class="btn '+o.class+'"><span class="bigger-150">'+o.count+'</span><br>'+o.name+'</a> ');
			});
		}
		else{
			body.append(i18n.t('no_clients'));
		}
	});
});
</script>
