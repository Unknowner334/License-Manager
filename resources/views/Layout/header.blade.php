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
                        <a class="nav-link text-white" href={{ route('licenses.index') }}><i class="bi bi-key"></i> Licenses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href={{ route('apps.index') }}><i class="bi bi-terminal"></i> Apps</a>
                    </li>
                </ul>
                <div class="float-right">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle pe-2"></i>{{ auth()->user()->name }}</a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ config('messages.settings.source_link') }}">
                                        <i class="bi bi-{{ strtolower(config('messages.settings.source')) }}"></i> {{ config('messages.settings.source') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li class="dropdown-item text-muted">{{ auth()->user()->name }} ({{ auth()->user()->username }})</li>
                                @if (auth()->user()->role != "Reseller")
                                    <li>
                                        <a class="dropdown-item" href={{ route('admin.users.index') }}>
                                            <i class="bi bi-person"></i> Manage Users
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href={{ route('admin.referrable.index') }}>
                                            <i class="bi bi-person-add"></i> Manage Referrable Code
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item" href={{ route('settings.index') }}>
                                        <i class="bi bi-gear"></i> Settings
                                    </a>
                                </li>
                                @if (auth()->user()->role != "Reseller")
                                    <li>
                                        <a class="dropdown-item" href={{ route('settings.webui.index') }}>
                                            <i class="bi bi-gear"></i> Web UI Settings
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger" id="logoutBtn"><i class="bi bi-box-arrow-in-left"></i> Logout</button>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>    
        </div>
    </nav>
</header>