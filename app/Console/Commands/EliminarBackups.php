<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class EliminarBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backups:eliminar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminar backups viejos';

    private $HOURS_AGO = 1;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();
        $files = Storage::files('backups');

        foreach ($files as $file) {
            $lastModified = Storage::lastModified($file);
            $time = Carbon::parse($lastModified);
            $hoursAgo = $time->diffInHours($now);

            if ($hoursAgo > $this->HOURS_AGO) Storage::delete($file);
        }
    }
}
