<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        // DB::unprepared("SET GLOBAL max_allowed_packet=1073741824;");
        DB::unprepared(File::get(base_path("sql/districts.sql")));

        Schema::enableForeignKeyConstraints();
    }
}
