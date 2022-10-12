<?php


namespace Database\Seeders;

use App\Models\MaterialStatus;
use App\Models\SpaceRole;
use Illuminate\Database\Seeder;

class MaterialStatusesSeeder extends Seeder
{
    private $items = [
        [
            'name' => 'Обычный',
            'code' => 'regular',
        ],
        [
            'name' => 'Неактуальный',
            'code' => 'irrelevant',
        ],
        [
            'name' => 'Официальный',
            'code' => 'official',
        ],
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->items as $item) {
            MaterialStatus::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}
