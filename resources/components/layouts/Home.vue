<template>
    <div>
        <div id="wrapper">
            <!-- Sidebar -->
            <ul
                class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
                id="accordionSidebar"
            >
                <!-- Sidebar - Brand -->
                <a
                    class="sidebar-brand d-flex align-items-center position-relative"
                    href
                    @click.prevent
                >
                    <img :src="logo" width="50" alt="Escudo de la universidad" />
                    <div class="mx-3 d-sm-none d-md-block">Saeg</div>
                </a>

                <!-- Divider -->
                <hr class="sidebar-divider my-0" />

                <!-- Nav Item - Dashboard -->
                <li class="nav-item active">
                    <router-link :to="$attrs.path" class="nav-link">
                        <i class="fas fa-fw fa-home"></i>
                        <span>Inicio</span>
                    </router-link>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider" />

                <!-- Heading -->
                <div class="sidebar-heading">Opciones</div>

                <li class="nav-item" v-for="item in $attrs.links" :key="item.name">
                    <router-link :to="$attrs.path + '/' + item.path" class="nav-link">
                        <i :class="'fas fa-fw fa-' + item.icon"></i>
                        <span>{{item.name}}</span>
                    </router-link>
                </li>

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
                <div class="text-center d-none">
                    <button class="rounded-circle border-0" id="sidebarToggle2">
                        <i :class="'fas fa-chevron-' + (collapsed ? 'right' : 'left')"></i>
                    </button>
                </div>
            </ul>
            <!-- End of Sidebar -->

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">
                <!-- Main Content -->
                <div id="content">
                    <!-- Topbar -->
                    <nav
                        class="navbar navbar-expand navbar-light bg-primary topbar mb-4 static-top shadow"
                    >
                        <!-- Sidebar Toggle (Topbar) -->
                        <button
                            id="sidebarToggleTop"
                            class="btn btn-link d-md-none rounded-circle mr-3 text-white"
                        >
                            <i class="fa fa-bars"></i>
                        </button>

                        <!-- Topbar Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                            <li class="nav-item dropdown no-arrow d-sm-none">
                                <a
                                    class="nav-link dropdown-toggle"
                                    href="#"
                                    id="searchDropdown"
                                    role="button"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                >
                                    <i class="fas fa-search fa-fw"></i>
                                </a>
                                <!-- Dropdown - Messages -->
                                <div
                                    class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                    aria-labelledby="searchDropdown"
                                >
                                    <form class="form-inline mr-auto w-100 navbar-search">
                                        <div class="input-group">
                                            <input
                                                type="text"
                                                class="form-control bg-light border-0 small"
                                                placeholder="Search for..."
                                                aria-label="Search"
                                                aria-describedby="basic-addon2"
                                            />
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
                                <a
                                    class="nav-link dropdown-toggle"
                                    href="#"
                                    id="userDropdown"
                                    role="button"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                >
                                    <span
                                        class="mr-2 d-none d-md-inline text-white small"
                                    >{{userData.nombre}}</span>
                                    <!-- <img
                                        class="img-profile rounded-circle"
                                        src="https://source.unsplash.com/QAB-WJcbgJk/60x60"
                                    />-->
                                    <i class="fas fa-user"></i>
                                </a>
                                <!-- Dropdown - User Information -->
                                <div
                                    class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown"
                                >
                                    <a
                                        class="dropdown-item"
                                        href="#"
                                        data-toggle="modal"
                                        data-target="#logoutModal"
                                    >
                                        <i
                                            class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"
                                        ></i>
                                        Cerrar sesión
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>
                    <!-- End of Topbar -->

                    <!-- Begin Page Content -->
                    <div class="container-fluid" v-if="!iscargando">
                        <router-view :userData="userData" />
                        <!-- <slot /> -->
                    </div>
                    <!-- /.container-fluid -->
                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                <footer class="sticky-footer bg-gray-300 p-4">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>© Copyright 2020 Universidad del Magdalena ® Todos los Derechos Reservados - Centro de Egresados</span>
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
        <div
            class="modal fade"
            id="logoutModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content rounded">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="exampleModalLabel">¿Desea salir del aplicativo?</h5>
                        <button
                            class="close text-white"
                            type="button"
                            data-dismiss="modal"
                            aria-label="Close"
                        >x</button>
                    </div>
                    <div class="modal-body">Seleccione "Cerrar sesión" para salir del aplicativo.</div>
                    <div class="modal-footer">
                        <button class="btn btn-light" type="button" data-dismiss="modal">Cancelar</button>
                        <a class="btn btn-primary" href @click.prevent="logout">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import $ from "jquery";
