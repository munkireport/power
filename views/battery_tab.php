<h2 data-i18n="power.battery_tab"></h2>
<div id="battery-table"></div>
<div id="adapter-table"></div>

<div id="battery-msg" data-i18n="listing.loading" class="col-lg-12 text-center"></div>

<script>
$(document).on('appReady', function(){
	$.getJSON(appUrl + '/module/power/get_data/' + serialNumber, function(d){
        if( ! d ){
            // Change loading messages to no data
            $('#battery-msg').text(i18n.t('no_data'));
            $('#power-msg').text(i18n.t('no_data'));

            // Update the tab battery percent
            $('#battery-cnt').text("");

        } else {

            // Hide loading/no data message
            $('#battery-msg').text('');
            $('#power-msg').text('');

            var battery_rows = '';
            var adapter_rows = '';
            var ups_rows = '';
            var power_rows = '';
            var power_settings_rows = '';
            var thermal_load_rows = '';
            var assertions_rows = '';
            var battery_header_percent = '';
            var ups_header_percent = '';

            // Process each key in the JSON array
            for (var prop in d){
                if (d[prop] !== 0 && d[prop] == '' && prop !== 'condition' || d[prop] == null){
                    // Do nothing for nulls to blank them
                    battery_rows = battery_rows

                } else if (prop == 'condition' && d[prop] != ""){

                    // Format battery condition
                    d[prop] = d[prop] == 'Good' ? '<span class="label label-success">'+i18n.t('power.widget.normal')+'</span>' :
                    d[prop] = d[prop] == 'Normal' ? '<span class="label label-success">'+i18n.t('power.widget.normal')+'</span>' :
                    d[prop] = d[prop] == 'Service Battery' ? '<span class="label label-warning">'+i18n.t('power.widget.service')+'</span>' :
                    d[prop] = d[prop] == 'ServiceBattery' ? '<span class="label label-warning">'+i18n.t('power.widget.service')+'</span>' :
                    d[prop] = d[prop] == 'Check Battery' ? '<span class="label label-warning">'+i18n.t('power.widget.check')+'</span>' :
                    d[prop] = d[prop] == 'CheckBattery' ? '<span class="label label-warning">'+i18n.t('power.widget.check')+'</span>' :
                    d[prop] = d[prop] == 'Replace Soon' ? '<span class="label label-warning">'+i18n.t('power.widget.soon')+'</span>' :
                    d[prop] = d[prop] == 'ReplaceSoon' ? '<span class="label label-warning">'+i18n.t('power.widget.soon')+'</span>' :
                    d[prop] = d[prop] == 'Fair' ? '<span class="label label-warning">'+i18n.t('power.widget.soon')+'</span>' :
                    d[prop] = d[prop] == 'Replace Now' ? '<span class="label label-danger">'+i18n.t('power.widget.now')+'</span>' :
                    d[prop] = d[prop] == 'ReplaceNow' ? '<span class="label label-danger">'+i18n.t('power.widget.now')+'</span>' :
                    d[prop] = d[prop] == 'Poor' ? '<span class="label label-danger">'+i18n.t('power.widget.now')+'</span>' :
                    d[prop] = d[prop] == '' ? '<span class="label label-danger">'+i18n.t('power.widget.nobattery')+'</span>' :
                    (d[prop] === 'No Battery' ? '<span class="label label-danger">'+i18n.t('power.widget.nobattery')+'</span>' : '')
                    $('#battery-condition').html(d[prop])

                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';
                
                } else if (prop == 'condition' && d[prop] == ''){
                    // Update the tab battery percent
                    $('#battery-cnt').hide()

                // Format battery_rows danger yes
                } else if((prop == "permanentfailurestatus") && (d[prop] == 1 || d[prop] == "TRUE")){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><span class="label label-danger">'+i18n.t('yes')+'</span></td></tr>';

                // Format battery_rows yes/no
                } else if((prop == "ischarging" || prop == "fullycharged" || prop == "externalconnected") && (d[prop] == 0 || d[prop] == "FALSE")){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('no')+'</td></tr>';
                } else if((prop == "ischarging" || prop == "fullycharged" || prop == "externalconnected" || prop == "permanentfailurestatus") && (d[prop] == 1 || d[prop] == "TRUE")){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('yes')+'</td></tr>';

                // Format mAh
                } else if((prop == "packreserve" || prop == "design_capacity" || prop == "current_capacity" || prop == "max_capacity")){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+' mAh</td></tr>';

                // Format cell voltage
                } else if((prop == "cellvoltage") && (d[prop] != ".")){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';
                // Format voltage
                } else if(prop == "voltage" && d[prop] != "0.00" && d[prop] != "0"){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+" "+i18n.t('power.volts')+'</td></tr>';

                // Format lifetime current
                } else if(prop == "max_charge_current" || prop == "max_discharge_current"){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+(d[prop]/1000).toFixed(2)+' '+i18n.t('power.amps')+'</td></tr>';
                } else if(prop == "max_pack_voltage" || prop == "min_pack_voltage"){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+(d[prop]/1000).toFixed(2)+' '+i18n.t('power.volts')+'</td></tr>';

                // Format timeremaining, instanttimetoempty, avgtimetofull, avgtimetoempty
                } else if((prop == "timeremaining" || prop == "instanttimetoempty" || prop == "avgtimetofull" || prop == "avgtimetoempty") && d[prop] !== -1 && d[prop] !== '0' && d[prop] !== '65535'){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><span title="'+d[prop]+' '+i18n.t('power.minutes')+'">'+moment.duration(parseInt(d[prop]), "minutes").humanize()+'</span></td></tr>';

                // Format amperage and alculate charge/discharge watts
                } else if(prop == 'amperage' && d['voltage']){

                    var batt_watts = (d['amperage']*d['voltage']).toFixed(2);

                    if (d['amperage'] > 0){
                        battery_rows = battery_rows + '<tr><th>'+i18n.t('power.charging_watt')+'</th><td>'+batt_watts+" "+i18n.t('power.watts')+'</td></tr>';
                        battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+" "+i18n.t('power.amps')+'</td></tr>';

                    } else if (d['amperage'] < 0){
                        battery_rows = battery_rows + '<tr><th>'+i18n.t('power.discharging_watt')+'</th><td>'+batt_watts+" "+i18n.t('power.watts')+'</td></tr>';
                        battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+" "+i18n.t('power.amps')+'</td></tr>';
                    }

                // Format manufacture date
                } else if((prop == "manufacture_date") && (d[prop] === '1980-00-00' || d[prop] === '1980-01-01' || d[prop] == null || d[prop] == "Unknown")){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td class="danger">'+i18n.t('power.widget.unknown')+'</td></tr>';

                } else if((prop == "manufacture_date")){

                    a = moment(d[prop])
                    b = a.diff(moment(), 'years', true)
                    if(a.diff(moment(), 'years', true) < -4){
                        battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td class="danger">'+d[prop]+'</td></tr>';
                    } else {
                        battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';
                    }

                // Format max_percent
                } else if(prop == "max_percent"){
                    var cls = d[prop] > 89 ? 'success' : (d[prop] > 79 ? 'warning' : 'danger');
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><div class="progress"><div class="progress-bar progress-bar-'+cls+'" style="width: '+d[prop]+'%;">'+d[prop]+'%</div></div></td></tr>';

                // Format current_percent
                } else if(prop == "current_percent"){
                    cls = d[prop] > 89 ? 'success' : (d[prop] > 79 ? 'warning' : 'danger');
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><div class="progress"><div class="progress-bar progress-bar-'+cls+'" style="width: '+d[prop]+'%;">'+d[prop]+'%</div></div></td></tr>';
                    battery_header_percent = '<div class="progress" style="width:190px;display:inline-flex;"><div class="progress-bar progress-bar-'+cls+'" style="width: '+d[prop]+'%;">'+d[prop]+'%</div></div>';

                    // Update the tab battery percent
                    $('#battery-cnt').text(d['current_percent']+"%");

                // Format temperature F/C
                } else if((prop == "temperature" || prop == "max_temperature" || prop == "min_temperature") && d[prop] >= 10 && d['temp_format'] >= "F"){
                    if (prop == "max_temperature" || prop == "min_temperature"){
                        // This vaule is stored differently
                        d[prop] = (d[prop]*100)
                    }
                    outtemp_c = (d[prop] / 100)+"°C";
                    outtemp_f = (((d[prop] * 9/5) + 3200) / 100).toFixed(2)+"°F";           
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><span title="'+outtemp_c+'">'+outtemp_f+'</span></td></tr>';
                } else if((prop == "temperature" || prop == "max_temperature" || prop == "min_temperature") && d[prop] >= 10){
                    if (prop == "max_temperature" || prop == "min_temperature"){
                        // This vaule is stored differently
                        d[prop] = (d[prop]*100)
                    }
                    outtemp_c = (d[prop] / 100)+"°C";
                    outtemp_f = (((d[prop] * 9/5) + 3200) / 100).toFixed(2)+"°F";
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><span title="'+outtemp_f+'">'+outtemp_c+'</span></td></tr>';

                // Add battery_rows strings
                } else if((prop == "batteryserialnumber" || prop == "manufacturer" || prop == "cycle_count" || prop == "designcyclecount")){
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';                    


                // Format adapter volts
                } else if(prop == "adapter_voltage"){
                    adapter_rows = adapter_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+" "+i18n.t('power.volts')+'</td></tr>';
                // Format adapter amperage
                } else if(prop == "adapter_current"){
                    adapter_rows = adapter_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+" "+i18n.t('power.amps')+'</td></tr>';
                // Format adapter watts
                } else if(prop == "wattage"){
                    adapter_rows = adapter_rows + '<tr><th>'+i18n.t('power.adapter_wattage')+'</th><td>'+d[prop]+" "+i18n.t('power.watts')+'</td></tr>';          
                
                // Format adapter description
                } else if(prop == "adapter_description" && d[prop] == "pd charger"){
                    adapter_rows = adapter_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.pd_charger')+'</td></tr>';
                } else if(prop == "adapter_description" && d[prop] !== "pd charger"){
                    adapter_rows = adapter_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';

                // Add adapter_rows strings
                } else if((prop == "adapter_id" || prop == "family_code" || prop == "adapter_serial_number" || prop == "adapter_name" || prop == "adapter_manufacturer")){
                    adapter_rows = adapter_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';


                // Format UPS time
                } else if((prop == "haltafter" || prop == "haltremain") && d[prop] >= 0){
                    ups_rows = ups_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><span title="'+d[prop]+' '+i18n.t('power.minutes')+'">'+moment.duration(parseInt(d[prop]), "minutes").humanize()+'</span></td></tr>';

                // Format ups charging status true/false
                } else if((prop == "ups_charging_status") && (d[prop] === "true " || d[prop] === "true")){
                    ups_rows = ups_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.charging')+'</td></tr>';
                } else if((prop == "ups_charging_status") && (d[prop] === "false")){
                    ups_rows = ups_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.charging')+'</td></tr>';
                } else if(prop == "ups_charging_status"){
                    ups_rows = ups_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';

                // Format ups_percent
                } else if(prop == "ups_percent"){

                    cls = d[prop] > 89 ? 'success' : (d[prop] > 79 ? 'warning' : 'danger');
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><div class="progress"><div class="progress-bar progress-bar-'+cls+'" style="width: '+d[prop]+'%;">'+d[prop]+'%</div></div></td></tr>';
                    ups_header_percent = '<div class="progress" style="width:190px;display:inline-flex;"><div class="progress-bar progress-bar-'+cls+'" style="width: '+d[prop]+'%;">'+d[prop]+'%</div></div>';

                    // Update the tab battery percent
                    $('#battery-cnt').show()
                    $('#battery-cnt').text(d["ups_percent"]+"%");

                // Format ups haltlevel
                } else if(prop == "haltlevel"){

                    cls = d[prop] > 89 ? 'success' : (d[prop] > 79 ? 'warning' : 'danger');
                    battery_rows = battery_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><div class="progress"><div class="progress-bar progress-bar-'+cls+'" style="width: '+d[prop]+'%;">'+d[prop]+'%</div></div></td></tr>';
                    ups_header_percent = '<div class="progress" style="width:190px;display:inline-flex;"><div class="progress-bar progress-bar-'+cls+'" style="width: '+d[prop]+'%;">'+d[prop]+'%</div></div>';

                // Add ups_rows strings
                } else if(prop == "ups_name"){
                    ups_rows = ups_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';


                // Format active_profile
                } else if((prop == "active_profile") && d[prop] == "AC Power"){
                    power_rows = power_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.ac_power')+'</td></tr>';
                } else if((prop == "active_profile") && d[prop] == 1 || d[prop] == "Battery Power"){
                    power_rows = power_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.battery_power')+'</td></tr>';
                } else if (prop == "active_profile"){
                    power_rows = power_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';

                // Add power_rows strings
                } else if(prop == "schedule"){
                    power_rows = power_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop].replaceAll("\n", "<br>")+'</td></tr>';
                // Add power_rows strings
                } else if(prop == "user_wake_count" || prop == "dark_wake_count" || prop == "sleep_count"){
                    power_rows = power_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';


                // Format hibernatemode
                } else if((prop == "hibernatemode") && (d[prop] == "1" || d[prop] == "25")){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.hibernate')+' ('+d[prop]+')</td></tr>';
                } else if((prop == "hibernatemode") && (d[prop] == "3")){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.safe_sleep')+' ('+d[prop]+')</td></tr>';
                } else if((prop == "hibernatemode") && (d[prop] == "0")){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.sleep')+' ('+d[prop]+')</td></tr>';
                } else if((prop == "hibernatemode") && (d[prop] !== null)){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';

                // Format standbydelay
                } else if((prop == "standbydelay") && d[prop] > 0){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><span title="'+d[prop]+' '+i18n.t('power.seconds')+'">'+moment.duration(parseInt(d[prop]), "seconds").humanize()+'</span></td></tr>';
                // Format displaysleep
                } else if((prop == "displaysleep" || prop == "disksleep" || prop == "autopoweroffdelay" || prop == "sleep") && d[prop] > 0){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td><span title="'+d[prop]+' '+i18n.t('power.minutes')+'">'+moment.duration(parseInt(d[prop]), "minutes").humanize()+'</span></td></tr>';
                // Format never
                } else if((prop == "standbydelay" || prop == "displaysleep" || prop == "disksleep" || prop == "autopoweroffdelay" || prop == "sleep") && d[prop] == 0){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.never')+'</td></tr>';

                // Format gpuswitch
                } else if((prop == "gpuswitch") && (d[prop] == "2")){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.auto_gpu')+' ('+d[prop]+')</td></tr>';
                } else if((prop == "gpuswitch") && (d[prop] == "1")){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.discreet')+' ('+d[prop]+')</td></tr>';
                } else if((prop == "gpuswitch") && (d[prop] == "0")){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('power.integrated')+' ('+d[prop]+')</td></tr>';

                // Format power_settings_rows yes/no
                } else if((prop == "standby" || prop == "halfdim" || prop == "sms" || prop == "networkoversleep" || prop == "autopoweroff" || prop == "ttyskeepawake" || prop == "acwake" || prop == "lidwake" || prop == "sleep_on_power_button" || prop == "autorestart" || prop == "destroyfvkeyonstandby" || prop == "powernap") && (d[prop] == 0 || d[prop] == "FALSE")){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('no')+'</td></tr>';
                } else if((prop == "standby" || prop == "halfdim" || prop == "sms" || prop == "networkoversleep" || prop == "autopoweroff" || prop == "ttyskeepawake" || prop == "acwake" || prop == "lidwake" || prop == "sleep_on_power_button" || prop == "autorestart" || prop == "destroyfvkeyonstandby" || prop == "powernap") && (d[prop] == 1 || d[prop] == "TRUE")){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('yes')+'</td></tr>';
                    
                // Add power_settings_rows strings
                } else if(prop == "hibernatefile"){
                    power_settings_rows = power_settings_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';


                // Format cpu_scheduler_limit, cpu_speed_limit
                } else if(prop == "cpu_scheduler_limit" || prop == "cpu_speed_limit"){
                    thermal_load_rows = thermal_load_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'%</td></tr>';
                // Add thermal_load_rows strings
                } else if(prop == "combined_sys_load" || prop == "user_sys_load" || prop == "thermal_level" || prop == "battery_level" || prop == "cpu_available_cpus"){
                    thermal_load_rows = thermal_load_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';


                // Format assertions_rows yes/no
                } else if((prop == "externalmedia" || prop == "preventuseridlesystemsleep" || prop == "networkclientactive" || prop == "womp" || prop == "backgroundtask" || prop == "applepushservicetask" || prop == "preventuseridledisplaysleep" || prop == "preventsystemsleep" || prop == "userisactive") && (d[prop] == 0 || d[prop] == "FALSE")){
                    assertions_rows = assertions_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('no')+'</td></tr>';

                } else if((prop == "externalmedia" || prop == "preventuseridlesystemsleep" || prop == "networkclientactive" || prop == "womp" || prop == "backgroundtask" || prop == "applepushservicetask" || prop == "preventuseridledisplaysleep" || prop == "preventsystemsleep" || prop == "userisactive") && (d[prop] == 1 || d[prop] == "TRUE")){
                    assertions_rows = assertions_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+i18n.t('yes')+'</td></tr>';

                // Add assertions_rows strings
                } else if(prop == "sleep_prevented_by"){
                    assertions_rows = assertions_rows + '<tr><th>'+i18n.t('power.'+prop)+'</th><td>'+d[prop]+'</td></tr>';
                }
            }

            // Only show and sort battery table if data exists
            if (battery_rows !== "" && d.condition != "" && d.current_percent >= 90){
                $('#battery-table')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-battery-4'))
                        .append(' '+i18n.t('power.battery')+'&nbsp;&nbsp;'+battery_header_percent))
                    .append($('<div style="max-width:450px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(battery_rows))));
            }
            // Only show and sort battery table if data exists
            else if (battery_rows !== "" && d.condition != "" && d.current_percent >= 70){
                $('#battery-table')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-battery-3'))
                        .append(' '+i18n.t('power.battery')+'&nbsp;&nbsp;'+battery_header_percent))
                    .append($('<div style="max-width:450px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(battery_rows))));
            }
            // Only show and sort battery table if data exists
            else if (battery_rows !== "" && d.condition != "" && d.current_percent >= 50){
                $('#battery-table')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-battery-2'))
                        .append(' '+i18n.t('power.battery')+'&nbsp;&nbsp;'+battery_header_percent))
                    .append($('<div style="max-width:450px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(battery_rows))));
            }
            // Only show and sort battery table if data exists
            else if (battery_rows !== "" && d.condition != "" && d.current_percent >= 25){
                $('#battery-table')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-battery-1'))
                        .append(' '+i18n.t('power.battery')+'&nbsp;&nbsp;'+battery_header_percent))
                    .append($('<div style="max-width:450px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(battery_rows))));
            }
            // Only show and sort battery table if data exists
            else if (battery_rows !== "" && d.condition.includes("No Battery")){
                $('#battery-table')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-battery-0'))
                        .append(' '+i18n.t('power.battery')+'&nbsp;&nbsp;'+battery_header_percent))
                    .append($('<div style="max-width:450px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(battery_rows))));
            }
            // Show that we have no battery data
            else {
                $('#battery-msg').text(i18n.t('no_data'));
            }

            // Only show and sort adapter table if data exists
            if ( adapter_rows !== "" && d.wattage){
                $('#battery-table')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-plug'))
                        .append(' '+i18n.t('power.adapter')))
                    .append($('<div style="max-width:370px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(adapter_rows))));
            }

            // Only show and sort UPS table if data exists
            if ( ups_rows !== "" && d.ups_percent){
                // Hide no data message on battery tab
                $('#battery-msg').text('');

                $('#battery-tab')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-truck'))
                        .append(' '+i18n.t('power.ups_status')))
                    .append($('<div style="max-width:550px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(ups_rows))));
            }

            // Only show and sort power options table if data exists
            if ( power_rows !== ""){
                $('#power-tab')
                    .append($('<div style="max-width:775px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(power_rows))));;
            }

            // Only show and sort power settings table if data exists
            if ( power_settings_rows !== ""){
                $('#power-tab')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-bulb'))
                        .append(' '+i18n.t('power.power_settings')))
                    .append($('<div style="max-width:500px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(power_settings_rows))));
            }

            // Only show thermal load table if data exists
            if ( thermal_load_rows !== ""){
                $('#power-tab')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-thermometer-three-quarters'))
                        .append(' '+i18n.t('power.thermal_load')))
                    .append($('<div style="max-width:370px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(thermal_load_rows))))
            }

            // Only show assertions table if data exists
            if ( assertions_rows !== ""){
                $('#power-tab')
                    .append($('<h4>')
                        .append($('<i>')
                            .addClass('fa fa-hand-paper-o'))
                        .append(' '+i18n.t('power.assertions')))
                    .append($('<div style="max-width:550px;">')
                        .append($('<table>')
                            .addClass('table table-striped table-condensed')
                            .append($('<tbody>')
                                .append(assertions_rows))))
            }
        }
    });
});
</script>
