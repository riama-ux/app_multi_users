<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" href="{{ URL::to('template/img/icons/icon-48x48.png') }}" />

    <title>DocStore</title>

    <link href="{{ URL::to('template/css/app.css') }}" rel="stylesheet">
    <link href="{{ URL::to('template/css/fonts.googleapis.css') }}" rel="stylesheet">
</head>

<body style="font-family: JetBrains Mono, Verdana, Geneva, Tahoma, sans-serif;">

    @include('auth.navbar')

    <main class="bg-white">
        <div class="container">
            <div class="row">
                <img src="template\img\data_storage.jpg" alt="logo" srcset="" class="img-fluid auto" style="height: 90vh;">
            </div>
        </div>
    </main>


    <script src="{{ URL::to('template/js/app.js') }}"></script>

</body>

</html>