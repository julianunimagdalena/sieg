<?php

namespace App\Console\Commands;

use App\Models\Estudiante;
use App\Tools\WSFoto;
use Illuminate\Console\Command;

class ActualizarEstadoFoto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foto:actualizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado de la foto pendiente en sieg';

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
        $info = (object) ['aprobados' => 0, 'rechazados' => 0];
        $ws = new WSFoto();
        $estudiantes = Estudiante::egresados()
            ->whereHas('procesoGrado', function ($pg) {
                $pg->where('foto_aprobada', false)->where('foto_cargada', true);
            })
            ->get();

        foreach ($estudiantes as $estudiante) {
            $pg = $estudiante->procesoGrado;
            $res = $ws->consultarEstado($pg->est_carnetizacion_id);

            switch ($res) {
                case 'aprobado':
                    $pg->foto_aprobada = true;
                    $pg->est_carnetizacion_id = null;
                    $pg->save();
                    $info->aprobados++;
                    break;
                case 'rechazado':
                    $pg->foto_cargada = false;
                    $pg->est_carnetizacion_id = null;
                    $pg->save();
                    $info->rechazados++;
                    break;
            }
        }

        $this->info("aprobados: $info->aprobados, rechazados: $info->rechazados");
    }
}
