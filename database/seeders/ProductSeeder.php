<?php
		
		namespace Database\Seeders;
		
		use App\Models\Brand;
		use App\Models\Category;
		use App\Models\Product;
		use Illuminate\Database\Console\Seeds\WithoutModelEvents;
		use Illuminate\Database\Seeder;
		use Illuminate\Support\Str;
		class ProductSeeder extends Seeder
		{
				
				/**
					* Run the database seeds.
					*/
				public function run() : void
				{
						$product = Product::create( [
								'brand_id'          => Brand::find( 1 )->id,
								'name'              => 'SNEAKER SS-2151',
								'slug'              => Str::slug( 'SNEAKER SS-2151' ),
								'sku'               => '12345678',
								'barcode'           => '43998635196648',
								'description'       => 'Minimalist and casual sneakers with ultimate cushioning, memory effect insole for grip and attractive insole lining that enhance your style.',
								'qty'               => 500,
								'security_stock'    => 20,
								'featured'          => 0,
								'is_visible'        => 1,
								'old_price'         => 2500,
								'price'             => 3500,
								'cost'              => 2700,
								'type'              => '',
								'backorder'         => 1,
								'requires_shipping' => 1,
								'seo_title'         => 'SNEAKER SS-2151',
								'seo_description'   => 'Minimalist and casual sneakers with ultimate cushioning, memory effect insole for grip and attractive insole lining that enhance your style.',
						] );
						$category = Category::find( 1 );
						if( $category ) {
								$product->categories()->attach( $category );
						}
				}
				
		}
