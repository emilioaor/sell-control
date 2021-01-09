<?php

use Illuminate\Database\Seeder;

class PhoneTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $phoneType = new \App\PhoneType();
        $phoneType->name = 'Feature phone';
        $phoneType->save();

        $phoneType = new \App\PhoneType();
        $phoneType->name = 'Smartphone';
        $phoneType->save();
    }
}
