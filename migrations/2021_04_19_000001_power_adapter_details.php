<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class PowerAdapterDetails extends Migration
{
    private $tableName = 'power';

    public function up()
    {
        $capsule = new Capsule();

        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->string('adapter_manufacturer')->nullable();
            $table->string('adapter_name')->nullable();
        });
    }

    public function down()
    {
        $capsule = new Capsule();

        $capsule::schema()->table($this->tableName, function (Blueprint $table) {
            $table->dropColumn('adapter_manufacturer');
            $table->dropColumn('adapter_name');
        });
    }
}
