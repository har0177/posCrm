<?php
		
		namespace Database\Seeders;
		
		use App\Models\Address;
		use App\Models\Customer;
		use App\Models\Order;
		use App\Models\Product;
		use Illuminate\Database\Console\Seeds\WithoutModelEvents;
		use Illuminate\Database\Seeder;
		class OrderSeeder extends Seeder
		{
				
				/**
					* Run the database seeds.
					*/
				public function run() : void
				{
						$order = Order::create( [
								'number'      => 'OR-953734',
								'customer_id' => Customer::find( 1 )->id,
								'total_price' => Product::find( 1 )->price,
								'status'      => 'new',
								'currency'    => 'usd',
								'notes'       => 'Testing Order',
						] );
						$order->items()->create( [
								'sort'       => 1,
								'product_id' => Product::find( 1 )->id,
								'qty'        => 1,
								'unit_price' => Product::find( 1 )->price,
						] );
						// Now, let's associate an address with the order
						$order->address()->create( [
								'country' => 'pk',
								'street'  => 'Noor Corporation Abasin Marker No.2 Mingora',
								'city'    => 'Mingora',
								'state'   => 'KP',
								'zip'     => '19130',
						] );
						
				}
				
		}
