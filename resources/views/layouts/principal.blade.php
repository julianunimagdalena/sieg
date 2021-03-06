<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="root" content="{{Request::root()}}">
    <title>@yield('title') - Sistema de Información para Egresados y Graduados</title>

    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.21/datatables.min.css" />
    <link href="{{asset('css/principal.css')}}" rel="stylesheet">
    <link href="{{asset('css/app-principal.css')}}" rel="stylesheet">
    <link href="{{asset('css/spin.css')}}" rel="stylesheet">

    <style>
        .form-group label {
            font-weight: bold;
        }

        .app-text-black-0 {
            color: black;
        }

        .app-text-black-1 {
            color: #2d3436;
        }

        table {
            text-align: center
        }

        .table-hover tbody tr:hover {
            color: black;
        }

        thead {
            background-color: var(--primary);
            color: var(--white);
        }

        tbody {
            color: black;
        }

        .badge.PENDIENTE {
            color: var(--white);
            background-color: var(--warning);
        }

        .badge.APROBADO {
            color: var(--white);
            background-color: var(--success);
        }

        .badge.RECHAZADO {
            color: var(--white);
            background-color: var(--danger);
        }

        .badge.SIN.CARGAR {
            color: var(--white);
            background-color: var(--secondary);
        }

        .badge-wrapper {
            position: relative;
        }

        .badge-icon-notify {
            position: absolute !important;
            top: -10px;
            right: -5px;
            display: inline-block;
            width: 15px;
            height: 15px;
            border-radius: 50%;
        }
    </style>
    @yield('css')
    @stack('csscomponent')
</head>

