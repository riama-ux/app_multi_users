@extends('auth.layout')

@section('content')

<div class="nk-block nk-block-middle nk-auth-body">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h2 class="nk-block-title text-center mb-5">Inscription</h2>
        </div>
    </div><!-- .nk-block-head -->

    @include('flash-message')

    <form action="{{ route('auth.postLogup') }}" method="POST" class="form-validate is-alter" autocomplete="off">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="email">Email</label>
            </div>
            <div class="form-control-wrap">
                <input type="email" name="email" id="email" 
                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                       placeholder="Entrez votre adresse mail" 
                       value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                <span class="invalid-feedback" style="display:block;"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div><!-- .form-group -->

        {{-- Mot de passe --}}
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="password">Mot de passe</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" name="password" id="password" 
                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                       placeholder="Entrez votre mot de passe" required autocomplete="new-password">
                @error('password')
                <span class="invalid-feedback" style="display:block;"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        </div><!-- .form-group -->

        {{-- Confirmation --}}
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="password_confirmation">Confirmer</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password_confirmation">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                       class="form-control form-control-lg"
                       placeholder="Confirmez votre mot de passe" required autocomplete="new-password">
            </div>
        </div><!-- .form-group -->

        {{-- Bouton --}}
        <div class="form-group">
            <button type="submit" class="btn btn-lg btn-primary btn-block">Inscription</button>
        </div>
    </form><!-- form -->

    <div class="text-center mt-5">
        <span class="fw-500">Vous avez déjà un compte? <a href="{{ route('auth.login') }}">Se connecter</a></span>
    </div>
</div><!-- .nk-block -->
 

@endsection