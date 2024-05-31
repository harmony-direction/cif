<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>CIF HRMs</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css?v=2') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/bs-stepper/css/bs-stepper.min.css') }}">
    {{--
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}"> --}}

    {{-- <link rel="stylesheet" href="{{ asset('assets/css/adminlte.min.css') }}"> --}}

    @stack('styles')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans+Thai">
    <style>
        html, body {
            height: 100%;
            font-family: "Noto Sans Thai","Nunito", sans-serif !important;
        }
    </style>

<body data-page="dashboard">
    <div class="d-flex flex-grow-1">
        @include('layouts.partial.dashborad-aside', ['groupUrl' => isset($groupUrl) ? $groupUrl:''])
        <div class="d-flex flex-grow-1 flex-column rounded-start-3 overflow-hidden" style="background: #F2F4F7;">
            <nav class="navbar navbar-expand">
                <ul class="navbar-nav d-flex justify-content-between align-items-center w-100 px-3">
                    <li class="nav-item d-flex align-items-center gap-3">
                        <a href="#" id="menuBtn" class="menuBtn text-primary p-0 d-lg-none" >
                            <span class="material-symbols-outlined" style="font-size: 2rem;">
                                menu
                            </span>
                        </a>
                        <a href="{{ route('home') }}" class="d-flex text-decoration-none" style="color: #101828;">
                            <span class="material-symbols-outlined" style="font-size: 28px">
                                home
                            </span>
                        </a>
                    </li>
                    {{-- Current Page --}}
                    @isset($currentPage)
                        <li class="nav-item">
                            <h4 class="m-0">{{ $currentPage }}</h4>
                        </li>
                    @endisset
                    <li class="d-flex gap-3">
                        <div class="d-none d-sm-block">
                            <p class="text-md-end m-0">{{Auth::user()->name}} {{Auth::user()->lastname}}</p>
                            <p class="text-md-end m-0 text-muted" style="font-size: 12px">{{Auth::user()->user_position->name}}</p>
                        </div>
                        <img src="{{ Auth::user()->avatar != "" ? route('storage.avatar', ['image'=> Auth::user()->avatar]) : asset('user_test.png') }}" class="rounded-circle" width="40px" height="40px" alt="avatar">
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="btn btn-outline-secondary p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <mask id="mask0_114_445" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="20">
                                <rect width="20" height="20" fill="#D9D9D9"/>
                                </mask>
                                <g mask="url(#mask0_114_445)">
                                <path d="M9.02085 9.89583V2.875C9.02085 2.59722 9.1146 2.36111 9.3021 2.16667C9.4896 1.97222 9.72224 1.875 10 1.875C10.2778 1.875 10.5104 1.97222 10.6979 2.16667C10.8854 2.36111 10.9792 2.59722 10.9792 2.875V9.89583C10.9792 10.1736 10.8854 10.4062 10.6979 10.5938C10.5104 10.7812 10.2778 10.875 10 10.875C9.72224 10.875 9.4896 10.7812 9.3021 10.5938C9.1146 10.4062 9.02085 10.1736 9.02085 9.89583ZM10 18.0625C8.88891 18.0625 7.84724 17.8542 6.87502 17.4375C5.9028 17.0208 5.0521 16.4479 4.32294 15.7188C3.59377 14.9896 3.02085 14.1389 2.60419 13.1667C2.18752 12.1944 1.97919 11.1528 1.97919 10.0417C1.97919 9.05556 2.14585 8.12153 2.47919 7.23958C2.81252 6.35764 3.31252 5.55556 3.97919 4.83333C4.17363 4.59722 4.42016 4.46875 4.71877 4.44792C5.01738 4.42708 5.2778 4.52083 5.50002 4.72917C5.69446 4.92361 5.78474 5.15625 5.77085 5.42708C5.75697 5.69792 5.66669 5.9375 5.50002 6.14583C4.97224 6.6875 4.57988 7.2882 4.32294 7.94792C4.06599 8.60764 3.93752 9.30556 3.93752 10.0417C3.93752 11.7222 4.5278 13.1528 5.70835 14.3333C6.88891 15.5139 8.31947 16.1042 10 16.1042C11.6806 16.1042 13.1111 15.5139 14.2917 14.3333C15.4722 13.1528 16.0625 11.7222 16.0625 10.0417C16.0625 9.29167 15.9306 8.59028 15.6667 7.9375C15.4028 7.28472 15.0278 6.6875 14.5417 6.14583C14.3611 5.92361 14.2604 5.66667 14.2396 5.375C14.2188 5.08333 14.3056 4.84722 14.5 4.66667C14.7222 4.44444 14.9861 4.34722 15.2917 4.375C15.5972 4.40278 15.8542 4.53472 16.0625 4.77083C16.7014 5.50694 17.191 6.32292 17.5313 7.21875C17.8715 8.11458 18.0417 9.05556 18.0417 10.0417C18.0417 11.1528 17.8299 12.1944 17.4063 13.1667C16.9827 14.1389 16.4063 14.9896 15.6771 15.7188C14.9479 16.4479 14.0972 17.0208 13.125 17.4375C12.1528 17.8542 11.1111 18.0625 10 18.0625Z" fill="#101828"/>
                                </g>
                            </svg>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </nav>
            @yield('content')
        </div>


    </div>
    @include('layouts.footer')


</body>
{{-- HRM Slidebar --}}
<script src="{{ asset('js/hrm_slidebar.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>

<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
<script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>

{{-- <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script> --}}

{{-- <script src="{{ asset('assets/js/adminlte.min.js') }}"></script> --}}

@stack('scripts')



</html>
