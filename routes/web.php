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

Route::get('/estudiante/datos', 'EstudianteController@datos');
Route::get('/estudiante/datos-academicos', 'EstudianteController@datosAcademicos');
Route::get('/estudiante/datos-hoja', 'EstudianteController@datosHoja');
Route::get('/estudiante/datos-laborales', 'EstudianteController@datosLaborales');

Route::get('/vista/{path?}', function ($req, $path = '') {
    $root = env('APP_ENV') === 'local' ? '/sieg/public/' : '/';
    $template = Auth::check() ? 'welcome' : 'login';

    return view($template, compact('root'));
})->where('path', '(.*?)');

Route::get('/direccion', 'DirProgramaController@index');
Route::get('/dirprograma/solicitudes', 'DirProgramaController@solicitudes');

Route::get('/', function () {
    if (!Auth::check()) return view('login2');
    else return redirect(session('ur')->rol->home_egresados);
});
