<?php
/**
 * power status module class
 *
 * @package munkireport
 * @author
 **/
class Power_controller extends Module_controller
{
    
    /*** Protect methods with auth! ****/
    public function __construct()
    {
        // Store module path
        $this->module_path = dirname(__FILE__);
    }
    /**
     * Default method
     *
     * @author AvB
     **/
    public function index()
    {
        echo "You've loaded the power module!";
    }

    /**
     * Get Power Statistics
     *
     *
     **/
    public function get_stats()
    {
        $obj = new View();
        $pm = new Power_model;
        $out[] = $pm->get_stats();
        $obj->view('json', array('msg' => $out));
    }

    /**
     * Get conditions
     *
     * @return void
     * @author AvB
     **/
    public function conditions()
    {
        $obj = new View();
        $queryobj = new Power_model();
        $sql = "SELECT COUNT(CASE WHEN `condition` = 'Normal' OR `condition` = 'Good' THEN 1 END) AS good,
						COUNT(CASE WHEN `condition` = 'Service Battery' OR `condition` = 'ServiceBattery' OR `condition` = 'Check Battery' THEN 1 END) AS service,
						COUNT(CASE WHEN `condition` = 'Replace Soon' OR `condition` = 'ReplaceSoon' OR `condition` = 'Fair' THEN 1 END) AS fair,
						COUNT(CASE WHEN `condition` = 'Replace Now' OR `condition` = 'ReplaceNow' OR `condition` = 'Poor' THEN 1 END) AS poor,
                        COUNT(CASE WHEN `condition` = 'No Battery' OR `condition` = 'NoBattery' THEN 1 END) AS missing
                        FROM power
			 			LEFT JOIN reportdata USING (serial_number)
			 			".get_machine_group_filter();
        $obj->view('json', array('msg' => current($queryobj->query($sql))));
    }

    /**
     * Retrieve data in json format
     *
     **/
    public function get_data($serial_number = '')
    {
        $serial_number = preg_replace("/[^A-Za-z0-9_\-]]/", '', $serial_number);

        $sql = "SELECT `manufacture_date`, `design_capacity`, `max_capacity`, `max_percent`, `current_capacity`, `current_percent`, `cycle_count`, `designcyclecount`, `condition`, `temperature`, `externalconnected`, `ischarging`, `fullycharged`, `avgtimetofull`, `avgtimetoempty`, `timeremaining`, `instanttimetoempty`, `amperage`, `voltage`, `cellvoltage`, `permanentfailurestatus`, `manufacturer`, `batteryserialnumber`, `packreserve`, `wattage`, `adapter_name`, `adapter_manufacturer`, `adapter_current`, `adapter_voltage`, `adapter_id`, `family_code`, `adapter_serial_number`, `ups_name`, `ups_percent`, `ups_charging_status`, `haltlevel`, `haltafter`, `haltremain`, `active_profile`, `schedule`, `sleep_count`, `dark_wake_count`, `user_wake_count`, `standbydelay`, `standby`, `womp`, `halfdim`, `hibernatefile`, `gpuswitch`, `sms`, `networkoversleep`, `disksleep`, `sleep`, `autopoweroffdelay`, `hibernatemode`, `autopoweroff`, `ttyskeepawake`, `displaysleep`, `acwake`, `lidwake`, `sleep_on_power_button`, `powernap`, `autorestart`, `destroyfvkeyonstandby`, `cpu_scheduler_limit`, `cpu_available_cpus`, `cpu_speed_limit`, `combined_sys_load`, `user_sys_load`, `battery_level`, `thermal_level`, `backgroundtask`, `applepushservicetask`, `userisactive`, `preventuseridledisplaysleep`, `preventsystemsleep`, `externalmedia`, `preventuseridlesystemsleep`, `networkclientactive`, `sleep_prevented_by`
                        FROM power 
                        WHERE serial_number = '$serial_number'";

        $queryobj = new Power_model;
        $power_data = $queryobj->query($sql)[0];
        $temp_format = conf('temperature_unit');
        $power_data->temp_format = $temp_format; // Add the temp format for use in the client tab's JavaScript    
        jsonView($power_data);
    }
} // END class Power_controller
