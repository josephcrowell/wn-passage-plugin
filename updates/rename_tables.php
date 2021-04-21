<?php

namespace JosephCrowell\Passage\Updates;

use Winter\Storm\Database\Updates\Migration;
use Schema;

class RenameTables extends Migration
{
    const TABLES = ["keys", "groups_keys", "variances"];

    public function up()
    {
        foreach (self::TABLES as $table) {
            $from = "kurtjensen_passage_" . $table;
            $to = "josephcrowell_passage_" . $table;

            if (Schema::hasTable($from) && !Schema::hasTable($to)) {
                Schema::rename($from, $to);
            }
        }
    }

    public function down()
    {
        foreach (self::TABLES as $table) {
            $from = "josephcrowell_passage_" . $table;
            $to = "kurtjensen_passage_" . $table;

            if (Schema::hasTable($from) && !Schema::hasTable($to)) {
                Schema::rename($from, $to);
            }
        }
    }
}