<?php

use App\Franchise;
use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Create a HeadOffice user
        factory(User::class)->create([
            'username' => 'headoffice1',
            'user_type' => User::HEAD_OFFICE
        ]);

        Franchise::all()->each(function ($franchise)  {
            if($franchise->isParent()){
                dump('Creating Franchise Admin');
                factory(User::class)->create([
                    'username' => 'franchiseadmin' . $franchise->id,
                    'user_type' => User::FRANCHISE_ADMIN
                ]);
            }else {
                dump('Creating Staff User');
                factory(User::class)->create([
                    'username' => 'staffuser' . $franchise->id,
                    'user_type' => User::STAFF_USER
                ]);
            }

        });

    }
}
