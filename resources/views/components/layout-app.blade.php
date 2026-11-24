<!DOCTYPE html> 
<html lang="en">  
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <meta name="csrf-token" content="{{ csrf_token() }}">     
    <title>{{ $title }}</title>     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">     
    @vite('resources/css/app.css')      
    @notifyCss      
    <script src="https://cdn.jsdelivr.net/npm/notify-js"></script>      

    <style>         
        .notify { position: fixed !important; top: 15px !important; right: 20px !important; z-index: 999999 !important; }
        body, html { overflow: visible !important; } 
        nav, aside { z-index: 1000 !important; }    
    </style> 
</head>  
<body class="bg-primary-white">          
    <x-notify::notify class="notify"/>      
    {{ $slot }}      
    @notifyJs      

    @if (session('success'))         
        <script> notify().success("{{ session('success') }}", "Success"); </script>     
    @endif      

    @if ($errors->has('error'))         
        <script> notify().error("{{ $errors->first('error') }}", "Error"); </script>     
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
