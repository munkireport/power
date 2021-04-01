<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class PowerBigSurColumns extends Migration
{
    private $tableName = 'power';

    public function up()
    {
        $capsule = new Capsule();

        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->float('adapter_current')->nullable();
            $table->float('adapter_voltage')->nullable();
            $table->float('charging_current')->nullable();
            $table->float('charging_voltage')->nullable();

            $table->index('adapter_current');
            $table->index('adapter_voltage');
            $table->index('charging_current');
            $table->index('charging_voltage');
        });
    }

    public function down()
    {
        $capsule = new Capsule();

        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('adapter_current');
            $table->dropColumn('adapter_voltage');
            $table->dropColumn('charging_current');
            $table->dropColumn('charging_voltage');
        });
    }
}
