<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />

    <title>DOCSTORE</title>

    <link href="{{ URL::to('template/css/app.css') }}" rel="stylesheet">
    <link href="{{ URL::to('template/css/fonts.googleapis.css') }}" rel="stylesheet">
</head>

<body data-theme="light" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="">
                    <span class="align-middle">DOCSTORE</span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-header">
                        Gestion des immatriculations
                    </li>

                    <li class="{{ request()->is('supervisor') ? 'sidebar-item active' : 'sidebar-item' }}">
                        <a class="sidebar-link" href="{{ route('supervisor.home') }}">
                            <i class="align-middle" data-feather="chevron-right"></i> <span class="align-middle">Tableau de bord</span>
                        </a>
                    </li>
                </ul>

            </div>
        </nav>

        <div class="main">
        <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">

                        </li>
                        <li class="nav-item">
                            <span class="nav-link d-none d-sm-inline-block">

                            </span>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('supervisor.user.edit', encrypt(Auth::user()->id)) }}"><i class="align-middle me-1" data-feather="user"></i> Mon Compte</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('auth.logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="align-middle me-1" data-feather="log-out"></i> Déconnexion</a>
                                <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="content">
                <div class="container-fluid p-0" style="font-family: JetBrains Mono;">
                    @yield('content')
                </div>
            </main>

            <footer class="footer">
                <div class="container-fluid">
                <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <a class="text-muted" href="" target=""><strong>Archivage numérique des documents/ MTFPDS-DNFPP</strong></a> &copy; 2024
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a class="text-muted" href="" target="_blank">Contact :</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="" target="_blank">wim@gmail.com</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="" target="_blank">(+223) 76 61 36 05</a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="text-muted" href="" target="_blank">(+223) 66 61 36 05</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ URL::to('template/js/app.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Choices.js
            new Choices(document.querySelector(".choices-single0"));
            new Choices(document.querySelector(".choices-single1"));
            new Choices(document.querySelector(".choices-single2"));
            new Choices(document.querySelector(".choices-single3"));
            new Choices(document.querySelector(".choices-single4"));
            new Choices(document.querySelector(".choices-single5"));
            new Choices(document.querySelector(".choices-single6"));
            new Choices(document.querySelector(".choices-single7"));
            new Choices(document.querySelector(".choices-single8"));
            new Choices(document.querySelector(".choices-single9"));
        });
    </script>
</body>

</html>