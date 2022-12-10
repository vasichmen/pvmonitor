<?php


namespace App\Console\Commands;

use App\Contracts\Services\ModuleServiceContract;
use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportModuleFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:module {--file=CEC_Modules.csv : название файла в папке storage/db_sources}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импорт файла ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filepath = $this->option('file') ?? throw new \Exception("Файл не задан");
        $filepath = '/db_sources/' . $filepath;
        app(ModuleServiceContract::class)->importFile(
            $filepath,
            ['name'],
            Module::class,
            ['length', 'width', 't_noct'],
            [
                'date' => function (string $value) {
                    try {
                        return Carbon::parse($value);
                    }
                    catch (\Exception) {
                        return null;
                    }
                },
                'bipv' => function (string $value) {
                    return $value === 'Y';
                },
            ],
            function ($message) {
                $this->info($message);
            }
        );

        return self::SUCCESS;
    }
}
