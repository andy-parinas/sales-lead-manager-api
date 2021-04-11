<?php

use App\Franchise;
use App\User;
use Illuminate\Database\Seeder;

class UserActualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['username' => 'franchiseadmin-2107','franchise' => '2107',
              'user_type' => User::FRANCHISE_ADMIN, 'email' => 'northernbeaches@spanline.com.au',
              'name' => 'Franchise 2107', 'password' => 'password2107'],

            ['username' => 'franchiseadmin-3555','franchise' => '3555',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'bendigo@spanline.com.au',
                'name' => 'Franchise 3555', 'password' => 'password3555'],

            ['username' => 'franchiseadmin-4074','franchise' => '4074',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'briswest@spanline.com.au',
                'name' => 'Franchise 4074', 'password' => 'password4074'],

            ['username' => 'franchiseadmin-2830','franchise' => '2830',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'dubbo@spanline.com.au',
                'name' => 'Franchise 2830', 'password' => 'password2830'],

            ['username' => 'franchiseadmin-4127','franchise' => '4127',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'brissouth@spanline.com.au',
                'name' => 'Franchise 4127', 'password' => 'password4127'],

            ['username' => 'franchiseadmin-2800','franchise' => '2800',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'orange@spanline.com.au',
                'name' => 'Franchise 2800', 'password' => 'password2800'],

            ['username' => 'franchiseadmin-4740','franchise' => '4740',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'mackay@spanline.com.au',
                'name' => 'Franchise 4740', 'password' => 'password4740'],

            ['username' => 'franchiseadmin-2650','franchise' => '2650',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'riverina@spanline.com.au',
                'name' => 'Franchise 2650', 'password' => 'password2650'],

            ['username' => 'franchiseadmin-2160F','franchise' => '2160F',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'sydneysw@spanline.com.au',
                'name' => 'Franchise 2160F', 'password' => 'password2160F'],

            ['username' => 'franchiseadmin-2527','franchise' => '2527',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'illawarra@spanline.com.au',
                'name' => 'Franchise 2527', 'password' => 'password2527'],

            ['username' => 'franchiseadmin-2315','franchise' => '2315',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'portstephens@spanline.com.au',
                'name' => 'Franchise 2315', 'password' => 'password2315'],

            ['username' => 'franchiseadmin-2450QA','franchise' => '2450QA',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'northcoast@spanline.com.au',
                'name' => 'Franchise 2450QA', 'password' => 'password2450QA'],

            ['username' => 'franchiseadmin-2285','franchise' => '2285',
                'user_type' => User::FRANCHISE_ADMIN, 'email' => 'newcastle@spanline.com.au',
                'name' => 'Franchise 2285', 'password' => 'password2285'],

            ['username' => 'staffuser-2160F','franchise' => '2160F',
                'user_type' => User::STAFF_USER, 'email' => 'sydneysw@spanline.com.au',
                'name' => 'Staff 2160F', 'password' => 'password2160F'],

            ['username' => 'staffuser-4211GC','franchise' => '4211GC',
                'user_type' => User::STAFF_USER, 'email' => 'goldcoast@spanline.com.au',
                'name' => 'Staff 4211GC', 'password' => 'password4211GC'],

            ['username' => 'staffuser-4127','franchise' => '4127',
                'user_type' => User::STAFF_USER, 'email' => 'brissouth@spanline.com.au',
                'name' => 'Staff 4127', 'password' => 'password4127'],

            ['username' => 'staffuser-2230F','franchise' => '2230F',
                'user_type' => User::STAFF_USER, 'email' => 'sydneysouth@spanline.com.au',
                'name' => 'Staff 2230F', 'password' => 'password2230F'],

            ['username' => 'staffuser-4500','franchise' => '4500',
                'user_type' => User::STAFF_USER, 'email' => 'brisnorth@spanline.com.au',
                'name' => 'Staff 4500', 'password' => 'password4500'],

            ['username' => 'staffuser-4074','franchise' => '4074',
                'user_type' => User::STAFF_USER, 'email' => 'briswest@spanline.com.au',
                'name' => 'Staff 4074', 'password' => 'password4074'],

            ['username' => 'staffuser-3630','franchise' => '3630',
                'user_type' => User::STAFF_USER, 'email' => 'shepparton@spanline.com.au',
                'name' => 'Staff 3630', 'password' => 'password3630'],

            ['username' => 'staffuser-3555','franchise' => '3555',
                'user_type' => User::STAFF_USER, 'email' => 'bendigo@spanline.com.au',
                'name' => 'Staff 3555', 'password' => 'password3555'],

            ['username' => 'staffuser-3500','franchise' => '3500',
                'user_type' => User::STAFF_USER, 'email' => 'mildura@spanline.com.au',
                'name' => 'Staff 3500', 'password' => 'password3500'],

            ['username' => 'staffuser-3350','franchise' => '3350',
                'user_type' => User::STAFF_USER, 'email' => 'ballarat@spanline.com.au',
                'name' => 'Staff 3350', 'password' => 'password3350'],

            ['username' => 'staffuser-3023','franchise' => '3023',
                'user_type' => User::STAFF_USER, 'email' => 'melbwest@spanline.com.au',
                'name' => 'Staff 3023', 'password' => 'password3023'],

            ['username' => 'staffuser-2830','franchise' => '2830',
                'user_type' => User::STAFF_USER, 'email' => 'dubbo@spanline.com.au',
                'name' => 'Staff 2830', 'password' => 'password2830'],

            ['username' => 'staffuser-2444','franchise' => '2444',
                'user_type' => User::STAFF_USER, 'email' => 'midcoast@spanline.com.au',
                'name' => 'Staff 2444', 'password' => 'password2444'],

            ['username' => 'staffuser-2340','franchise' => '2340',
                'user_type' => User::STAFF_USER, 'email' => 'tamworth@spanline.com.au',
                'name' => 'Staff 2340', 'password' => 'password2340'],

            ['username' => 'staffuser-2541BM','franchise' => '2541BM',
                'user_type' => User::STAFF_USER, 'email' => 'southcoast@spanline.com.au',
                'name' => 'Staff 2541BM', 'password' => 'password2541BM'],

            ['username' => 'staffuser-2650','franchise' => '2650',
                'user_type' => User::STAFF_USER, 'email' => 'riverina@spanline.com.au',
                'name' => 'Staff 2650', 'password' => 'password4127'],

            ['username' => 'staffuser-2640','franchise' => '2640',
                'user_type' => User::STAFF_USER, 'email' => 'albury@spanline.com.au',
                'name' => 'Staff 2640', 'password' => 'password2640'],

            ['username' => 'staffuser-2609','franchise' => '2609',
                'user_type' => User::STAFF_USER, 'email' => 'act@spanline.com.au',
                'name' => 'Staff 4127', 'password' => 'password2609'],

            ['username' => 'staffuser-2285','franchise' => '2285',
                'user_type' => User::STAFF_USER, 'email' => 'newcastle@spanline.com.au',
                'name' => 'Staff 2285', 'password' => 'password2285'],

            ['username' => 'staffuser-2263','franchise' => '2263',
                'user_type' => User::STAFF_USER, 'email' => 'newcastle@spanline.com.au',
                'name' => 'Staff 2263', 'password' => 'password2263'],

            ['username' => 'headoffice1',
                'user_type' => User::HEAD_OFFICE, 'email' => 'services@spanline.com.au',
                'name' => 'Head Office', 'password' => 'passwordheadoffice'],

        ];

        foreach ($users as $user)
        {

            $newUser = User::create([
                'username' => $user['username'],
                'name' => $user['name'],
                'email'=> $user['email'],
                'password' => $user['password'],
                'user_type' => $user['user_type']
            ]);

            if(isset($user['franchise']))
            {
                dump('Key Exist');

                $franchise = Franchise::where('franchise_number', $user['franchise'])->first();

                if($franchise !== null)
                {
                    $newUser->franchises()->attach($franchise->id);

                    dump('Franchise set');
                }
            }

            dump("User Created");
        }

    }
}
