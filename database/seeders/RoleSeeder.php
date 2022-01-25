<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$roles = [
			[ 'name' => 'customer' ],
			[ 'name' => 'super-admin' ],
		];

		foreach ( $roles as $role ) {
			Role::create( [ 'name' => $role['name'] ] );
		}
	}
}
