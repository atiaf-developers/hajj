<!doctype html>
<html>
    <head>

        @include('components/front/meta')

    </head>

    <body>

        @include('components/front/header')

        <div style="min-height: 700px;">
            @yield('content')
        </div>



        @include('components/front/footer')

        @include('components/front/scripts')


    </body>
</html>
