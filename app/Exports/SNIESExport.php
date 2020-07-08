<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class SNIESExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable, RegistersEventListeners;

    public function __construct($estudiantes)
    {
        $this->estudiantes = $estudiantes;
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->styleCells('A1:Y1', [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '004A87'
                ]
            ],
            'font' => [
                'color' => [
                    'rgb' => 'ffffff'
                ]
            ]
        ]);
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->estudiantes as $key => $est) {
            $persona = $est->persona;
            $pg = $est->procesoGrado;
            $dm = $est->estudio;

            array_push($data, [
                $key + 1,
                $persona->apellidos,
                $persona->nombres,
                $persona->nombre,
                $persona->tipoDocumento->abrv . ' N° ' . $persona->identificacion,
                $pg->codigo_ecaes,
                $persona->genero->nombre,
                $est->codigo,
                $pg->titulo_grado,
                $dm->programa->nombre,
                $dm->facultad->nombre,
                $pg->mejor_ecaes ? 'SI' : 'NO',
                $pg->mencion_honor ? 'SI' : 'NO',
                ($pg->incentivo_nacional ? 'SI' : 'NO') . ' APLICA',
                ($pg->incentivo_institucional ? 'SI' : 'NO') . ' APLICA',
                $dm->modalidad->nombre,
                $pg->modalidad_grado,
                $pg->titulo_memoria_grado,
                $pg->nota,
                $pg->tutor_grado,
                $pg->tipoVinculacionTutor->nombre,
                $persona->direccion,
                $persona->telefono_fijo,
                $persona->celular,
                $persona->correo
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Apellidos',
            'Nombres',
            'Nombre Completo',
            'Identificación', // C.C. N° xxxxxxx
            'Identificación Prueba a Saber Pro',
            'Género',
            'Código',
            'Título',
            'Programa',
            'Facultad',
            'Mejor Resultado Saber Pro (SI / NO)', // SI|NO
            '¿Aplica a mención de honor entregada en ceremonia de grado por resultado'
                . ' en las pruebas Saber Pro? (Acuerdo Superior N° 19 de 2017)', // SI|NO
            'Incentivos por mejor resultado Saber Pro a nivel nacional', // SI APLICA|NO APLICA
            'Incentivos por mejor resultado Saber Pro a nivel institucional', // SI APLICA|NO APLICA
            'Modalidad',
            'Modalidad Trabajo de Grado',
            'Titulo de Trabajo de Grado',
            'Calificación Trabajo de Grado',
            'Nombre del Director de la Modalidad de Grado',
            'Tipo de Vinculación',
            'Direccion',
            'Teléfono Fijo',
            'Teléfono Movil',
            'Correo Electrónico'
        ];
    }
}
