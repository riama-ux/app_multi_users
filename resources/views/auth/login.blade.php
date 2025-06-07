@extends('auth.layout')

@section('content')

 <div class="nk-block nk-block-middle nk-auth-body">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h2 class="nk-block-title text-center mb-5">Connexion</h2>
        </div>
    </div><!-- .nk-block-head -->

    @include('flash-message')

    <form action="{{ route('auth.postLogin') }}" method="post" class="form-validate is-alter" autocomplete="off">
        @csrf
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="email-address">Email</label>
            </div>
            <div class="form-control-wrap">
                <input autocomplete="email" 
                       type="email" 
                       name="email" 
                       id="email-address"
                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                       placeholder="Entrez votre adresse mail" 
                       value="{{ old('email') }}" 
                       required>
                @error('email')
                <span class="invalid-feedback" role="alert" style="display:block;">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div><!-- .form-group -->

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="password">Mot de passe</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input autocomplete="new-password" 
                       type="password" 
                       name="password" 
                       id="password" 
                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                       placeholder="Entrez votre mot de passe" 
                       required>
                @error('password')
                <span class="invalid-feedback" role="alert" style="display:block;">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div><!-- .form-group -->

        <div class="form-group">
            <button type="submit" class="btn btn-lg btn-primary btn-block">Valider</button>
        </div>
    </form><!-- form -->

    <div class="text-center mt-5">
        <span class="fw-500">Vous n'avez pas de compte? <a href="{{ route('auth.logup') }}">S'inscrire</a></span>
    </div>
</div><!-- .nk-block -->

                            
@endsection