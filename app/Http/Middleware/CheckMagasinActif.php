<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMagasinActif
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('magasin_actif_id')) {
            $user = auth()->user();
            if ($user) {
                $magasin = $user->role === 'Admin' ? \App\Models\Magasin::first() : $user->magasins->first();
                if ($magasin) {
                    session(['magasin_actif_id' => $magasin->id]);
                }
            }
        }
        return $next($request);
    }

}
