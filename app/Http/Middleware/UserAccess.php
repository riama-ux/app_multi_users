<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $userRoles)
    {
        
        // transforme "Admin,Supervisor" en tableau ['Admin', 'Supervisor']
        $roles = explode(',', $userRoles);

        // si le rôle de l'utilisateur est dans la liste des rôles autorisés
        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }
           
        return response()->json(['You do not have permission to access for this page.']);
        /* return response()->view('errors.check-permission'); */
    }
}
