<?php
		
		namespace Database\Seeders;
		
		use App\Models\Brand;
		use Illuminate\Database\Console\Seeds\WithoutModelEvents;
		use Illuminate\Database\Seeder;
		use Illuminate\Support\Str;
		class BrandSeeder extends Seeder
		{
				
				/**
					* Run the database seeds.
					*/
				public function run() : void
				{
						Brand::create( [
								'name'        => 'Urbansole',
								'slug'        => Str::slug( 'Urbansole' ),
								'website'     => 'https://www.urbansole.com.pk',
								'description' => "Urbansole’s journey started with the first step take '1998, when the company was formed as a national brand with an international outlook.",
								'position'    => 0,
								'is_visible'  => 1,
								'sort'        => 1,
						] );
				}
				
		}
