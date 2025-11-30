<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:Admin') or ->middleware('role:Admin,Sales')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (! $user || ! $user->role) {
            return redirect()->route('dashboard')->with('error', 'Acceso no autorizado.');
        }

        $name = $user->role->name;

        foreach ($roles as $r) {
            if (strcasecmp(trim($r), $name) === 0) {
                return $next($request);
            }
        }

        return redirect()->route('dashboard')->with('error', 'Acceso no autorizado para el rol actual.');
    }
}
