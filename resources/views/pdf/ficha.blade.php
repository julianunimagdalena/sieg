@extends('pdf.base')

@section('content')
<h5 class="text-center font-weight-bold">
    Ficha de Egresados y/o Actualización de Datos
</h5>
<table class="w-100">
    <tr>
        <td style="width: 80%">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th colspan="2" style="background-color: #ddd">Datos básicos del estudiante</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Nombres:</th>
                        <th>Apellidos:</th>
                    </tr>
                    <tr>
                        <td>{{$persona->nombres}}</td>
                        <td>{{$persona->apellidos}}</td>
                    </tr>
                    <tr>
                        <th>Documento de identificación:</th>
                        <th>Número de identificación:</th>
                    </tr>
                    <tr>
                        <td>{{$persona->tipoDocumento->nombre}}</td>
                        <td>{{$persona->identificacion}}</td>
                    </tr>
                    <tr>
                        <th>Genero:</th>
                        <th>Estado civil:</th>
                    </tr>
                    <tr>
                        <td>{{$persona->genero->nombre}}</td>
                        <td>{{$persona->estadoCivil->nombre}}</td>
                    </tr>
                    <tr>
                        <th>Fecha de nacimiento:</th>
                        <th>Edad:</th>
                    </tr>
                    <tr>
                        <td>{{$persona->fecha_nacimiento_formated}}</td>
                        <td>{{$persona->edad}} años</td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td style="width: 20%">foto</td>
    </tr>
</table>
<br>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th colspan="3" style="background-color: #ddd">Datos básicos del estudiante (ubicación)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th style="width: 33%">Departamento de nacimiento:</th>
            <th style="width: 33%">Municipio de nacimiento:</th>
            <th>Número de teléfono fijo:</th>
        </tr>
        <tr>
            <td>{{$persona->municipioNacimiento->departamento->nombre}}</td>
            <td>{{$persona->municipioNacimiento->nombre}}</td>
            <td>{{$persona->telefono_fijo}}</td>
        </tr>
        <tr>
            <th>Direccion:</th>
            <th>Barrio:</th>
            <th>Estrato:</th>
        </tr>
        <tr>
            <td>{{$persona->direccion}}</td>
            <td>{{$persona->sector}}</td>
            <td>{{$persona->estrato}}</td>
        </tr>
        <tr>
            <th>Número de teléfono móvil:</th>
            <th>Correo electrónico 1:</th>
            <th>Correo electrónico 2:</th>
        </tr>
        <tr>
            <td>{{$persona->celular}}</td>
            <td>{{$persona->correo}}</td>
            <td>{{$persona->correo2}}</td>
        </tr>
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th colspan="1" style="background-color: #ddd">Perfil profesional</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{$hoja->perfil}}</td>
        </tr>
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th colspan="4" style="background-color: #ddd">
                Información académica del egresado en la Universidad del Magdalena
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Código</th>
            <th>Facultad</th>
            <th>Programa</th>
            <th>Modalidad</th>
        </tr>
        <tr>
            <td>{{$estudiante->codigo}}</td>
            <td>{{$dm->facultad->nombre}}</td>
            <td>{{$dm->programa->nombre}}</td>
            <td>{{$dm->modalidad->nombre}}</td>
        </tr>
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th colspan="4" style="background-color: #ddd">
                Información académica desarrollada en la Universidad del Magdalena
            </th>
        </tr>
    </thead>
    <tbody>
        @if ($persona->estudiantes()->graduados()->count() === 0)
        <tr>
            <td colspan="4">No posee información</td>
        </tr>
        @else
        <tr>
            <th>Código</th>
            <th>Facultad</th>
            <th>Programa</th>
            <th>Fecha de grado</th>
        </tr>
        @foreach ($persona->estudiantes()->graduados()->get() as $est)
        <tr>
            <td>{{$est->codigo}}</td>
            <td>{{$est->estudio->facultad->nombre}}</td>
            <td>{{$est->estudio->programa->nombre}}</td>
            <td>{{$est->procesoGrado->fechaGrado->fecha_formated}}</td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th colspan="5" style="background-color: #ddd">
                Información académica desarrollada en otras insticiones
            </th>
        </tr>
    </thead>
    <tbody>
        @if ($hoja->estudios()->count() === 0)
        <tr>
            <td colspan="5">No posee información</td>
        </tr>
        @else
        <tr>
            <th>Nombre del estudio</th>
            <th>Institución</th>
            <th>Meses cursados</th>
            <th>Graduado</th>
            <th>Año de finalización</th>
        </tr>
        @foreach ($hoja->estudios as $est)
        <tr>
            <td>{{$est->titulo}}</td>
            <td>{{$est->institucion}}</td>
            <td>{{$est->duracion}}</td>
            <td>{{$est->graduado ? 'SI' : 'NO'}}</td>
            <td>{{$est->anioGrado}}</td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th colspan="4" style="background-color: #ddd">
                Información de experiencias laborales
            </th>
        </tr>
    </thead>
    <tbody>
        @if ($hoja->experiencias()->count() === 0)
        <tr>
            <td colspan="4">No posee información</td>
        </tr>
        @else
        <tr>
            <th>Empresa</th>
            <th>Cargo</th>
            {{-- <th>Direccion</th> --}}
            <th>Telefono</th>
            <th>Activo</th>
        </tr>
        @foreach ($hoja->experiencias as $exp)
        <tr>
            <td>{{$exp->empresa}}</td>
            <td>{{$exp->cargo}}</td>
            <td>{{$exp->telefono}}</td>
            <td>{{$exp->contrato_activo ? 'SI' : 'NO'}}</td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th colspan="1" style="background-color: #ddd">
                Discapacidades
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($hoja->discapacidades as $dis)
        <tr>
            <td>{{$dis->nombre}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th colspan="1" style="background-color: #ddd">
                Listado de distinciones
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($hoja->distinciones as $dis)
        <tr>
            <td>{{$dis->nombre}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th colspan="1" style="background-color: #ddd">
                Listado de asociaciones a las que pertenece
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($hoja->asociaciones as $asc)
        <tr>
            <td>{{$asc->nombre}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th colspan="4" style="background-color: #ddd">
                Listado de idiomas
            </th>
        </tr>
    </thead>
    <tbody>
        @if ($hoja->idiomas()->count() === 0)
        <tr>
            <td colspan="4">No posee información</td>
        </tr>
        @else
        <tr>
            <th>Nombre</th>
            <th>Nivel de habla</th>
            <th>Nivel de escritura</th>
            <th>Nivel de lectura</th>
        </tr>
        @foreach ($hoja->idiomas as $hvi)
        <tr>
            <td>{{$hvi->idioma->nombre}}</td>
            <td>{{$hvi->nivelHabla->nombre}}</td>
            <td>{{$hvi->nivelEscritura->nombre}}</td>
            <td>{{$hvi->nivelLectura->nombre}}</td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
<br>
<p class="text-center" style="font-size: .8rem">
    Gracias, por aportar al proceso de Seguimiento a Graduados direccionado por el Viceministerio de Educación Superior.
    Debe hacer entrega de
    la Ficha de Inscripción de Graduados y/o Actualización de Datos en su Dirección de Programa o Centro de Formación.
</p>
@endsection