import "startbootstrap-sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js";
import axios from "../../tools/http";

import logo from "../../img/logo.png";
import { baseURL } from "../../tools/variables";

export default {
    data: () => ({
        logo,
        userData: {},
        iscargando: true,
        collapsed: false
    }),
    computed: {
        homeIcon() {
            //@ts-ignore
            return this.$attrs.links.filter(lnk => lnk.path === "")[0];
        }
    },
    async created() {
        const res = await axios.get("session-data");

        this.userData = res.data.data;
        this.iscargando = false;
    },
    mounted() {
        this.setUp();
    },
    methods: {
        logout() {
            axios.get("/logout").then(res => (window.location.href = baseURL));
        },
        setUp() {
            const vm = this;
            $("#sidebarToggle2, #sidebarToggleTop").on("click", function(e) {
                vm.collapsed = !vm.collapsed;

                $("body").toggleClass("sidebar-toggled");
                $(".sidebar").toggleClass("toggled");
                if ($(".sidebar").hasClass("toggled")) {
                    $(".sidebar .collapse").collapse("hide");
                }
            });

            $("#sidebarToggle2").click();

            // Close any open menu accordions when window is resized below 768px
            $(window).resize(function() {
                if ($(window).width() < 768) {
                    $(".sidebar .collapse").collapse("hide");
                }
            });

            // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
            $("body.fixed-nav .sidebar").on(
                "mousewheel DOMMouseScroll wheel",
                function(e) {
                    if ($(window).width() > 768) {
                        var e0 = e.originalEvent,
                            delta = e0.wheelDelta || -e0.detail;
                        this.scrollTop += (delta < 0 ? 1 : -1) * 30;
                        e.preventDefault();
                    }
                }
            );

            // Scroll to top button appear
            $(document).on("scroll", function() {
                var scrollDistance = $(this).scrollTop();
                if (scrollDistance > 100) {
                    $(".scroll-to-top").fadeIn();
                } else {
                    $(".scroll-to-top").fadeOut();
                }
            });

            // Smooth scrolling using jQuery easing
            $(document).on("click", "a.scroll-to-top", function(e) {
                var $anchor = $(this);
                $("html, body")
                    .stop()
                    .animate(
                        {
                            scrollTop: $($anchor.attr("href")).offset().top
                        },
                        1000,
                        "easeInOutExpo"
                    );
                e.preventDefault();
            });
        }
    }
};
</script>

<style lang="scss">
$blue: #004a87;
$orange: #d17900;
$theme-colors: (
    "primary": $blue
);

// @import "~@fortawesome/fontawesome-free/scss/fontawesome.scss";
@import url("https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i");
@import "~startbootstrap-sb-admin-2/scss/sb-admin-2.scss";

svg {
    @extend i;
    cursor: pointer;
}

.router-link-active svg {
    color: #fff !important;
}

#sidebarToggle2 {
    background-color: rgba(255, 255, 255, 0.2);
    width: 2.5rem;
    height: 2.5rem;
    text-align: center;
    margin-bottom: 1rem;
    cursor: pointer;
    margin-right: 0.1rem;
    font-family: "Font Awesome 5 Free";
    color: #fff;
    outline: none;
}

.topbar {
    background-color: #ad34;
    border-bottom: 3px solid $orange;
}

#sidebarToggleTop {
    color: $white;
}

#sidebarToggleTop:hover {
    color: $blue !important;
}

body {
    color: #000;
}

.titulo {
    text-transform: uppercase;
    font-weight: 800;
}
</style>
