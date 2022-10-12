<?php


namespace Database\Seeders;

use App\Models\MaterialType;
use App\Models\SpaceRole;
use Illuminate\Database\Seeder;

class MaterialTypesSeeder extends Seeder
{
    private $items = [
        [
            'name' => 'Решённый вопрос',
            'code' => 'solved_question',
        ],
        [
            'name' => 'Нерешённый вопрос',
            'code' => 'unsolved_question',
        ],
        [
            'name' => 'Обсуждение',
            'code' => 'discussion',
        ],
        [
            'name' => 'Файл',
            'code' => 'file',
        ],
        [
            'name' => 'Запись в блоге',
            'code' => 'blog_post',
        ],
        [
            'name' => 'Вики-статья',
            'code' => 'wiki_page',
        ],
        [
            'name' => 'Закладка',
            'code' => 'bookmark',
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
            MaterialType::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}
