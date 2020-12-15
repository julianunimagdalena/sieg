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

class ListadoAsistenciaCeremoniaExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
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
                $est->codigo,
                $dm->programa->nombre,
                $dm->facultad->nombre,
                $persona->nombre,
                $pg->confirmacion_asistencia ? 'Sí' : 'No',
                $pg->talla_camisa,
                $pg->estatura,
                $pg->tamano_birrete,
                $pg->num_acompaniantes,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Código',
            'Programa',
            'Facultad',
            'Nombre Completo',
            '¿Asiste a ceremonia?',
            'Talla de camisa',
            'Estatura (m)',
            'Tamaño del birrete',
            'Número de acompañantes',
        ];
    }
}
