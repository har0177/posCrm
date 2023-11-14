<?php
		
		namespace Database\Seeders;
		
		use App\Models\Brand;
		use App\Models\Customer;
		use App\Models\Role;
		use App\Models\User;
		use Illuminate\Database\Console\Seeds\WithoutModelEvents;
		use Illuminate\Database\Seeder;
		use Illuminate\Support\Facades\Hash;
		use Illuminate\Support\Str;
		class UserSeeder extends Seeder
		{
				
				/**
					* Run the database seeds.
					*/
				public function run() : void
				{
						User::create( [
								'name'     => 'Admin',
								'email'    => 'admin@admin.com',
								'password' => Hash::make( 'admin' ),
								'status'   => 1,
								'role_id'  => Role::find( 1 )->id,
						] );
				}
				
		}
