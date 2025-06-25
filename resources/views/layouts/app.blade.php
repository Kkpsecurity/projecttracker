<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" siteType="standard-content">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="//fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        /* Global resets */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            font-family: 'Nunito', sans-serif;
            background-color: #394ea1;
            color: #212529;
        }

        #app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
            padding: 20px;
        }

        footer {
            background-color: #343a40;
            color: #f8f9fa;
            padding: 1rem 0;
            text-align: center;
        }

        @media (max-width: 768px) {
            main {
                padding: 10px;
            }

            footer {
                padding: 0.5rem 0;
            }
        }

        /* Form Controls */
        .form-control {
            border: 1px solid #f0f0f0;
            color: #212529 background-color: #cfcfcf;
            border-radius: 0;
            padding: 0.5rem;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .form-control:focus {
            border-color: #212529;
            box-shadow: 0 0 0 0.2rem rgba(33, 37, 41, 0.25);
        }

        /* Buttons */
        .btn {
            border-radius: 0;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }

        .card {
            display: block;
            background: #cacaca
        }
    </style>

    @yield('styles')
</head>

<body>
    <div id="app">
        @include('partials.navbar')


        @yield('content')


        <footer class="footer text-white bg-dark">
            <div class="container-fluid">
                <span class="text-muted">© {{ date('Y') }} <a href="{{ url('/') }}"
                        class="text-white">ProjectTracker</a>. All rights reserved.</span>
            </div>
        </footer>
    </div>

    @yield('modals')

</body>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')

</html>
