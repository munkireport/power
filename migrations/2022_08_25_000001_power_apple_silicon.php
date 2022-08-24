<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class PowerAppleSilicon extends Migration
{
    private $tableName = 'power';

    public function up()
    {
        $capsule = new Capsule();

        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->string('adapter_description')->nullable();
            $table->string('max_charge_current')->nullable();
            $table->string('max_discharge_current')->nullable();
            $table->string('max_pack_voltage')->nullable();
            $table->string('min_pack_voltage')->nullable();
            $table->string('max_temperature')->nullable();
            $table->string('min_temperature')->nullable();
        });
    }

    public function down()
    {
        $capsule = new Capsule();

        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('adapter_description');
            $table->dropColumn('max_charge_current');
            $table->dropColumn('max_discharge_current');
            $table->dropColumn('max_pack_voltage');
            $table->dropColumn('min_pack_voltage');
            $table->dropColumn('max_temperature');
            $table->dropColumn('min_temperature');
        });
    }
}
