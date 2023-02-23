<?php

use CFPropertyList\CFPropertyList;

class Power_model extends \Model
{
    public function __construct($serial = '')
    {
        parent::__construct('id', 'power'); //primary key, tablename
        $this->rs['id'] = '';
        $this->rs['serial_number'] = $serial;
        $this->rs['manufacture_date'] = '';
        $this->rs['design_capacity'] = 0;
        $this->rs['max_capacity'] = 0;
        $this->rs['max_percent'] = 0;
        $this->rs['current_capacity'] = 0;
        $this->rs['current_percent'] = 0;
        $this->rs['cycle_count'] = 0;
        $this->rs['temperature'] = 0;
        $this->rs['condition'] = '';
        $this->rs['sleep_prevented_by'] = '';
        $this->rs['hibernatefile'] = '';
        $this->rs['schedule'] = '';
        $this->rs['adapter_id'] = '';
        $this->rs['family_code'] = '';
        $this->rs['adapter_serial_number'] = '';
        $this->rs['combined_sys_load'] = '';
        $this->rs['user_sys_load'] = '';
        $this->rs['thermal_level'] = '';
        $this->rs['battery_level'] = '';
        $this->rs['ups_name'] = '';
        $this->rs['active_profile'] = '';
        $this->rs['ups_charging_status'] = '';
        $this->rs['externalconnected'] = '';
        $this->rs['cellvoltage'] = '';
        $this->rs['manufacturer'] = '';
        $this->rs['batteryserialnumber'] = '';
        $this->rs['fullycharged'] = '';
        $this->rs['ischarging'] = '';
        $this->rs['standbydelay'] = 0;
        $this->rs['standby'] = 0;
        $this->rs['womp'] = 0;
        $this->rs['halfdim'] = 0;
        $this->rs['gpuswitch'] = 0;
        $this->rs['sms'] = 0;
        $this->rs['networkoversleep'] = 0;
        $this->rs['disksleep'] = 0;
        $this->rs['sleep'] = 0;
        $this->rs['autopoweroffdelay'] = 0;
        $this->rs['hibernatemode'] = 0;
        $this->rs['autopoweroff'] = 0;
        $this->rs['ttyskeepawake'] = 0;
        $this->rs['displaysleep'] = 0;
        $this->rs['acwake'] = 0;
        $this->rs['lidwake'] = 0;
        $this->rs['sleep_on_power_button'] = 0;
        $this->rs['autorestart'] = 0;
        $this->rs['destroyfvkeyonstandby'] = 0;
        $this->rs['powernap'] = 0;
        $this->rs['haltlevel'] = 0;
        $this->rs['haltafter'] = 0;
        $this->rs['haltremain'] = 0;
        $this->rs['lessbright'] = 0;
        $this->rs['sleep_count'] = 0;
        $this->rs['dark_wake_count'] = 0;
        $this->rs['user_wake_count'] = 0;
        $this->rs['wattage'] = 0;
        $this->rs['backgroundtask'] = 0;
        $this->rs['applepushservicetask'] = 0;
        $this->rs['userisactive'] = 0;
        $this->rs['preventuseridledisplaysleep'] = 0;
        $this->rs['preventsystemsleep'] = 0;
        $this->rs['externalmedia'] = 0;
        $this->rs['preventuseridlesystemsleep'] = 0;
        $this->rs['networkclientactive'] = 0;
        $this->rs['cpu_scheduler_limit'] = 0;
        $this->rs['cpu_available_cpus'] = 0;
        $this->rs['cpu_speed_limit'] = 0;
        $this->rs['ups_percent'] = 0;
        $this->rs['timeremaining'] = 0;
        $this->rs['instanttimetoempty'] = 0;
        $this->rs['voltage'] = 0.0;
        $this->rs['permanentfailurestatus'] = 0;
        $this->rs['packreserve'] = 0;
        $this->rs['avgtimetofull'] = 0;
        $this->rs['amperage'] = 0.0;
        $this->rs['designcyclecount'] = 0;
        $this->rs['avgtimetoempty'] = 0;
        $this->rs['adapter_current'] = 0;
        $this->rs['adapter_voltage'] = 0;
        $this->rs['charging_current'] = 0;
        $this->rs['charging_voltage'] = 0;
        $this->rs['adapter_manufacturer'] = '';
        $this->rs['adapter_name'] = '';
        $this->rs['adapter_description'] = '';
        $this->rs['max_charge_current'] = '';
        $this->rs['max_discharge_current'] = '';
        $this->rs['max_pack_voltage'] = '';
        $this->rs['min_pack_voltage'] = '';
        $this->rs['max_temperature'] = '';
        $this->rs['min_temperature'] = '';


        if ($serial) {
            $this->retrieve_record($serial);
        }

        $this->serial = $serial;
    }

