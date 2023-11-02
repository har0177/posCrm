<?php
		
		namespace Database\Seeders;
		
		use App\Models\Address;
		use App\Models\Brand;
		use App\Models\Customer;
		use App\Models\Product;
		use Illuminate\Database\Console\Seeds\WithoutModelEvents;
		use Illuminate\Database\Seeder;
		use Illuminate\Support\Str;
		class CustomerSeeder extends Seeder
		{
				
				/**
					* Run the database seeds.
					*/
				public function run() : void
				{
						$customer = Customer::create( [
								'name'   => 'Haroon Yousaf',
								'email'  => 'haroonyousaf80@gmail.com',
								'gender' => 'Male',
								'phone'  => '03339471086',
						] );
						$addressData = [
								'country'      => 'pk',
								'street'       => 'Noor Corporation Abasin Marker No.2 Mingora',
								'city'         => 'Mingora',
								'state'        => 'KP',
								'zip'          => '19130',
						];
						$address = Address::create( $addressData );
						$customer->addresses()->attach( $address );
				}
				
		}
