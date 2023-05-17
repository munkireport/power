<?php $this->view('partials/head'); ?>

<div class="container-fluid">
  <div class="row pt-4">
  	<div class="col-lg-12">
	<h3><span data-i18n="power.report"></span> <span id="total-count" class='badge badge-primary'>…</span></h3>
		  <table class="table table-striped table-condensed table-bordered">
		    <thead>
		      <tr>
		      	<th data-i18n="listing.computername" data-colname='machine.computer_name'></th>
		        <th data-i18n="serial" data-colname='reportdata.serial_number'></th>
		        <th data-i18n="username" data-colname='reportdata.long_username'></th>
		        <th data-i18n="power.active_profile" data-colname='power.active_profile'></th>
		        <th data-i18n="power.sleep" data-colname='power.sleep'></th>
		        <th data-i18n="power.disksleep" data-colname='power.disksleep'></th>
		        <th data-i18n="power.displaysleep" data-colname='power.displaysleep'></th>
		        <th data-i18n="power.powernap" data-colname='power.powernap'></th>
		        <th data-i18n="power.hibernatemode" data-colname='power.hibernatemode'></th>
		        <th data-i18n="power.standby" data-colname='power.standby'></th>
		        <th data-i18n="power.standbydelay" data-colname='power.standbydelay'></th>
		        <th data-i18n="power.womp_short" data-colname='power.womp'></th>
		        <th data-i18n="power.networkoversleep" data-colname='power.networkoversleep'></th>
		        <th data-i18n="power.ttyskeepawake" data-colname='power.ttyskeepawake'></th>
		        <th data-i18n="power.sms_short" data-colname='power.sms'></th>
		        <th data-i18n="power.gpuswitch" data-colname='power.gpuswitch'></th>
		        <th data-i18n="power.autopoweroff" data-colname='power.autopoweroff'></th>
		        <th data-i18n="power.autorestart" data-colname='power.autorestart'></th>
		        <th data-i18n="power.acwake" data-colname='power.acwake'></th>
		        <th data-i18n="power.lidwake" data-colname='power.lidwake'></th>
		        <th data-i18n="power.sleep_on_power_button" data-colname='power.sleep_on_power_button'></th>
		        <th data-i18n="power.destroyfvkeyonstandby_short" data-colname='power.destroyfvkeyonstandby'></th>
		        <th data-i18n="power.schedule" data-colname='power.schedule'></th>
		      </tr>
		    </thead>
		    <tbody>
		    	<tr>
		          <td data-i18n="listing.loading" colspan="23" class="dataTables_empty"></td>
		    	</tr>
		    </tbody>
		  </table>
    </div> <!-- /span 13 -->
  </div> <!-- /row -->
</div>  <!-- /container -->

