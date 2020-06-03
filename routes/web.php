<?php

use App\Models\User;
use App\Tools\WSAdmisiones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/prueba', function () {
    return session('estudiante_id');
    $ws = new WSAdmisiones();
    $codigo = '2013243078';
    $documento = '1082999764';

    return [
        'getInfoEstudianteByCodigo' => $ws->getInfoEstudianteByCodigo($codigo),
        'getInformacionGraduadoByCodigo' => $ws->getInformacionGraduadoByCodigo($codigo),
        'getInformacionGraduadoByDocumentoIdentidad' => $ws->getInformacionGraduadoByDocumentoIdentidad($documento),
    ];
});

Route::get('/session-data', 'CustomLoginController@sessionData');
Route::get('/logout', 'CustomLoginController@logout');
Route::post('/autenticar', 'CustomLoginController@autenticar');

Route::get('/programas-por-identificacion/{identificacion}', 'SolicitudGradoController@programasPorIdentificacion');
Route::post('/solicitar-grado', 'SolicitudGradoController@solicitar');
Route::get('/solicitud-grado/pendientes', 'SolicitudGradoController@pendientes');

Route::get('/fechas-grado/activas', 'FechaGradoController@getFechasActivas');


// RECURSOS DEL SISTEMA

Route::get('/recursos/duraciones-laborales', 'RecursosController@duracionesLaborales');
Route::get('/recursos/idiomas', 'RecursosController@idiomas');
Route::get('/recursos/niveles-cargo', 'RecursosController@nivelesCargo');
Route::get('/recursos/niveles-idioma', 'RecursosController@nivelesIdioma');
Route::get('/recursos/paises', 'RecursosController@paises');
Route::get('/recursos/departamentos', 'RecursosController@departamentos');
Route::get('/recursos/municipios', 'RecursosController@municipios');
Route::get('/recursos/salarios', 'RecursosController@salarios');
Route::get('/recursos/tipos-vinculacion', 'RecursosController@tiposVinculacion');
Route::get('/recursos/generos', 'RecursosController@generos');
Route::get('/recursos/tipos-documento', 'RecursosController@tiposDocumento');
Route::get('/recursos/estados-civiles', 'RecursosController@estadosCiviles');
Route::get('/recursos/discapacidades', 'RecursosController@discapacidades');
Route::get('/recursos/consejos', 'RecursosController@consejos');
Route::get('/recursos/roles', 'RecursosController@roles');
Route::get('/recursos/programas', 'RecursosController@programas');
Route::get('/recursos/niveles-estudio', 'RecursosController@nivelesEstudio');
Route::get('/recursos/tipos-grado', 'RecursosController@tiposGrado');
Route::get('/recursos/fechas-grado', 'RecursosController@fechasGrado');

