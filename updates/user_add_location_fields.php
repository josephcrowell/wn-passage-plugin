<?php
namespace JosephCrowell\Passage\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class UserAddLocationFields extends Migration
{
    public function up()
    {
        if (Schema::hasColumns("users", ["state_id", "country_id"]))
        {
            return;
        }

        Schema::table("users", function ($table)
        {
            $table
                ->integer("state_id")
                ->unsigned()
                ->nullable()
                ->index();
            $table
                ->integer("country_id")
                ->unsigned()
                ->nullable()
                ->index();
        });
    }

    public function down()
    {
        if (Schema::hasTable("users"))
        {
            Schema::table("users", function ($table)
            {
                $table->dropColumn(["state_id", "country_id"]);
            });
        }
    }
}
