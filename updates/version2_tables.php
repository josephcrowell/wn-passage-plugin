<?php namespace JosephCrowell\Passage\Updates;

use Winter\Storm\Database\Updates\Migration;
use Schema;

class Version2Tables extends Migration
{
    public function up()
    {
        if (Schema::hasTable("josephcrowell_passage_keys") && !Schema::hasTable("josephcrowell_passage_permissions")) {
            Schema::rename("josephcrowell_passage_keys", "josephcrowell_passage_permissions");
        }

        if (
            Schema::hasTable("josephcrowell_passage_groups_keys") &&
            !Schema::hasTable("josephcrowell_passage_groups_permissions")
        ) {
            Schema::rename("josephcrowell_passage_groups_keys", "josephcrowell_passage_groups_permissions");
        }

        if (Schema::hasTable("josephcrowell_passage_variances") && !Schema::hasTable("josephcrowell_passage_overrides")) {
            Schema::rename("josephcrowell_passage_variances", "josephcrowell_passage_overrides");
        }
    }

    public function down()
    {
        foreach (self::TABLES as $table) {
            if (Schema::hasTable("josephcrowell_passage_permissions") && !Schema::hasTable("josephcrowell_passage_keys")) {
                Schema::rename("josephcrowell_passage_permissions", "josephcrowell_passage_keys");
            }

            if (
                Schema::hasTable("josephcrowell_passage_groups_permissions") &&
                !Schema::hasTable("josephcrowell_passage_groups_keys")
            ) {
                Schema::rename("josephcrowell_passage_groups_permissions", "josephcrowell_passage_groups_keys");
            }

            if (Schema::hasTable("josephcrowell_passage_overrides") && !Schema::hasTable("josephcrowell_passage_variances")) {
                Schema::rename("josephcrowell_passage_overrides", "josephcrowell_passage_variances");
            }
        }
    }
}