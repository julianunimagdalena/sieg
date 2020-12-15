<!DOCTYPE html>
<html>

<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style type="text/css">
        img {
            max-width: 100%;
        }

        #wraper {
            max-width: 650px;
            min-height: 550px;
            background-color: #08357c;
            position: relative;
        }

        #header {
            min-height: 350px;
            border-bottom-right-radius: 30px;
            background-color: #a6ce39;
        }

        #izq,
        #der {
            display: inline-block;
            vertical-align: top;
            width: 30%;
        }

        #der {
            width: 60%;
        }

        #footer {
            position: absolute;
            bottom: 0;
        }

        #content {
            width: 80%;
            background-color: #c5da90;
            /*opacity: 0.5;*/
            min-height: 250px;
            position: absolute;
            left: 65px;
            padding: 15px;
        }

        #content span {
            color: black !important;
            font-size: 1.2em;
        }
    </style>
</head>

<body>


    <div id="wraper">
        <div id="header">
            <div id="izq">
                <center>
                    <img src="http://egresados.unimagdalena.edu.co/Content/images/escudo.png">
                </center>
            </div>
            <div id="der">
                <h4 style="margin-top:35px;">
                    Documentación Precontractuales - Universidad del Magdalena
                </h4>
            </div>
            <div id="content" style="color:black; margin: 0 auto;">
                <span style="text-align:left !important;">
                    Buen día <br>
                    <b>Sr(a). <?php echo $nombre; ?>.</b>
                    <br><br>
                    <?php echo $texto; ?>
                    <br><br>
                    Gracias por atender nuestro mensaje.
                </span>
            </div>
        </div>
        <div id="footer">
            <img src="http://egresados.unimagdalena.edu.co/Content/images/footer_correo.png">
        </div>
    </div>






    <script src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>

</html>