// PETICIONES DEL EGRESADO
Route::get('/egresado/datos', 'EstudianteController@datos');
Route::get('/egresado/datos-academicos', 'EstudianteController@datosAcademicos');
Route::get('/egresado/perfil', 'EstudianteController@perfil');
Route::get('/egresado/distinciones', 'EstudianteController@distinciones');
Route::get('/egresado/asociaciones', 'EstudianteController@asociaciones');
Route::get('/egresado/concejos', 'EstudianteController@concejos');
Route::get('/egresado/discapacidades', 'EstudianteController@discapacidades');
Route::get('/egresado/idiomas', 'EstudianteController@idiomas');
Route::get('/egresado/datos-laborales', 'EstudianteController@datosLaborales');
Route::post('/egresado/datos', 'EstudianteController@guardarDatosPersonales');
Route::post('/egresado/estudio', 'EstudianteController@guardarEstudio');
Route::post('/egresado/eliminar-estudio', 'EstudianteController@eliminarEstudio');
Route::post('/egresado/perfil-profesional', 'EstudianteController@editarPerfilProfesional');
Route::post('/egresado/distincion', 'EstudianteController@guardarDistincion');
Route::post('/egresado/eliminar-distincion', 'EstudianteController@eliminarDistincion');
Route::post('/egresado/asociacion', 'EstudianteController@guardarAsociacion');
Route::post('/egresado/eliminar-asociacion', 'EstudianteController@eliminarAsociacion');
Route::post('/egresado/concejo', 'EstudianteController@guardarConcejo');
Route::post('/egresado/eliminar-concejo', 'EstudianteController@eliminarConcejo');
Route::post('/egresado/agregar-discapacidad', 'EstudianteController@agregarDiscapacidad');
Route::post('/egresado/eliminar-discapacidad', 'EstudianteController@eliminarDiscapacidad');
Route::post('/egresado/idioma', 'EstudianteController@guardarIdioma');
Route::post('/egresado/eliminar-idioma', 'EstudianteController@eliminarIdioma');
Route::post('/egresado/actualidad-laboral', 'EstudianteController@guardarActualidadLaboral');
Route::post('/egresado/experiencia-laboral', 'EstudianteController@guardarExperiencia');
Route::post('/egresado/eliminar-experiencia-laboral', 'EstudianteController@eliminarExperiencia');
Route::get('/egresado/progreso-ficha', 'EstudianteController@progresoFicha');
Route::get('/egresado/info-grado', 'EstudianteController@infoGrado');
Route::get('/egresado/documentos-grado', 'EstudianteController@documentosGrado');
Route::post('/egresado/cargar-documento', 'EstudianteController@cargarDocumento');
Route::get('/egresado/info-asistencia-ceremonia/{codigo}', 'EstudianteController@infoAsistenciaCeremonia');
Route::post('/egresado/asistencia-ceremonia', 'EstudianteController@guardarAsistenciaCeremonia');

// PETICIONES DIRECCION DE PROGRAMA
Route::post('/dirprograma/activar-estudiante', 'DirProgramaController@activarEstudiante');
Route::get('/direccion/programas-coordinados', 'DirProgramaController@programasCoordinados');
Route::post('/direccion/obtener-estudiantes', 'DirProgramaController@obtenerEstudiantes');
Route::get('/direccion/proceso-grado/{estudiante_id}', 'DirProgramaController@procesoGrado');
Route::get('/direccion/datos-estudiante/{estudiante_id}', 'DirProgramaController@datosEstudiante');
Route::get('/direccion/documentos-estudiante/{estudiante_id}', 'DirProgramaController@documentosEstudiante');

// PETICIONES DOCUMENTO
Route::get('/documento/ver/{ed_id}', 'DocumentoController@ver');

// PETICIONES ADMIN
Route::get('/administrador/usuarios', 'AdminController@usuarios');
Route::post('/administrador/usuario', 'AdminController@usuario');
Route::post('/administrador/eliminar-usuario', 'AdminController@eliminarUsuario');
Route::get('/administrador/datos-usuario', 'AdminController@datosUsuario');
Route::get('/administrador/fecha-grado/{fecha_grado_id}', 'AdminController@fechaGrado');
Route::post('/administrador/fecha-grado', 'AdminController@editarFechaGrado');

//RUTAS VISTAS DIRECCION DE PROGRAMA

Route::get('/direccion', 'DirProgramaController@index');
Route::get('/direccion/estudiante/{estudiante_id}', 'DirProgramaController@estudiante');
Route::get('/direccion/solicitudes', 'DirProgramaController@solicitudes');
Route::get('/direccion/estudiantes', 'DirProgramaController@estudiantes');

//RUTAS VISTAS DE EGRESADO

Route::get('/egresado', 'EstudianteController@index');
Route::get('/egresado/ficha-egresado', 'EstudianteController@fichaEgresado');
Route::get('/egresado/carga-documentos', 'EstudianteController@cargaDocumentos');

//RUTAS VISTA ADMIN

Route::get('/administrador', 'AdminController@index');
Route::get('/administrador/administrar-usuarios', 'AdminController@administrarUsuarios');
Route::get('/administrador/fechas-grado', 'AdminController@fechasGrado');

Route::get('/', function () {
    if (!Auth::check()) return view('login2');
    else return redirect(session('ur')->rol->home_egresados);
});
