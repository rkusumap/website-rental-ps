<!doctype html>
<html lang="en">
    <head>

        @include('admin.metadata')
        @yield('css')
    </head>
    <body class="sidebar-toggle-display sidebar-hidden">
        <div class="container-scroller">
            @include('admin.header')
            <div class="container-fluid page-body-wrapper">
            @include('admin.navigation')
            <div class="main-panel">
                <div class="content-wrapper">
                @yield('page-title')
                @yield('content')
                </div>
            </div>
            </div>
            @include('admin.footer')
        </div>

        @include('admin.script')
        @yield('script')
    </body>

</html>
