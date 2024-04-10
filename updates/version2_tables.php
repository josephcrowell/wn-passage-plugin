<?php
namespace JosephCrowell\Passage\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class Version2Tables extends Migration
{
    public function up()
    {
        Schema::rename("josephcrowell_passage_keys", "josephcrowell_passage_permissions");

        Schema::table("josephcrowell_passage_groups_keys", function (Blueprint $table) {
            $table->renameColumn("key_id", "permission_id");
        });
        Schema::rename("josephcrowell_passage_groups_keys", "josephcrowell_passage_groups_permissions");

        Schema::table("josephcrowell_passage_variances", function (Blueprint $table) {
            $table->renameColumn("key_id", "permission_id");
        });
        Schema::rename("josephcrowell_passage_variances", "josephcrowell_passage_overrides");
    }

    public function down()
    {
        Schema::rename("josephcrowell_passage_permissions", "josephcrowell_passage_keys");

        Schema::table("josephcrowell_passage_groups_permissions", function (Blueprint $table) {
            $table->renameColumn("permission_id", "key_id");
        });
        Schema::rename("josephcrowell_passage_groups_permissions", "josephcrowell_passage_groups_keys");

        Schema::table("josephcrowell_passage_overrides", function (Blueprint $table) {
            $table->renameColumn("permission_id", "key_id");
        });
        Schema::rename("josephcrowell_passage_overrides", "josephcrowell_passage_variances");
    }
}
