<?php

namespace App\Console\Commands;

use App\Models\FechaGrado;
use App\Models\ProcesoGrado;
use App\Tools\Variables;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DesactivarFechas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fechas:desactivar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desactivar viejas fechas de grado';

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
        $estados = Variables::estados();
        $fechas = FechaGrado::where('estado', true)
            ->where('fecha_grado', '<', Carbon::now())
            ->get();

        foreach ($fechas as $fec) {
            $pg_ids = $fec->procesosGrado()
                ->where('estado_secretaria_id', '<>', $estados['aprobado']->id)
                ->select('id')
                ->get()
                ->pluck('id');

            ProcesoGrado::whereIn('id', $pg_ids)->update(['no_aprobado' => true]);
        }
    }
}