    // ------------------------------------------------------------------------
    /**
     * Process data sent by postflight
     *
     * @param string data
     *
     **/
    public function process($data)
    {
        // If data is empty, throw error
        if (! $data) {
            throw new Exception("Error Processing Power Module Request: No data found", 1);
        }

        // Switch between old style and xml style reports
        if (substr( $data, 0, 30 ) == '<?xml version="1.0" encoding="' ) {
            $this->_process_xml($data);
        } else {
            $this->_process_legacy($data);
        }

        // Massage data into the right format for the database
        $this->_adjust_battery_data();
        $this->save();
    }

    /**
     * Process legacy
     * 
     *
     * @param Array $data Data array
     **/
    private function _process_legacy($data)
    {
        // Translate network strings to db fields
        $translate = [
            'manufacture_date = ' => 'manufacture_date',
            'design_capacity = ' => 'design_capacity',
            'max_capacity = ' => 'max_capacity',
            'max_percent = ' => 'max_percent',
            'current_capacity = ' => 'current_capacity',
            'cycle_count = ' => 'cycle_count',
            'temperature = ' => 'temperature',
            'condition = ' => 'condition',
        ];

        // Reset values
        $this->manufacture_date = '';
        $this->design_capacity = 1;
        $this->max_capacity = 1;
        $this->max_percent = 100;
        $this->current_capacity = 0;
        $this->current_percent = 0;
        $this->cycle_count = 0;
        $this->temperature = 0;
        $this->condition = '';
        $this->timestamp = 0;

        // Parse data
        foreach(explode("\n", $data) as $line) {
            // Translate standard entries
            foreach($translate as $search => $field) {

                if(strpos($line, $search) === 0) {

                    $value = substr($line, strlen($search));

                    $this->$field = $value;
                    break;
                }
            }
        } // End foreach explode lines
    }
    /**
     * Process xml
     *
     *
     * @param Array $data Client data
     **/
    private function _process_xml($data)
    {
        // Process incoming powerinfo.xml
        $parser = new CFPropertyList();
        $parser->parse($data, CFPropertyList::FORMAT_XML);
        $plist = $parser->toArray();

        // Translate battery strings to db fields
        $translate = array(
            'ManufactureDate' => 'manufacture_date',
            'DesignCapacity' => 'design_capacity',
            'CurrentCapacity' => 'current_capacity',
            'CycleCount' => 'cycle_count',
            'Temperature' => 'temperature',
            'MaxCapacity' => 'max_capacity',
            'condition' => 'condition',
            'standbydelay' => 'standbydelay',
            'standby' => 'standby',
            'womp' => 'womp',
            'halfdim' => 'halfdim',
            'hibernatefile' => 'hibernatefile',
            'gpuswitch' => 'gpuswitch',
            'sms' => 'sms',
            'networkoversleep' => 'networkoversleep',
            'disksleep' => 'disksleep',
            'sleep' => 'sleep',
            'autopoweroffdelay' => 'autopoweroffdelay',
            'hibernatemode' => 'hibernatemode',
            'autopoweroff' => 'autopoweroff',
            'ttyskeepawake' => 'ttyskeepawake',
            'displaysleep' => 'displaysleep',
            'acwake' => 'acwake',
            'lidwake' => 'lidwake',
            'SleepOn' => 'sleep_on_power_button',
            'powernap' => 'powernap',
            'autorestart' => 'autorestart',
            'DestroyFVKeyOnStandby' => 'destroyfvkeyonstandby',
            'schedule' => 'schedule',
            'haltlevel' => 'haltlevel',
            'haltafter' => 'haltafter',
            'haltremain' => 'haltremain',
            'lessbright' => 'lessbright',
            'SleepCount' => 'sleep_count',
            'DarkWake' => 'dark_wake_count',
            'UserWake' => 'user_wake_count',
            'attage' => 'wattage', // This is spelled correctly
            'AdapterID' => 'adapter_id',
            'FamilyCode' => 'family_code',
            'SerialNumber' => 'adapter_serial_number',
            'adapter_current' => 'adapter_current',
            'adapter_voltage' => 'adapter_voltage',
            'adapter_manufacturer' => 'adapter_manufacturer',
            'adapter_name' => 'adapter_name',
            'charging_current' => 'charging_current',
            'charging_voltage' => 'charging_voltage',
            'CPUSchedulerLimit' => 'cpu_scheduler_limit',
            'CPUAvailableCPUs' => 'cpu_available_cpus',
            'CPUSpeedLimit' => 'cpu_speed_limit',
            'BackgroundTask' => 'backgroundtask',
            'ApplePushServiceTask' => 'applepushservicetask',
            'UserIsActive' => 'userisactive',
            'PreventUserIdleDisplaySleep' => 'preventuseridledisplaysleep',
            'PreventSystemSleep' => 'preventsystemsleep',
            'ExternalMedia' => 'externalmedia',
            'PreventUserIdleSystemSleep' => 'preventuseridlesystemsleep',
            'NetworkClientActive' => 'networkclientactive',
            'combinedlevel' => 'combined_sys_load',
            'user' => 'user_sys_load',
            'battery' => 'battery_level',
            'thermal' => 'thermal_level',
            'UPSName' => 'ups_name',
            'Nowdrawing' => 'active_profile',
            'UPSPercent' => 'ups_percent',
            'UPSStatus' => 'ups_charging_status',
            'ExternalConnected' => 'externalconnected',
            'TimeRemaining' => 'timeremaining',
            'InstantTimeToEmpty' => 'instanttimetoempty',
            'CellVoltage' => 'cellvoltage',
            'Voltage' => 'voltage',
            'PermanentFailureStatus' => 'permanentfailurestatus',
            'Manufacturer' => 'manufacturer',
            'PackReserve' => 'packreserve',
            'AvgTimeToFull' => 'avgtimetofull',
            'BatterySerialNumber' => 'batteryserialnumber',
            'AmperagemA' => 'amperage',
            'FullyCharged' => 'fullycharged',
            'IsCharging' => 'ischarging',
            'DesignCycleCount9C' => 'designcyclecount',
            'AvgTimeToEmpty' => 'avgtimetoempty',
            'sleep_prevented_by' => 'sleep_prevented_by',
            'adapter_description' => 'adapter_description',
            'max_charge_current' => 'max_charge_current',
            'max_discharge_current' => 'max_discharge_current',
            'max_pack_voltage' => 'max_pack_voltage',
            'min_pack_voltage' => 'min_pack_voltage',
            'max_temperature' => 'max_temperature',
            'min_temperature' => 'min_temperature'
        );

        // Array of strings
        $strings =  array('manufacture_date', 'condition', 'hibernatefile', 'adapter_id', 'family_code', 'adapter_serial_number', 'adapter_manufacturer', 'adapter_name', 'combined_sys_load', 'user_sys_load', 'thermal_level', 'battery_level', 'ups_name', 'active_profile', 'ups_charging_status', 'externalconnected', 'cellvoltage', 'manufacturer', 'batteryserialnumber', 'fullycharged', 'ischarging','sleep_prevented_by','schedule','adapter_description');

        // Traverse the xml with translations
        foreach ($translate as $search => $field) {

            // If key does not exist in $plist, null it
            if ( ! array_key_exists($search, $plist)) {
                $this->$field = null;

            // Else if is not a string and is numeric, save the value
            } else if ( ! in_array($field, $strings) && is_numeric($plist[$search])) {                 
                $this->$field = $plist[$search];

            // Else if a string, save the value
            } else if ( in_array($field, $strings)) {
                $this->$field = $plist[$search];

            // Else null the field
            } else {
                $this->$field = null;
            }
        }
    }

