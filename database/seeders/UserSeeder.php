<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

		// This will create one admin user
	    $admin = User::create([
		    'name' => Str::random(10),
		    'username' => Str::random(10),
		    'email' => Str::random(10).'@gmail.com',
		    'password' => Hash::make('secret'),
		    'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
		    'registered_at' => Carbon::now()->format('Y-m-d H:i:s'),
	    ]);

		$admin->assignRole('super-admin');

    }
}
