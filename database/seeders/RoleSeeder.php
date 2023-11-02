<?php
		
		namespace Database\Seeders;
		
		use App\Models\Role;
		use App\Models\User;
		use Illuminate\Database\Console\Seeds\WithoutModelEvents;
		use Illuminate\Database\Seeder;
		use Illuminate\Support\Facades\Hash;
		class RoleSeeder extends Seeder
		{
				
				/**
					* Run the database seeds.
					*/
				public function run() : void
				{
						Role::create( [
								'name'        => 'Super Admin',
								'permissions' => [ "add role", "manage users", "add user" ],
						] );
				}
				
		}
