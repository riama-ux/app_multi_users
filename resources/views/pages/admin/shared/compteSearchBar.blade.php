<div class="row g-2 align-items-end mb-3">
    <div class="col-6 col-md-6 col-lg-3">
        <input type="text" name="name" 
            class="form-control {{ request()->is('admin/compte/search') && $request->name != '' ? 'bg-light' : '' }}" 
            value="{{ request()->is('admin/compte/search') && $request->name != '' ? $request->name : '' }}" 
            placeholder="Nom utilisateur ...">
    </div>

    <div class="col-6 col-md-6 col-lg-3">
        <select name="role" 
            class="form-control {{ request()->is('admin/compte/search') && $request->role != '' ? 'bg-light' : '' }}">
            <option value="{{ request()->role ?? '' }}">{{ request()->role ?? 'Rôle ...' }}</option>
            @foreach($roles as $role)
                <option value="{{ $role }}">{{ $role }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <input type="text" name="email" 
            class="form-control {{ request()->is('admin/compte/search') && $request->email != '' ? 'bg-light' : '' }}" 
            value="{{ request()->email ?? '' }}" 
            placeholder="Email ...">
    </div>

    <div class="col-12 col-md-6 col-lg-3 text-end">
        <a href="{{ route('admin.compte.index') }}" class="btn btn-outline-primary mb-1">
            <i class="align-middle" data-feather="refresh-ccw"></i> Annuler
        </a>
        <button type="submit" class="btn btn-primary mb-1">
            <i class="align-middle" data-feather="search"></i> Recherche
        </button>
    </div>
</div>
