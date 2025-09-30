<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionFiscal extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $permissions = [

            'fiscal.access',
            'perhitungan_pajak_penghasilan.access',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
