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
Route::get('/recursos/discapacidades', 'RecursosController@discapacidades');
Route::get('/recursos/consejos', 'RecursosController@consejos');
Route::get('/recursos/roles', 'RecursosController@roles');
Route::get('/recursos/programas', 'RecursosController@programas');
Route::get('/recursos/niveles-estudio', 'RecursosController@nivelesEstudio');

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

Route::get('/documento/ver/{ed_id}', 'DocumentoController@ver');

Route::get('/administrador/usuarios', 'AdminController@usuarios');
Route::post('/administrador/usuario', 'AdminController@usuario');
Route::post('/administrador/eliminar-usuario', 'AdminController@eliminarUsuario');
Route::get('/administrador/datos-usuario', 'AdminController@datosUsuario');

//RUTAS VISTAS DIRECCION DE PROGRAMA

Route::get('/direccion', 'DirProgramaController@index');
Route::get('/dirprograma/solicitudes', 'DirProgramaController@solicitudes');

//RUTAS VISTAS DE EGRESADO

Route::get('/egresado', 'EstudianteController@index');
Route::get('/egresado/ficha-egresado', 'EstudianteController@fichaEgresado');
Route::get('/egresado/carga-documentos', 'EstudianteController@cargaDocumentos');

//RUTAS VISTA ADMIN

Route::get('/administrador', 'AdminController@index');
Route::get('/administrador/administrar-usuarios', 'AdminController@administrarUsuarios');

Route::get('/', function () {
    if (!Auth::check()) return view('login2');
    else return redirect(session('ur')->rol->home_egresados);
});
