<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GTEMP') }}</title>

    <!-- Fonts -->
    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="http://www.datatables.net/rss.xml">
    <link href="{{asset('front/css/jquery.dataTables.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('front/css/data_table.css')}}">


    <link href="{{asset('front/css/themes/lite-purple.min.css')}}" rel="stylesheet" />
    <link href="{{asset('front/css/plugins/perfect-scrollbar.min.css')}}" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">



    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.css' rel='stylesheet' />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


        @vite(['resources/css/app.css', 'resources/js/app.js'])


    <!-- Scripts -->
    <script src="//code.jquery.com/jquery-1.12.3.js"></script>
    <script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script
        src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
        <link rel="stylesheet"
        href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet"
        href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="{{asset('front/css/style.css')}}">
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow dark:bg-gray-800">
            <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main class="corps">
            {{ $slot }}
        </main>

        <footer>
            <div class="copyright">
                - &copy; Copyright <?= date('Y') ?> APPLICATION REALISEE & DEVELOPPEE PAR TERSYS -
            </div>
        </footer>
    </div>

    <script src="{{asset('front/js/data_table/jquery.min.js')}}"></script>
    <script src="{{asset('front/js/data_table/bootstrap.min.js')}}"></script>
    <script src="{{asset('front/js/data_table/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('front/js/data_table/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('front/js/data_table/buttons.flash.min.js')}}"></script>
    <script src="{{asset('front/js/data_table/jszip.min.js')}}"></script>
    <script src="{{asset('front/js/data_table/pdfmake.min.js')}}"></script>
    <script src="{{asset('front/js/data_table/vfs_fonts.js')}}"></script>
    <script src="{{asset('front/js/data_table/buttons.html5.min.js')}}"></script>
    <script src="{{asset('front/js/data_table/buttons.print.min.js')}}"></script>
    <script src="{{asset('front/js/data_table/export-table-data.js')}}"></script>
    <script src="{{asset('front/js/data_table/jquery.slimscroll.js')}}"></script>
    <script src="{{asset('front/js/data_table/switchery.min.js')}}"></script>
    <script src="{{asset('front/js/data_table/dropdown-bootstrap-extended.js')}}"></script>
    <script src="{{asset('front/js/data_table/init.js')}}"></script>
    @stack('scripts')
    @stack('table')
    @stack('scriptsHeures')
    @stack('stats')
    @stack('scriptsHeuresTekos')
</body>
</html>
