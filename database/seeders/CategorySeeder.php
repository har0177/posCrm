<?php
		
		namespace Database\Seeders;
		
		use App\Models\Brand;
		use App\Models\Category;
		use Illuminate\Database\Seeder;
		use Illuminate\Support\Str;
		class CategorySeeder extends Seeder
		{
				
				/**
					* Run the database seeds.
					*/
				public function run() : void
				{
						Category::create( [
								'name'        => 'Sneaker',
								'slug'        => Str::slug( 'Sneaker' ),
								'description' => "Urbansole’s journey started with the first step take '1998, when the company was formed as a national brand with an international outlook.",
								'position'    => 0,
								'is_visible'  => 1,
						] );
				}
				
		}
