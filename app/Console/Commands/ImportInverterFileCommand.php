<?php


namespace App\Console\Commands;

use App\Contracts\Services\InverterServiceContract;
use App\Models\Inverter;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportInverterFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:inverter {--file=CEC_Inverters.csv : название файла в папке storage/db_sources}';

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
        app(InverterServiceContract::class)->importFile(
            $filepath,
            ['name'],
            Inverter::class,
            [],
            [
                'cec_date' => function (string $value) {
                    try {
                        return Carbon::parse($value);
                    }
                    catch (\Exception) {
                        return null;
                    }
                },
                'cec_hybrid' => function (string $value) {
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
