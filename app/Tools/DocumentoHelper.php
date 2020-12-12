<?php

namespace App\Tools;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DocumentoHelper
{
    static public function generarPazSalvo($ed, $prueba = false, $html = false)
    {
        $pdf = app('dompdf.wrapper');
        $estudiante = $ed->estudiante;

        $persona = $estudiante->persona;
        $date = Carbon::now()->format('d/m/Y g:i:s A');
        $pdf->loadView('pdf.pazsalvo', compact('ed', 'persona', 'estudiante', 'date'));

        if ($prueba) {
            if ($html) return view('pdf.pazsalvo', compact('ed', 'persona', 'estudiante', 'date'));
            return $pdf->stream();
        }

        Storage::makeDirectory($ed->folder);
        $pdf->save(storage_path('app/' . $ed->path));

        return true;
    }

    static public function generarFicha($ed, $prueba = false, $html = false)
    {
        $pdf = app('dompdf.wrapper');
        $estudiante = $ed->estudiante;
        $dm = $estudiante->estudio;
        $persona = $estudiante->persona;
        $hoja = $persona->hojaVida;
        $date = Carbon::now()->format('d/m/Y g:i:s A');
        $pdf->loadView('pdf.ficha', compact('ed', 'date', 'persona', 'hoja', 'estudiante', 'dm'));

        if ($prueba) {
            if ($html) return view('pdf.ficha', compact('ed', 'date', 'persona', 'hoja', 'estudiante', 'dm'));
            return $pdf->stream();
        }

        Storage::makeDirectory($ed->folder);
        $pdf->save(storage_path('app/' . $ed->path));

        return true;
    }

    static public function generarAdmisiones($ed)
    {
        return false;
    }

    static public function actualizarDocumentoIdentidad($ed, $prueba = false)
    {
        $contents = null;
        $ws = new WSAdmisiones();
        $pdf = app('dompdf.wrapper');

        try {
            $contents = $ws->fetchDocumentoIdentidad($ed->estudiante->codigo);
        } catch (\Throwable $th) {
            return false;
        }

        $filename = 'temp/' . Carbon::now()->timestamp . '.jpg';

        file_put_contents(public_path($filename), $contents);
        Storage::makeDirectory($ed->folder);

        $pdf->loadView('pdf.blank', compact('filename'));

        if ($prueba) {
            return $pdf->stream();
        }

        $pdf->save(storage_path('app/' . $ed->path));
        Storage::disk('public_dir')->delete($filename);

        return true;
    }
}
