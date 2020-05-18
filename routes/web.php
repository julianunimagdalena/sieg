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
    $ws = new WSAdmisiones();
    // return $ws->getInformacionGraduadoByCodigo('2014114136');
    return $ws->getInformacionGraduadoByDocumentoIdentidad('1083029383');
    return $ws->getInfoEstudianteByCodigo('2014114136');
    return [$ws->getListaGraduadoByFechas('2019-07-13', '2019-07-14')];
    return [$ws->getInfoEstudianteByCodigo('2014114136')[0], $ws->getInformacionGraduadoByDocumentoIdentidad('1083029383')[0]];
});

Route::get('/session-data', 'CustomLoginController@sessionData');
Route::get('/logout', 'CustomLoginController@logout');
Route::post('/autenticar', 'CustomLoginController@autenticar');

Route::get('/programas-por-identificacion/{identificacion}', 'SolicitudGradoController@programasPorIdentificacion');
Route::post('/solicitar-grado', 'SolicitudGradoController@solicitar');
Route::get('/solicitud-grado/pendientes', 'SolicitudGradoController@pendientes');

Route::post('/dirprograma/activar-estudiante', 'DirProgramaController@activarEstudiante');

Route::get('/fechas-grado/activas', 'FechaGradoController@getFechasActivas');

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

Route::get('/egresado/datos', 'EstudianteController@datos');
Route::get('/egresado/datos-academicos', 'EstudianteController@datosAcademicos');
Route::get('/egresado/datos-hoja', 'EstudianteController@datosHoja');
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

Route::get('/direccion', 'DirProgramaController@index');
Route::get('/dirprograma/solicitudes', 'DirProgramaController@solicitudes');

//RUTAS VISTAS DE EGRESADO

Route::get('/egresado', 'EstudianteController@index');
Route::get('/egresado/ficha-egresado', 'EstudianteController@fichaEgresado');

//FIN

Route::get('/', function () {
    if (!Auth::check()) return view('login2');
    else return redirect(session('ur')->rol->home_egresados);
});
