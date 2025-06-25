@extends('pages.admin.shared.layout')

@section('content')
    <h3>Modifier le client</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('module.clients.update', $client->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nom">Nom</label>
            <input type="text" name="nom" class="form-control" required value="{{ old('nom', $client->nom) }}">
        </div>

        <div class="mb-3">
            <label for="telephone">Téléphone</label>
            <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $client->telephone) }}">
        </div>

        <div class="mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
        </div>

        <div class="mb-3">
            <label for="adresse">Adresse</label>
            <textarea name="adresse" class="form-control">{{ old('adresse', $client->adresse) }}</textarea>
        </div>

        <button class="btn btn-primary">Modifier</button>
        <a href="{{ route('module.clients.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection
