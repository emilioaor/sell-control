<?php

use Illuminate\Database\Seeder;

class PhoneBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $phoneBrand = new \App\PhoneBrand();
        $phoneBrand->name = 'Apple';
        $phoneBrand->save();

        $phoneBrand = new \App\PhoneBrand();
        $phoneBrand->name = 'Samsung';
        $phoneBrand->save();

        $phoneBrand = new \App\PhoneBrand();
        $phoneBrand->name = 'Xiaomi';
        $phoneBrand->save();

        $phoneBrand = new \App\PhoneBrand();
        $phoneBrand->name = 'Motorola';
        $phoneBrand->save();

        $phoneBrand = new \App\PhoneBrand();
        $phoneBrand->name = 'Huawei';
        $phoneBrand->save();

        $phoneBrand = new \App\PhoneBrand();
        $phoneBrand->name = 'Yezz';
        $phoneBrand->save();

        $phoneBrand = new \App\PhoneBrand();
        $phoneBrand->name = 'Emerging';
        $phoneBrand->save();
    }
}
