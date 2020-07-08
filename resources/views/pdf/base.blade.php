<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{asset('css/principal.css')}}" rel="stylesheet">
    <style>
        /**
            Set the margins of the page to 0, so the footer and the header
            can be of the full height and width !
         **/
        @page {
            margin: 0cm 0cm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 3cm;
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 3.5cm;
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
        }

        /** Define the footer rules **/
        footer {
            position: fixed;
            bottom: 0.5cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
        }
    </style>
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
    <header>
        <img src="{{asset('img/header.JPG')}}" width="100%" height="100%" />
    </header>
    <footer>
        <img src="{{asset('img/footer.JPG')}}" width="100%" height="100%" />
    </footer>
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
