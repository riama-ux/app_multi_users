<div class="container-fluid bg-white">
    <div class="navbar">
        <div class="col">
            <a href="{{ url('/') }}" class="text-decoration-none">
                <img src="{{ URL::to('template/img/icons/icon-48x48.png') }}" alt="" width="30" height="24" class="d-inline-block  col">
                <span>DocStore</span>
            </a>
        </div>
        <div class="text-end">
            @if(request()->is('/'))
            <a href="{{ route('auth.logup') }}" class="col btn btn-outline-primary">Créer un compte</a>
            <a href="{{ route('auth.login') }}" class="col btn btn-outline-primary">Se Connecter</a>
            @endif
            @if(request()->is('auth/login'))
            <a href="{{ route('auth.logup') }}" class="col btn btn-primary">Créer un compte</a>
            @endif
            @if(request()->is('auth/logup'))
            <a href="{{ route('auth.login') }}" class="col btn btn-primary">Se Connecter</a>
            @endif
        </div>
    </div>
</div> 