    /**
     * Adjust battery data
     *
     **/
    private function _adjust_battery_data()
    {
        // Check if no battery is inserted and adjust values
        if ( $this->condition == "No Battery" || $this->condition == "") {
            $this->manufacture_date = null;
            $this->design_capacity = null;
            $this->max_capacity = null;
            $this->max_percent = null;
            $this->current_percent = null;
            $this->current_capacity = null;
            $this->cycle_count = null;
            $this->temperature = null;

        } else {

            // Calculate maximum capacity as percentage of original capacity
            if ( $this->design_capacity > 0) {
                $this->max_percent = round(($this->max_capacity / $this->design_capacity * 100 ), 0 );
            } else {
                $this->max_percent = 0;
            }

            // Calculate percentage of current maximum capacity
            if ( $this->max_capacity > 0) {
                $this->current_percent = round(($this->current_capacity / $this->max_capacity * 100 ), 0 );
            } else {
                $this->current_percent = 0;
            }

            // Convert battery manufacture date to calendar date.
            // Bits 0...4 => day (value 1-31; 5 bits)
            // Bits 5...8 => month (value 1-12; 4 bits)
            // Bits 9...15 => years since 1980 (value 0-127; 7 bits)

            $ManufactureDate = $this->manufacture_date;

            if (is_numeric($this->manufacture_date)) {
                $mfgday = $this->manufacture_date & 31;
                $mfgmonth = ($this->manufacture_date >> 5) & 15;
                $mfgyear = (($this->manufacture_date >> 9) & 127) + 1980;

                $this->manufacture_date = sprintf("%d-%02d-%02d", $mfgyear, $mfgmonth, $mfgday);
             } else {
                // Hardcode the date to be 'null' for the time being until
                //   data from BigSur can be evaluated correctly. 
                $this->manufacture_date = null;
            }
        }

        // Timestamp added by the server
        $this->timestamp = time();

        // Fix condition
        $this->condition = str_replace(array('Normal','ServiceBattery','Service Battery','ReplaceSoon','Replace Soon','ReplaceNow','Replace Now'),array('Good','Service','Service','Fair','Fair','Poor','Poor'), $this->condition);
    }
}
