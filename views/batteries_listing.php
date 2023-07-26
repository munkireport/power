<?php $this->view('partials/head'); ?>

<div class="container-fluid">
    <div class="row pt-4">
        <div class="col-lg-12">
            <h3><span data-i18n="power.battery_report"></span> <span id="total-count" class='badge badge-primary'>…</span></h3>
            <table class="table table-striped table-condensed table-bordered">
                <thead>
                    <tr>
                        <th data-i18n="listing.computername" data-colname='machine.computer_name'></th>
                        <th data-i18n="serial" data-colname='reportdata.serial_number'></th>
                        <th data-i18n="username" data-colname='reportdata.long_username'></th>
                        <th data-i18n="power.listing.designed" data-colname='power.design_capacity'></th>
                        <th data-i18n="power.listing.capacity" data-colname='power.max_capacity'></th>
                        <th data-i18n="power.listing.cycles" data-colname='power.cycle_count'></th>
                        <th data-i18n="power.listing.health" data-colname='power.max_percent'></th>
                        <th data-i18n="power.listing.condition" data-colname='power.condition'></th>
                        <th data-i18n="power.listing.current" data-colname='power.current_capacity'></th>
                        <th data-i18n="power.listing.charged" data-colname='power.current_percent'></th>
                        <?php
                        $temperature_unit=conf('temperature_unit');
                        if ( $temperature_unit == "F" ) {
                            echo "<th data-colname='power.temperature'>Temp°F</th>";
                        } else {
                            echo "<th data-colname='power.temperature'>Temp°C</th>";
                        }
                        ?>
                        <th data-i18n="power.listing.manufactured" data-colname='power.manufacture_date'></th>
                        <th data-i18n="power.wattage" data-colname='power.wattage'></th>
                        <th data-i18n="model" data-colname='machine.machine_model'></th>
                        <th data-i18n="listing.checkin" data-sort="desc" data-colname='reportdata.timestamp'></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-i18n="listing.loading" colspan="15" class="dataTables_empty"></td>
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
                    d.mrColNotEmptyBlank = "power.condition";

                    // Look for 'between' statement todo: make generic
                    if(d.search.value.match(/^\d+% max_percent \d+%$/))
                    {
                        // Add column specific search
                        d.columns[6].search.value = d.search.value.replace(/(\d+%) max_percent (\d+%)/, function(m, from, to){return ' BETWEEN ' + parseInt(from) + ' AND ' + parseInt(to)});
                        // Clear global search
                        d.search.value = '';
                    }

                    // Look for a bigger/smaller/equal statement
                    if(d.search.value.match(/^max_percent [<>=] \d+%$/))
                    {
                        // Add column specific search
                        d.columns[6].search.value = d.search.value.replace(/.*([<>=] )(\d+%)$/, function(m, o, content){return o + parseInt(content)});
                        // Clear global search
                        d.search.value = '';
                    }

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
                var link = mr.getClientDetailLink(name, sn, '#tab_battery-tab');
                $('td:eq(0)', nRow).html(link);

                // Format designed capacity
                var capacity=$('td:eq(3)', nRow).text();
                if (capacity != "" && (capacity)) {
                    $('td:eq(3)', nRow).text(capacity+' mAh').addClass('text-right');
                } else {
                    $('td:eq(3)', nRow).text('');
                }

                // Format maximum capacity
                var capacity=$('td:eq(4)', nRow).text();
                if (capacity != "" && (capacity)) {
                    $('td:eq(4)', nRow).text(capacity+' mAh').addClass('text-right');
                } else {
                    $('td:eq(4)', nRow).text('');
                }

                // Format cycles
                var cycles=$('td:eq(5)', nRow).text();
                if (cycles != "" && (cycles)) {
                    $('td:eq(5)', nRow).text(cycles+'').addClass('text-right');
                } else {
                    $('td:eq(5)', nRow).text('');
                }

                // Format battery health
                var max_percent=$('td:eq(6)', nRow).text();
                if (max_percent != "" && (max_percent)) {
                    var cls = max_percent > 89 ? 'success' : (max_percent > 79 ? 'warning' : 'danger');
                    $('td:eq(6)', nRow).html('<div class="progress"><div class="progress-bar bg-'+cls+'" style="width: '+max_percent+'%;">'+max_percent+'%</div></div>');
                } else {
                    $('td:eq(6)', nRow).text('');
                }

                // Format battery condition
                var status=$('td:eq(7)', nRow).text();
                status = status == 'Good' ? '<span class="badge badge-success">'+i18n.t('power.widget.normal')+'</span>' :
                status = status == 'Normal' ? '<span class="badge badge-success">'+i18n.t('power.widget.normal')+'</span>' :
                status = status == 'Service Battery' ? '<span class="badge badge-warning">'+i18n.t('power.widget.service')+'</span>' :
                status = status == 'ServiceBattery' ? '<span class="badge badge-warning">'+i18n.t('power.widget.service')+'</span>' :
                status = status == 'Check Battery' ? '<span class="badge badge-warning">'+i18n.t('power.widget.check')+'</span>' :
                status = status == 'CheckBattery' ? '<span class="badge badge-warning">'+i18n.t('power.widget.check')+'</span>' :
                status = status == 'Replace Soon' ? '<span class="badge badge-warning">'+i18n.t('power.widget.soon')+'</span>' :
                status = status == 'ReplaceSoon' ? '<span class="badge badge-warning">'+i18n.t('power.widget.soon')+'</span>' :
                status = status == 'Fair' ? '<span class="badge badge-warning">'+i18n.t('power.widget.soon')+'</span>' :
                status = status == 'Replace Now' ? '<span class="badge badge-danger">'+i18n.t('power.widget.now')+'</span>' :
                status = status == 'ReplaceNow' ? '<span class="badge badge-danger">'+i18n.t('power.widget.now')+'</span>' :
                status = status == 'Poor' ? '<span class="badge badge-danger">'+i18n.t('power.widget.now')+'</span>' :
                status = status == '' ? '<span class="badge badge-danger">'+i18n.t('power.widget.nobattery')+'</span>' :
                (status === 'No Battery' ? '<span class="badge badge-danger">'+i18n.t('power.widget.nobattery')+'</span>' : '')
                $('td:eq(7)', nRow).html(status)

                // Format current charge
                var charge=$('td:eq(8)', nRow).text();
                if (charge != "" && (charge)) {
                    $('td:eq(8)', nRow).text(charge+' mAh').addClass('text-right');
                } else {
                    $('td:eq(8)', nRow).text('');
                }

                // Format percentage
                var charge=$('td:eq(9)', nRow).text();
                if (charge != "" && (charge)) {
                    var cls = charge > 89 ? 'success' : (charge > 79 ? 'warning' : 'danger');
                    $('td:eq(9)', nRow).html('<div class="progress"><div class="progress-bar bg-'+cls+'" style="width: '+charge+'%;">'+charge+'%</div></div>');
                } else {
                    $('td:eq(9)', nRow).text('');
                }

                // Format temperature
                // Check config for temperature_unit °C or °F
                // °C * 9/5 + 32 = °F
                var temperature=$('td:eq(10)', nRow).text();
                if (temperature != "" ){
                    var temperature_unit = "<?=conf('temperature_unit')?>";
                    if ( temperature_unit == "F" ){
                        // Fahrenheit
                        if ( temperature == 0 || temperature == "" ){
                            temperature_c = "";
                            temperature_f = "";
                        } else {
                            temperature_c = (temperature / 100).toFixed(1)+"°C";
                            temperature_f = (((temperature * 9/5 ) + 3200 ) / 100).toFixed(1)+"°F";
                        }
                        $('td:eq(10)', nRow).html('<span title="'+temperature_c+'">'+temperature_f+'</span>').addClass('text-right');
                    } else {
                        // Celsius
                        if ( temperature == 0 || temperature == "" ){
                            temperature_c = "";
                            temperature_f = "";
                        } else {
                            temperature_c = (temperature / 100).toFixed(1)+"°C";
                            temperature_f = (((temperature * 9/5 ) + 3200 ) / 100).toFixed(1)+"°F";
                        }
                        $('td:eq(10)', nRow).html('<span title="'+temperature_f+'">'+temperature_c+'</span>').addClass('text-right');
                    }
                } else {
                    $('td:eq(10)', nRow).text("");
                }

                // Format Manufacture date
                var date=$('td:eq(11)', nRow).text();
                if(date === '1980-00-00' || date === '1980-01-01' || date == null || date == "Unknown"){
                    $('td:eq(11)', nRow).addClass('text-right danger').text(i18n.t('power.widget.unknown'));
                } else {
                    if(date){
                        a = moment(date)
                        b = a.diff(moment(), 'years', true)
                        if(a.diff(moment(), 'years', true) < -4)
                        {
                            $('td:eq(11)', nRow).addClass('danger')
                        }
                        $('td:eq(11)', nRow).addClass('text-right').html('<span title="'+date+'">'+moment(date).fromNow()+'</span>');
                    }
                }

                // Format wattage
                var wattage=$('td:eq(12)', nRow).text();
                if (wattage != "" && (wattage)) {
                    $('td:eq(12)', nRow).html(wattage+" "+i18n.t('power.watts'));
                } else {
                    $('td:eq(12)', nRow).text('');
                }

                // Format Check-In timestamp
                var checkin = parseInt($('td:eq(14)', nRow).text());
                var date = new Date(checkin * 1000);
                $('td:eq(14)', nRow).html('<span title="'+moment(date).format('llll')+'">'+moment(date).fromNow()+'</span>');
            }
        });
        
	    // Use hash as search query
	    if(window.location.hash.substring(1))
	    {
		    oTable.fnFilter( decodeURIComponent(window.location.hash.substring(1)) );
	    }

	} );
</script>

<?php $this->view('partials/foot')?>
