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
    // dd($sheet->getDelegate());
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class EncuestaExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable, RegistersEventListeners;

    public function __construct($encuesta, $pges)
    {
        $this->encuesta = $encuesta;
        $this->pges = $pges;
        $this->preguntas = null;
    }

    public static function afterSheet(AfterSheet $event)
    {
        $worksheet = $event->sheet->getDelegate();
        $highestColumn = $worksheet->getHighestColumn();
        $range = 'A1:' . $highestColumn . '1';
        $event->sheet->styleCells($range, [
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

        foreach ($worksheet->getColumnDimensions() as $key => $dimension) {
            if (!in_array($key, ['A', 'B', 'C', 'D'])) {
                $dimension->setAutoSize(false);
                $dimension->setWidth(20);
            }
        }
    }

    public function preguntasEnOrden()
    {
        if ($this->preguntas) return $this->preguntas;

        $preguntas = $this->encuesta->preguntas
            ->map(function ($item) {
                $orden = $item->orden;

                if (strpos($orden, '.') !== false) {
                    $exploded = explode('.', $orden);
                    $decimals = $exploded[1];

                    if (strlen($decimals) === 1) {
                        $orden = "$exploded[0].0$decimals";
                    }
                }

                $item->orden = floatval($orden);
                return $item;
            })
            ->sortBy('orden');

        $this->preguntas = $preguntas;
        return $preguntas;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->pges as $pge) {
            $estudiante = $pge->procesoGrado->estudiante;
            $persona = $estudiante->persona;
            $rowdata = [
                $persona->nombre,
                $estudiante->codigo,
                $persona->identificacion,
                $estudiante->estudio->programa->nombre
            ];

            $respuestas = $pge->respuestas;
            foreach ($this->preguntasEnOrden() as $pregunta) {
                $respuesta = $respuestas->first(fn ($r) => $r->pregunta_id === $pregunta->id);
                array_push($rowdata, $respuesta ? $respuesta->texto : '');
            }

            array_push($data, $rowdata);
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            'Nombre del estudiante',
            'Código',
            'Identificación',
            'Programa'
        ];

        foreach ($this->preguntasEnOrden() as $pregunta) {
            array_push($headings, $pregunta->text);
        }

        return $headings;
    }
}
