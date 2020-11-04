<?php

use App\Tools\WSFoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

Route::get('/prueba-documento/{html?}', function ($html = false) {
    $ed = App\Models\EstudianteDocumento::find(10029);
    return App\Tools\DocumentoHelper::actualizarDocumentoIdentidad($ed, true);
});

Route::get('/prueba-ws/{identificacion}', function ($identificacion) {
    // $est = App\Models\Estudiante::find(27300);
    // return $est->documentos_iniciales;
    $ws = new App\Tools\WSAdmisiones();
    return $ws->getInformacionGraduadoByDocumentoIdentidad($identificacion);
});

Route::get('/prueba-siare/{codigo}', function ($codigo) {
    $ws = new App\Tools\WSSiare();
    return [$ws->ConsultarPazySalvo($codigo)];
});

Route::get('/prueba', function () {
    $split = str_split(str_split('201424136', 5)[0], 4);
    return $split[0] . '-' . ($split[1] === '1' ? 'I' : 'II');
    return DB::connection('carnetizacion')->table('usuarios')->get();
});

Route::get('/session-data', 'CustomLoginController@sessionData');
Route::get('/logout', 'CustomLoginController@logout');
Route::post('/autenticar', 'CustomLoginController@autenticar');

Route::get('/programas-por-identificacion/{identificacion}', 'SolicitudGradoController@programasPorIdentificacion');
Route::post('/solicitar-grado', 'SolicitudGradoController@solicitar');
Route::get('/solicitud-grado/pendientes', 'SolicitudGradoController@pendientes');
Route::get('/solicitud-grado/numero-solicitudes', 'SolicitudGradoController@getNumeroSolicitudes');
Route::post('/solicitud-grado/eliminar', 'SolicitudGradoController@eliminar');

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
Route::get('/recursos/sectores-empresa', 'RecursosController@sectoresEmpresa');
Route::get('/recursos/sectores-economicos', 'RecursosController@sectoresEconomicos');
Route::get('/recursos/actividades-economicas', 'RecursosController@actividadesEconomicas');
Route::get('/recursos/areas-desempeno', 'RecursosController@areasDesempeno');
Route::get('/recursos/paz-salvos', 'RecursosController@pazSalvos');
Route::get('/recursos/facultades', 'RecursosController@facultades');
Route::get('/recursos/modalidades-estudio', 'RecursosController@modalidadesEstudio');
Route::get('/recursos/jornadas', 'RecursosController@jornadas');
Route::get('/recursos/documentos', 'RecursosController@documentos');
Route::get('/recursos/distinciones-estudiante', 'RecursosController@distincionesEstudiante');

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
Route::post('/egresado/cargar-foto', 'EstudianteController@cargarFoto');
Route::get('/egresado/foto', 'EstudianteController@datosFoto');
Route::post('/egresado/validar-foto', 'EstudianteController@validarFoto');

// PETICIONES DIRECCION DE PROGRAMA
Route::post('/dirprograma/activar-estudiante', 'DirProgramaController@activarEstudiante');
Route::get('/direccion/programas-coordinados', 'DirProgramaController@programasCoordinados');
Route::post('/direccion/obtener-estudiantes', 'DirProgramaController@obtenerEstudiantes');
Route::get('/direccion/proceso-grado/{estudiante_id}', 'DirProgramaController@procesoGrado');
Route::get('/direccion/datos-estudiante/{estudiante_id}', 'DirProgramaController@datosEstudiante');
Route::get('/direccion/documentos-estudiante/{estudiante_id}', 'DirProgramaController@documentosEstudiante');
Route::get('/direccion/generar/{ed_id}', 'DirProgramaController@generarDocumento');
Route::post('/direccion/aprobar-documento', 'DirProgramaController@aprobarDocumento');
Route::post('/direccion/rechazar-documento', 'DirProgramaController@rechazarDocumento');
Route::post('/direccion/aprobar', 'DirProgramaController@aprobarEstudiante');
Route::post('/direccion/no-aprobar', 'DirProgramaController@noAprobarEstudiante');
Route::get('/direccion/actualizar-estudiante/{estudiante_id}', 'DirProgramaController@actualizarEstudiante');
Route::get('/direccion/info-adicional-estudiante/{estudiante_id}', 'DirProgramaController@getInfoAdicionalEstudiante');
Route::post('/direccion/info-adicional-estudiante', 'DirProgramaController@infoAdicionalEstudiante');
Route::post('/direccion/actualizar-paz-salvos', 'DirProgramaController@actualizarPazSalvos');

