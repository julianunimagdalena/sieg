<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\Estudiante;
use App\Models\FechaGrado;
use App\Models\TipoGrado;
use App\Tools\Variables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private $PERSONS_BY_PACKAGE = 500;

    public function baseFolder()
    {
        return storage_path() . '/app/backups/';
    }

    private function getFilename($request)
    {
        $name = 'backup';

        if ($request->programa_id) {
            $programa = Dependencia::find($request->programa_id);
            $name .= ' ' . $programa->nombre;
        }

        if ($request->fecha_grado_id) {
            $fecha = FechaGrado::find($request->fecha_grado_id);
            $name .= ' ' . $fecha->nombre;
        } else if ($request->tipo_grado_id) {
            $tipo = TipoGrado::find($request->tipo_grado_id);
            $name .= ' ' . $tipo->nombre;
        }

        return $name .  ' ' . Carbon::now()->timestamp . '.part1.zip';
    }

    public function estudiantes(Request $request)
    {
        $downloadFiles = [];
        $controller = new SecretariaGeneralController();
        $estudiantes = $controller->getEstudiantes($request)->get();

        if ($estudiantes->count() !== 0) {
            $part = 1;
            $estados = Variables::estados();
            $zip = new \ZipArchive();
            $filename = $this->getFilename($request);
            $zip->open($this->baseFolder() . $filename, \ZipArchive::CREATE);

            foreach ($estudiantes as $key => $est) {
                if ($key !== 0 && $key % $this->PERSONS_BY_PACKAGE === 0) {
                    $zip->close();
                    array_push($downloadFiles, $filename);

                    $filename = str_replace('part' . $part, 'part' . ($part + 1), $filename);
                    $part++;

                    $zip->open($this->baseFolder() . $filename, \ZipArchive::CREATE);
                }

                $ed = $est->estudianteDocumento()->where('estado_id', '<>', $estados['sin_cargar']->id)->first();

                if ($ed) {
                    $fecha = $est->procesoGrado->fechaGrado;
                    $programa = $est->estudio->programa;
                    $nombre = $est->persona->nombre_invertido;

                    $options = [
                        'add_path' => $fecha->fecha_grado . '/' . $programa->nombre . '/' . $nombre . '/',
                        'remove_all_path' => TRUE
                    ];

                    $zip->addGlob(storage_path() . '/app/' . $ed->folder . '/*', GLOB_BRACE, $options);
                }
            }

            $zip->close();

            if ($part === 1) {
                $newfilename = str_replace('.part' . $part, '', $filename);
                Storage::move('backups/' . $filename, 'backups/' . $newfilename);

                $filename = $newfilename;
            }

            array_push($downloadFiles, $filename);
        }

        return $downloadFiles;
    }

    public function archivo($file)
    {
        $files = Storage::files('backups/');

        if (in_array('backups/' . $file, $files)) {
            $unit = 'bytes';
            $filesize = Storage::size('backups/' . $file);
            $path = storage_path('app/backups/' . $file);

            $headers = [
                'Accept-Ranges' => $unit,
                'Content-Length' => $filesize
            ];

            return response()->download($path, $file, $headers);
        }
    }
}
