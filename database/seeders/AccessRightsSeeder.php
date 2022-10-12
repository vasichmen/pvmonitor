<?php


namespace Database\Seeders;

use App\Models\AccessRight;
use Illuminate\Database\Seeder;

class AccessRightsSeeder extends Seeder
{
    private $items = [
        [
            'name' => 'Просмотр',
            'code' => 'view',
            'level' => '10',
        ],
        [
            'name' => 'Оценка',
            'code' => 'like',
            'level' => '20',
        ],
        [
            'name' => 'Комментирование',
            'code' => 'comment',
            'level' => '30',
        ],
        [
            'name' => 'Изменение',
            'code' => 'update',
            'level' => '40',
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
            AccessRight::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}
