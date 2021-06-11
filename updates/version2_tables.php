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
            if (Schema::hasColumn("josephcrowell_passage_groups_keys", "key_id")) {
                Schema::table("josephcrowell_passage_groups_keys", function (Blueprint $table) {
                    $table->renameColumn("key_id", "permission_id");

                    $table->renameIndex("key_id", "permission_id");
                });
            }

            Schema::rename("josephcrowell_passage_groups_keys", "josephcrowell_passage_groups_permissions");
        }

        if (Schema::hasTable("josephcrowell_passage_variances") && !Schema::hasTable("josephcrowell_passage_overrides")) {
            if (Schema::hasColumn("josephcrowell_passage_variances", "key_id")) {
                Schema::table("josephcrowell_passage_variances", function (Blueprint $table) {
                    $table->renameColumn("key_id", "permission_id");

                    $table->renameIndex("key_id", "permission_id");
                });
            }

            Schema::rename("josephcrowell_passage_variances", "josephcrowell_passage_overrides");
        }
    }

    public function down()
    {
        if (Schema::hasTable("josephcrowell_passage_permissions") && !Schema::hasTable("josephcrowell_passage_keys")) {
            Schema::rename("josephcrowell_passage_permissions", "josephcrowell_passage_keys");
        }

        if (
            Schema::hasTable("josephcrowell_passage_groups_permissions") &&
            !Schema::hasTable("josephcrowell_passage_groups_keys")
        ) {
            if (Schema::hasColumn("josephcrowell_passage_groups_permissions", "permission_id")) {
                Schema::table("josephcrowell_passage_groups_permissions", function (Blueprint $table) {
                    $table->renameColumn("permission_id", "key_id");

                    $table->renameIndex("permission_id", "key_id");
                });
            }

            Schema::rename("josephcrowell_passage_groups_permissions", "josephcrowell_passage_groups_keys");
        }

        if (Schema::hasTable("josephcrowell_passage_overrides") && !Schema::hasTable("josephcrowell_passage_variances")) {
            if (Schema::hasColumn("josephcrowell_passage_overrides", "permission_id")) {
                Schema::table("josephcrowell_passage_overrides", function (Blueprint $table) {
                    $table->renameColumn("permission_id", "key_id");

                    $table->renameIndex("permission_id", "key_id");
                });
            }

            Schema::rename("josephcrowell_passage_overrides", "josephcrowell_passage_variances");
        }
    }
}