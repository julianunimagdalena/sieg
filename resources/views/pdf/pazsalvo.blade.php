@extends('pdf.base')

@section('content')
<h5 class="text-center font-weight-bold">
    Paz y Salvos de Egresados
</h5>
<br>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th class="w-50">Código estudiante</th>
            <th>Número de documento</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{$estudiante->codigo}}</td>
            <td>{{$persona->identificacion}}</td>
        </tr>
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th class="w-50">Nombres</th>
            <th>Apellidos</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{$persona->nombres}}</td>
            <td>{{$persona->apellidos}}</td>
        </tr>
    </tbody>
</table>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th class="w-50">Facultad</th>
            <th>Programa</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{$estudiante->estudio->facultad->nombre}}</td>
            <td>{{$estudiante->estudio->programa->nombre}}</td>
        </tr>
    </tbody>
</table>
<br><br>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th>Dependencia</th>
            <th>Fecha de modificación</th>
            <th>Calificación</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($estudiante->estudiantePazSalvo as $eps)
        <tr>
            <td>{{$eps->pazSalvo->nombre_ucwords}}</td>
            <td>{{$eps->fecha}}</td>
            <td>{{$eps->paz_salvo ? 'APROBADO' : 'PENDIENTE'}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<br><br>
<p>
    Fecha de generacion del paz y salvo: <span class="font-weight-bold">{{$date}}</span>
</p>
@endsection
