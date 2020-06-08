<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{asset('css/principal.css')}}" rel="stylesheet">
    <style>
        * {
            font-size: .9rem;
        }

        table {
            color: black !important;
        }

        td,
        th {
            padding: 1px !important;
        }
    </style>
</head>

<body>
    <main>
        @yield('content')
    </main>
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(530, 815, "Pagina $PAGE_NUM de $PAGE_COUNT", $font, 7);
                $pdf->text(20, 815, "{{$date}}", $font, 7);
            ');
        }
    </script>
</body>

</html>
