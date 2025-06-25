<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse border" id="navbarSupportedContent">
            @auth
                <ul class="navbar-nav me-auto">
                    @if (Auth()->id() == 1 || Auth()->id() == 2)
                        <li><a href="{{ url('admin/users') }}" class="btn btn-sm btn-flat m-1 btn-primary">Admin</a></li>
                    @endif

                    <li><a href="{{ url('admin/home') }}" class="btn btn-sm btn-flat m-1 btn-primary">Home</a></li>
                    <li><a href="{{ url('admin/hb837') }}" class="btn btn-sm btn-flat m-1 btn-success">HB837</a></li>
                    <li><a href="{{ url('admin/mapplots') }}" class="btn btn-sm btn-flat m-1 btn-warning">Plot Map</a></li>
                </ul>
            @endauth

            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.login') }}">{{ __('Login') }}</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ url('admin.profile.change_password') }}">Change
                                Password</a>
                            <a class="dropdown-item" href="{{ route('admin.logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                                class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
