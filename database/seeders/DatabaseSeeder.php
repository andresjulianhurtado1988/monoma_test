<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Candidate;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        User::factory()->create();
       
         User::create([
            'username' => 'tester',
            'password'  => hash('sha256', "PASSWORD"),
            'is_active' => 1,
            'role' => 'agent']);

        Candidate::factory(10)->create();
            

    }
}