// PETICIONES DOCUMENTO
Route::get('/documento/ver/{ed_id}', 'DocumentoController@ver');
Route::post('/documento/cargar', 'DocumentoController@cargar');

// PETICIONES BACKUP
Route::post('/backup/estudiantes', 'BackupController@estudiantes');
Route::get('/backup/archivo/{file}', 'BackupController@archivo');

// PETICIONES ADMIN
Route::get('/administrador/usuarios', 'AdminController@usuarios');
Route::post('/administrador/usuario', 'AdminController@usuario');
Route::post('/administrador/eliminar-usuario', 'AdminController@eliminarUsuario');
Route::get('/administrador/datos-usuario', 'AdminController@datosUsuario');
Route::get('/administrador/fecha-grado/{fecha_grado_id}', 'AdminController@fechaGrado');
Route::post('/administrador/fecha-grado', 'AdminController@editarFechaGrado');
Route::post('/administrador/eliminar-fecha-grado', 'AdminController@eliminarFechaGrado');
Route::get('/administrador/info-programa/{programa_id}', 'AdminController@infoPrograma');
Route::post('/administrador/carga-ecaes', 'AdminController@cargaEcaes');
Route::post('/administrador/carga-titulo-grado', 'AdminController@cargaTituloGrado');
Route::post('/administrador/diligencia-encuesta', 'AdminController@diligenciaEncuesta');
Route::post('/administrador/paz-salvo', 'AdminController@nuevoPazSalvo');
Route::post('/administrador/borrar-paz-salvo', 'AdminController@borrarPazSalvo');
Route::post('/administrador/documento', 'AdminController@nuevoDocumento');
Route::post('/administrador/borrar-documento', 'AdminController@borrarDocumento');
Route::post('/administrador/registrar-programa', 'AdminController@registrarPrograma');
Route::post('/administrador/graduados', 'AdminController@obtenerGraduados');
Route::post('/administrador/registrar-graduados', 'AdminController@registrarGraduados');
Route::get('/administrador/consultar-graduado', 'AdminController@consultarGraduado');
Route::get('/administrador/consultar-graduado-programas', 'AdminController@consultarGraduadoProgramas');
Route::post('/administrador/update-graduado', 'AdminController@updateGraduado');

// PETICIONES SEC GENERAL
Route::post('/secgeneral/estudiantes', 'SecretariaGeneralController@obtenerEstudiantes');
Route::get('/secgeneral/generar-snies', 'SecretariaGeneralController@generarSnies');

// PETICIONES MIGRACION
Route::get('/migracion/estudiantes', 'MigracionController@migrarEstudiantes');

//RUTAS VISTAS DIRECCION DE PROGRAMA

Route::get('/direccion', 'DirProgramaController@index');
Route::get('/direccion/estudiante/{estudiante_id}', 'DirProgramaController@estudiante');
Route::get('/direccion/solicitudes', 'DirProgramaController@solicitudes');
Route::get('/direccion/estudiantes', 'DirProgramaController@estudiantes');

//RUTAS VISTAS DE EGRESADO

Route::get('/egresado', 'EstudianteController@index');
Route::get('/egresado/ficha-egresado', 'EstudianteController@fichaEgresado');
Route::get('/egresado/carga-documentos', 'EstudianteController@cargaDocumentos');

//RUTAS VISTAS ADMIN

Route::get('/administrador', 'AdminController@index');
Route::get('/administrador/administrar-usuarios', 'AdminController@administrarUsuarios');
Route::get('/administrador/fechas-grado', 'AdminController@fechasGrado');
Route::get('/administrador/estudiantes', 'AdminController@estudiantes');
Route::get('/administrador/programas', 'AdminController@configuracionProgramas');
Route::get('/administrador/graduados', 'AdminController@graduados');
Route::get('/administrador/graduado/{estudiante_id}', 'AdminController@graduado');
Route::get('/administrador/registrar-graduado', 'AdminController@registrarGraduado');

// RUTAS VISTAS SECRETARIA GENERAL
Route::get('/secgeneral', 'SecretariaGeneralController@index');
Route::get('/secgeneral/estudiantes', 'SecretariaGeneralController@estudiantes');
Route::get('/secgeneral/aprobados', 'SecretariaGeneralController@vistaAprobados');

Route::get('/', function () {
    if (!Auth::check()) return view('login2');
    else return redirect(session('ur')->rol->home_egresados);
});
