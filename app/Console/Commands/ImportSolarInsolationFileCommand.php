<?php


namespace App\Console\Commands;

use App\Contracts\Services\MapDataServiceContract;
use Illuminate\Console\Command;

class ImportSolarInsolationFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:solar-insolation {--file= : название файла в папке storage/db_sources}';

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
        app(MapDataServiceContract::class)->importFile($filepath, function ($message) {
            $this->info($message);
        });

        return self::SUCCESS;
    }
}
