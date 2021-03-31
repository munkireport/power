<div class="col-lg-4 col-md-6">
    <div class="panel panel-default" id="battery-condition-widget">
            <div class="panel-heading" data-container="body" data-i18n="[title]power.widget.tooltip">
                <h3 class="panel-title"><i class="fa fa-flash"></i>
                    <span data-i18n="power.widget.title"></span>
                    <list-link data-url="/show/listing/power/batteries"></list-link>
                </h3>
			</div>
		<div class="panel-body text-center"></div>
    </div><!-- /panel -->
</div><!-- /col -->

<script>
$(document).on('appReady appUpdate', function(e, lang) {

	$.getJSON( appUrl + '/module/power/conditions', function( data ) {

		// Show no clients span
		$('#power-nodata').removeClass('hide');

		if(data.error){
    		//alert(data.error);
    		return;
    	}

		var panel = $('#battery-condition-widget div.panel-body'),
			baseUrl = appUrl + '/show/listing/power/batteries';
		panel.empty();

		// Set statuses
		if(data.poor && data.poor != "0"){
			panel.append(' <a href="'+baseUrl+'#poor" class="btn btn-danger"><span class="bigger-150">'+data.poor+'</span><br>'+i18n.t('power.widget.now')+'</a>');
		}
		if(data.service && data.service != "0"){
			panel.append(' <a href="'+baseUrl+'#service" class="btn btn-danger"><span class="bigger-150">'+data.service+'</span><br>'+i18n.t('power.widget.service')+'</a>');
		}
		if(data.fair && data.service != "0"){
			panel.append(' <a href="'+baseUrl+'#fair" class="btn btn-warning"><span class="bigger-150">'+data.fair+'</span><br>'+i18n.t('power.widget.soon')+'</a>');
		}
		if(data.good && data.service != "0"){
			panel.append(' <a href="'+baseUrl+'#good" class="btn btn-success"><span class="bigger-150">'+data.good+'</span><br>'+i18n.t('power.widget.normal')+'</a>');
		}
		if(data.missing && data.missing != "0"){
			panel.append(' <a href="'+baseUrl+'#no battery" class="btn btn-info"><span class="bigger-150">'+data.missing+'</span><br>'+i18n.t('power.widget.nobattery')+'</a>');
		}
    });
});
</script>
