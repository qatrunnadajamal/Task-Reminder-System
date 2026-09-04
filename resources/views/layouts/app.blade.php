

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- for quil css -descrip -->
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <!-- calendar -->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@7.0.2/all/global.js"></script>
         <script src="https://cdn.jsdelivr.net/npm/fullcalendar@7.0.2/themes/forma/global.js"></script>
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@7.0.2/skeleton.css' rel='stylesheet' />
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@7.0.2/themes/forma/theme.css' rel='stylesheet' />
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@7.0.2/themes/forma/palettes/blue.css' rel='stylesheet' />
        <!-- bootsrap icons -->
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
        <!-- css -->
        <link rel="stylesheet" href="/css/notification.css">
        <link rel="stylesheet" href="/css/editor.css">
        <!-- css ai  -->
        <link rel="stylesheet" href="{{ asset('css/ai.css') }}">
        <!-- css form  -->
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])


        <!-- onesignalset-up -->
        <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
            <script>
                window.OneSignalDeferred = window.OneSignalDeferred || [];

                OneSignalDeferred.push(async function(OneSignal) {
                    await OneSignal.init({
                        appId: "9bb67f7e-59d8-4960-ae98-b9d61cd1a2ed",
                        allowLocalhostAsSecureOrigin: true,
                        notifyButton: { enable: true }
                    });
                    await OneSignal.login("{{ auth()->id()}}")
                });

            </script>
            <meta name="csrf-token" content="{{ csrf_token() }}">

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>   
        </div>
        <x-aiTask/> 
        <x-noti/> 
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- CKEditor -->
        <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
        <script src="/js/notification.js"></script>
        <!-- script for ai  -->
          <script src="/js/ai_task.js"></script>
        <!-- script for ai  -->
        <script src="/js/calendar/calendar.js"></script>
        <!-- Your custom scripts -->
        <script src="{{ asset('js/task.js') }}"></script>
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script> -->
        <script src="{{ asset('js/editor.js') }}"></script>
                @yield('scripts')
        <!-- script for tagify -->
        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

        <!-- script for chart  -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="{{ asset('js/dashboard.js') }}"></script>
        <!-- boostrap -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    </body>
</html>
