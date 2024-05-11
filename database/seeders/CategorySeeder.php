<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Schema::disableForeignKeyConstraints();
        Category::truncate();
        Schema::enableForeignKeyConstraints();

        Category::insert([
            [
                'name' => 'Sports',
                'description' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ad impedit eaque veritatis aperiam amet officiis quia alias quo laboriosam. Voluptate quae dignissimos repudiandae in quo, a numquam non ea atque vero sapiente esse consectetur! Delectus?',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Music',
                'description' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ad impedit eaque veritatis aperiam amet officiis quia alias quo laboriosam. Voluptate quae dignissimos repudiandae in quo, a numquam non ea atque vero sapiente esse consectetur! Delectus?',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Art',
                'description' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ad impedit eaque veritatis aperiam amet officiis quia alias quo laboriosam. Voluptate quae dignissimos repudiandae in quo, a numquam non ea atque vero sapiente esse consectetur! Delectus?',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]
        );
    }
}