<script type="text/javascript">

	$(document).on('appUpdate', function(e){

		var oTable = $('.table').DataTable();
		oTable.ajax.reload();
		return;

	});

	$(document).on('appReady', function(e, lang) {

        // Get modifiers from data attribute
        var mySort = [], // Initial sort
            hideThese = [], // Hidden columns
            col = 0, // Column counter
            runtypes = [], // Array for runtype column
            columnDefs = [{ visible: false, targets: hideThese }]; //Column Definitions

        $('.table th').map(function(){

            columnDefs.push({name: $(this).data('colname'), targets: col, render: $.fn.dataTable.render.text()});

            if($(this).data('sort')){
              mySort.push([col, $(this).data('sort')])
            }

            if($(this).data('hide')){
              hideThese.push(col);
            }

            col++
        });

	    oTable = $('.table').dataTable( {
            ajax: {
                url: appUrl + '/datatables/data',
                type: "POST",
                data: function(d){
                    d.mrColNotEmpty = "power.hibernatemode";

                }
            },
            dom: mr.dt.buttonDom,
            buttons: mr.dt.buttons,
            order: mySort,
            columnDefs: columnDefs,
		    createdRow: function( nRow, aData, iDataIndex ) {
	        	// Update name in first column to link
	        	var name=$('td:eq(0)', nRow).text();
	        	if(name == ''){name = "No Name"};
	        	var sn=$('td:eq(1)', nRow).text();
	        	var link = mr.getClientDetailLink(name, sn, '#tab_power-tab');
	        	$('td:eq(0)', nRow).html(link);

	        	// active power profile
	        	var columnvar=$('td:eq(3)', nRow).text();
                if(columnvar == "AC Power") {
                     $('td:eq(3)', nRow).text(i18n.t('power.ac_power'));
                } else if(columnvar == "Battery Power") {
                     $('td:eq(3)', nRow).text(i18n.t('power.battery_power'));
                } else{
                     $('td:eq(3)', nRow).text(columnvar);
                }

	        	// sleep
	        	var columnvar=$('td:eq(4)', nRow).text();
                if(columnvar == "") {
                     $('td:eq(4)', nRow).text('');
                } else if(columnvar == "1") {
                     $('td:eq(4)', nRow).text(columnvar+' '+i18n.t('power.minute'));
                } else if(columnvar == "0") {
                     $('td:eq(4)', nRow).text(i18n.t('power.never'));
                } else{
                     $('td:eq(4)', nRow).text(columnvar+' '+i18n.t('power.minutes'));
                }

	        	// disk sleep
	        	var columnvar=$('td:eq(5)', nRow).text();
                if(columnvar == "") {
                     $('td:eq(5)', nRow).text('');
                } else if(columnvar == "1") {
                     $('td:eq(5)', nRow).text(columnvar+' '+i18n.t('power.minute'));
                } else if(columnvar == "0") {
                     $('td:eq(5)', nRow).text(i18n.t('power.never'));
                } else{
                     $('td:eq(5)', nRow).text(columnvar+' '+i18n.t('power.minutes'));
                }

	        	// display sleep
	        	var columnvar=$('td:eq(6)', nRow).text();
                if(columnvar == "") {
                     $('td:eq(6)', nRow).text('');
                } else if(columnvar == "1") {
                     $('td:eq(6)', nRow).text(columnvar+' '+i18n.t('power.minute'));
                } else if(columnvar == "0") {
                     $('td:eq(6)', nRow).text(i18n.t('power.never'));
                } else{
                     $('td:eq(6)', nRow).text(columnvar+' '+i18n.t('power.minutes'));
                }

	        	// powernap
	        	var columnvar=$('td:eq(7)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(7)', nRow).text(columnvar)

                // hibernate mode
                var columnvar=$('td:eq(8)', nRow).text();
                if(columnvar == "25" || columnvar == "1") {
				 $('td:eq(8)', nRow).text(i18n.t('power.hibernate')+' ('+columnvar+')');
                } else if(columnvar == "3") {
                     $('td:eq(8)', nRow).text(i18n.t('power.safe_sleep')+' ('+columnvar+')');
                } else if(columnvar == "0") {
                     $('td:eq(8)', nRow).text(i18n.t('power.sleep')+' ('+columnvar+')');
                } else if(columnvar == "") {
                     $('td:eq(8)', nRow).text('');
                } else{
                     $('td:eq(8)', nRow).columnvar;
                }

                // standby
	        	var columnvar=$('td:eq(9)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(9)', nRow).text(columnvar)

	        	// standby delay
	        	var columnvar=$('td:eq(10)', nRow).text();
                if(columnvar == "") {
                     $('td:eq(10)', nRow).text('');
                } else if(columnvar == "1") {
                     $('td:eq(10)', nRow).text(columnvar+' '+i18n.t('power.second'));
                } else if(columnvar == "0") {
                     $('td:eq(10)', nRow).text(i18n.t('power.never'));
                } else{
                     $('td:eq(10)', nRow).text(columnvar+' '+i18n.t('power.seconds'));
                }

	        	// womp
	        	var columnvar=$('td:eq(11)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(11)', nRow).text(columnvar)

                // network over sleep
	        	var columnvar=$('td:eq(12)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(12)', nRow).text(columnvar)

                // tty keep awake
	        	var columnvar=$('td:eq(13)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(13)', nRow).text(columnvar)

                // sms
	        	var columnvar=$('td:eq(14)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(14)', nRow).text(columnvar)

                // gpu switch
	        	var columnvar=$('td:eq(15)', nRow).text();
                if(columnvar == "2") {
                     $('td:eq(15)', nRow).text(i18n.t('power.auto_gpu'));
                } else if(columnvar == "1") {
                     $('td:eq(15)', nRow).text(i18n.t('power.discreet'));
                } else if(columnvar == "0") {
                     $('td:eq(15)', nRow).text(i18n.t('power.integrated'));
                } else{
                     $('td:eq(15)', nRow).text("");
                }

                // auto power off
	        	var columnvar=$('td:eq(16)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(16)', nRow).text(columnvar)

                // auto restart
	        	var columnvar=$('td:eq(17)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(17)', nRow).text(columnvar)

                // ac wake
	        	var columnvar=$('td:eq(18)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(18)', nRow).text(columnvar)

                // lid wake
	        	var columnvar=$('td:eq(19)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(19)', nRow).text(columnvar)

                // sleep on power buton
	        	var columnvar=$('td:eq(20)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(20)', nRow).text(columnvar)

                // destroy filevault keys
	        	var columnvar=$('td:eq(21)', nRow).text();
	        	columnvar = columnvar == '1' ? i18n.t('yes') :
	        	(columnvar == '0' ? i18n.t('no') : '')
	        	$('td:eq(21)', nRow).text(columnvar)

		    }
	    } );
	    // Use hash as searchquery
	    if(window.location.hash.substring(1))
	    {
			oTable.fnFilter( decodeURIComponent(window.location.hash.substring(1)) );
	    }

	} );
</script>

<?php $this->view('partials/foot')?>
