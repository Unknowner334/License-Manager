<header class="header">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm align-middle">
        <div class="container px-3">
            <a class="navbar-brand" href="/"><i class="bi bi-box px-2"></i> {{ config('app.name') }} </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 px-2">
                    <li class="nav-item">
                        <a class="nav-link text-white" href=""><i class="bi bi-key"></i> Keys</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href={{ route('apps') }}><i class="bi bi-terminal"></i> Apps</a>
                    </li>
                </ul>
                <div class="float-right">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle pe-2"></i>{{ auth()->user()->name }}</a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start" aria-labelledby="navbarDropdown">
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                    <li class="dropdown-item text-muted">{{ auth()->user()->name }} ({{ auth()->user()->username }})</li>
                                    <li>
                                        <a class="dropdown-item" href="">
                                            <i class="bi bi-person"></i> Manage Users
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="">
                                            <i class="bi bi-person"></i> Manage Referrable Code
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href={{ route('settings') }}>
                                            <i class="bi bi-gear"></i> Settings
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                                            @csrf
                                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#confirmLogoutModal"><i class="bi bi-box-arrow-in-left"></i> Logout</button>
                                        </form>
                                    </li>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>    
        </div>
    </nav>
</header>