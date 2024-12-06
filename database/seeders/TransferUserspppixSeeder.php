<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransferUserspppixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Renomeia a tabela antiga para usersantiga
        Schema::rename('users', 'usersantiga');

        $data = DB::table('usersantiga')
            ->select([
                'id',
                'fcm_token',
                'car_id',
                'email',
                'phone',
                'group_name',
                'passwordBank',
                'passwordApp',
                'passwordEmergecy',
                'passwordDevice',
                'passwordDeviceEmergency',
                'lat',
                'log',
                'last_location_at',
                'remember_token',
                'password',
                'created_at',
                'updated_at'
            ])
            ->get()
            ->map(function ($item) {
                return (array) $item; // Converte cada objeto stdClass em array associativo
            })
            ->toArray();

        // Insere os dados na tabela nova
        DB::table('userspp')->insert($data);

        // Apaga a tabela antiga
        DB::statement('DROP TABLE IF EXISTS usersantiga CASCADE');

        // Renomeia a tabela antiga
        Schema::rename('userspp', 'users');
    }
}