<body>
    <div>
        <div id="wrapper">
            <!-- Sidebar -->
            <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
                <!-- Sidebar - Brand -->
                <a class="sidebar-brand d-flex align-items-center position-relative" href @click.prevent>
                    <img src="{{asset('logo.png')}}" width="50" alt="Escudo de la universidad" />
                    <div class="mx-3 d-sm-none d-md-block">Saeg</div>
                </a>

                <!-- Divider -->
                <hr class="sidebar-divider my-0" />

                <!-- Nav Item - Dashboard -->
                <li class="nav-item active">
                    <a class="nav-link" href="{{Request::root().session('ur')->rol->home_egresados}}">
                        <i class="fas fa-fw fa-home"></i>
                        <span>Inicio</span>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider" />

                <!-- Heading -->
                <div class="sidebar-heading">Opciones</div>

                @if (session('ur')->rol->nombre === 'Coordinador de programa')
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/direccion/estudiantes">
                        <i class=" fas fa-fw fa-users"></i>
                        <span>Aspirantes a grado</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/direccion/solicitudes">
                        <i class=" fas fa-fw fa-file-alt badge-wrapper">
                            <span class="badge badge-danger font-weight-bold badge-icon-notify"
                                id="numero-de-solicitudes">
                                {{session('ur')->solicitudes_grado_pendientes}}
                            </span>
                        </i>

                        <span>Solicitudes de grado </span>
                    </a>
                </li>
                @endif


                @if (session('ur')->rol->nombre === 'Estudiante')
                <style>
                    .nav-item span {
                        font-size: .8rem !important;
                    }
                </style>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/egresado">
                        <i class="fas fa-fw fa-graduation-cap"></i>
                        <span>Proceso de grado</span>
                    </a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/egresado/encuesta">
                        <i class="fas fa-file-invoice"></i>
                        <span>Encuesta momento de grado</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/egresado/ficha-egresado">
                        <i class="fas fa-fw fa-poll-h"></i>
                        <span>Ficha de egresado</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/egresado/carga-documentos">
                        <i class="fas fa-fw fa-file-alt"></i>
                        <span>Carga de documentos</span>
                    </a>
                </li>-->
                @endif

                @if (session('ur')->rol->nombre === 'Administrador Egresados')
                <style>
                    .nav-item span {
                        font-size: .8rem !important;
                    }
                </style>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/administrador/administrar-usuarios">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Administrar usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/administrador/fechas-grado">
                        <i class="fas fa-fw fa-calendar-day"></i>
                        <span>Administrar fechas de grado</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/administrador/estudiantes">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Ver Estudiantes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/administrador/programas">
                        <i class="fas fa-university"></i>
                        <span>Configuración de Programas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/administrador/graduados">
                        <i class="fas fa-user-graduate"></i>
                        <span>Graduados</span>
                    </a>
                </li>
                @endif

                @if (session('ur')->rol->nombre === 'Secretaría General')
                <style>
                    .nav-item span {
                        font-size: .8rem !important;
                    }
                </style>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/secgeneral/estudiantes">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Estudiantes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{Request::root()}}/secgeneral/aprobados">
                        <i class="fas fa-fw fa-calendar-day"></i>
                        <span>Estudiantes aprobados</span>
                    </a>
                </li>
                @endif

                <!-- Nav Item - Pages Collapse Menu -->
                <!-- <li class="nav-item">
                    <a
                        class="nav-link collapsed"
                        href="#"
                        data-toggle="collapse"
                        data-target="#collapseTwo"
                        aria-expanded="true"
                        aria-controls="collapseTwo"
                    >
                        <i class="fas fa-fw fa-cog"></i>
                        <span>Components</span>
                    </a>
                    <div
                        id="collapseTwo"
                        class="collapse"
                        aria-labelledby="headingTwo"
                        data-parent="#accordionSidebar"
                    >
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Custom Components:</h6>
                            <a class="collapse-item" href="buttons.html">Buttons</a>
                            <a class="collapse-item" href="cards.html">Cards</a>
                        </div>
                    </div>
                </li>-->

                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block" />

                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>
            </ul>
            <!-- End of Sidebar -->

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">
                <!-- Main Content -->
                <div id="content">
                    <!-- Topbar -->
                    <nav class="navbar navbar-expand navbar-light bg-primary topbar mb-4 static-top shadow">
                        <!-- Sidebar Toggle (Topbar) -->
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3 text-white">
                            <i class="fa fa-bars"></i>
                        </button>

                        <!-- Topbar Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                            <li class="nav-item dropdown no-arrow d-sm-none">
                                <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-search fa-fw"></i>
                                </a>
                                <!-- Dropdown - Messages -->
                                <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                    aria-labelledby="searchDropdown">
                                    <form class="form-inline mr-auto w-100 navbar-search">
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-light border-0 small"
                                                placeholder="Search for..." aria-label="Search"
                                                aria-describedby="basic-addon2" />
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button">
                                                    <i class="fas fa-search fa-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </li>

                            <!-- Nav Item - User Information -->
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span
                                        class="mr-2 d-none d-md-inline text-white small">{{Auth::user()->persona->nombre}}</span>
                                    <!-- <img
                                        class="img-profile rounded-circle"
                                        src="https://source.unsplash.com/QAB-WJcbgJk/60x60"
                                    />-->
                                    <i class="fas fa-user"></i>
                                </a>
                                <!-- Dropdown - User Information -->
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Cerrar sesión
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>
                    <!-- End of Topbar -->

                    <!-- Begin Page Content -->
                    <div class="container-fluid" id="app">
                        @yield('content')
                        <!-- <slot /> -->
                    </div>
                    <!-- /.container-fluid -->
                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                <footer class="sticky-footer bg-gray-300 p-4">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>© Copyright 2020 Universidad del Magdalena ® Todos los Derechos Reservados - Centro de
                                Egresados</span>
                        </div>
                    </div>
                </footer>
                <!-- End of Footer -->
            </div>
            <!-- End of Content Wrapper -->
        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content rounded">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="exampleModalLabel">¿Desea salir del aplicativo?</h5>
                        <button class="close text-white" type="button" data-dismiss="modal"
                            aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">Seleccione "Cerrar sesión" para salir del aplicativo.</div>
                    <div class="modal-footer">
                        <button class="btn btn-light" type="button" data-dismiss="modal">Cancelar</button>
                        <a class="btn btn-primary" href="{{Request::root().'/logout'}}">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PAGE SCRIPTS --}}
    <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>
    <script src="{{asset('js/sb-admin-2.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/datatables.min.js"></script>

    {{-- OWN SCRIPTS --}}
    <script src="{{asset('vendor/vue.js')}}"></script>
    <script src="{{asset('vendor/sweetalert.min.js')}}"></script>
    <script src="{{asset('js/default.js')}}"></script>
    <script src="{{asset('vendor/axios.min.js')}}"></script>
    <script>
        $(`a[href='${window.location.href}']`).parent().addClass('active');
        document.querySelector('#sidebarToggle').click();
    </script>
    @stack('components')
    @stack('scripts')
</body>

</html>
