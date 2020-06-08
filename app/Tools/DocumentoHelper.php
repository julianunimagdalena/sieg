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
}
