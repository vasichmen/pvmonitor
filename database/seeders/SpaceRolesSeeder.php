<?php


namespace Database\Seeders;

use App\Models\SpaceRole;
use Illuminate\Database\Seeder;

class SpaceRolesSeeder extends Seeder
{
    private $items = [
        [
            'name' => 'Администратор пространства',
            'code' => 'space_admin',
        ],
        [
            'name' => 'Модератор пространства',
            'code' => 'space_moderator',
        ],
        [
            'name' => 'Приглашенный участник',
            'code' => 'invited_participant',
        ],
        [
            'name' => 'Участник',
            'code' => 'participant',
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
            SpaceRole::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}
