<!DOCTYPE html> 
<html lang="id">
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <meta name="csrf-token" content="{{ csrf_token() }}">     
    <title>{{ $title }} | {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('kemenkum.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">     
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/kemenkum.css') }}">
    <script src="{{ asset('js/kemenkum.js') }}" defer></script>
    @notifyCss      
    <script src="https://cdn.jsdelivr.net/npm/notify-js"></script>      

    <style>         
        .notify { position: fixed !important; top: 15px !important; right: 20px !important; z-index: 999999 !important; }
    </style> 
</head>  
<body class="bg-primary-white">          
    <div class="app-submit-progress" data-submit-progress hidden>
        <span class="app-submit-progress-track" aria-hidden="true">
            <span class="app-submit-progress-line"></span>
        </span>
        <span class="app-submit-progress-label" role="status">Sedang menyimpan…</span>
    </div>
    <x-notify::notify class="notify"/>      
    {{ $slot }}      
    @notifyJs      

    @if (session('success'))         
        <script> notify().success(@js(session('success')), "Success"); </script>
    @endif      

    @if ($errors->has('error'))         
        <script> notify().error(@js($errors->first('error')), "Error"); </script>
    @endif      

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('projectForm');
            const cancelButton = document.getElementById('cancelButton');

            if (form) {
                form.addEventListener("submit", function () {
                    form.submit();
                });
            }

            if (cancelButton) {
                cancelButton.addEventListener("click", function (event) {
                    event.preventDefault();
                    window.history.back();
                });
            }
        });
    </script>

    @vite('resources/js/app.js')  
</body>  
</html>
