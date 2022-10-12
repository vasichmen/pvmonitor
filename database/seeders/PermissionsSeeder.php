<?php


namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    private $items = [
        [
            'name' => 'Редактировать хедер сайта',
            'code' => 'manage_site_header',
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
            Permission::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}
