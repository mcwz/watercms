<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        //init laravel-admin
        $userModel = config('admin.database.users_model');
        if ($userModel::count() == 0) {
            $this->call(AdminTablesSeeder::class);
            $this->call(TestDataSeed::class);
        }
    }
}
