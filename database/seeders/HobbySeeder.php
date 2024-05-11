<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hobby;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HobbySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Hobby::truncate();
        Schema::enableForeignKeyConstraints();
        
        Hobby::insert([
            [
                'name' => 'Football',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Basketball',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Guitar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Painting',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]
        );
       
    }
}
