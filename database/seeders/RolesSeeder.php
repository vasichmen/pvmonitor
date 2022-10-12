<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    private $items = [
        [
            'name' => 'Администратор',
            'code' => 'admin'
        ],
        [
            'name' => 'Пользователь',
            'code' => 'user'
        ],
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->items as $item) {
            Role::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}
