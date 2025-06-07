<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="shortcut icon" href="{{URL::to('template/img/icons/icon-48x48.png')}}" />

    <title>DOCSTORE</title>

    <link href="{{ URL::to('template/css/app.css') }}" rel="stylesheet">
    <link href="{{ URL::to('template/css/fonts.googleapis.css') }}" rel="stylesheet">
</head>

<body style="font-family: JetBrains Mono, Verdana, Geneva, Tahoma, sans-serif;">

    @include('auth.navbar')

    <main class="d-flex">
        <div class="container d-flex flex-column">
            <div class="row">
                <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">

                        <div class="h3 text-primary pt-5">Connexion</div> 

                        <div class="card">

                            @include('flash-message')

                            <div class="card-body">
                                <div class="">

                                    <form action="{{ route('auth.postLogin') }}" method="post">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input class="form-control form-control-lg @error('email') is-invalid @enderror" type="email" name="email" placeholder="Entrez votre Email" value="{{ old('email') }}" required autocomplete="email" />
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Mot de passe</label>
                                            <input class="form-control form-control-lg @error('password') is-invalid @enderror" type="password" name="password" placeholder="Entrez votre Mot de passe" />
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="text-end mt-3">
                                            <button type="reset" class="btn btn-lg btn-outline-primary">Annuler</button>
                                            <button type="submit" class="btn btn-lg btn-primary">Valider</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ URL::to('template/js/app.js') }}"></script>

</body>

</html>