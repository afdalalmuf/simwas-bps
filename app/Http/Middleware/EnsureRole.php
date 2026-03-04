<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */



    public function handle(Request $request, Closure $next, ...$role)
    {
        $roleString = $role[0];
        $user = auth()->user();

        if ($roleString === 'is_sekretaris') {
            if ($user->is_sekma || $user->is_sekwil) {
                return $next($request);
            }
            return redirect('/');
        }

        if ($roleString === 'status') {
            return $user->status ? $next($request) : redirect('/logout');
        }

        // Check if it's a column-based role
        if (array_key_exists($roleString, $user->getAttributes()) && (bool)$user->$roleString === true) {
            return $next($request);
        }

        // Check from related roles
        if ($user->roles->contains('id', $roleString)) {
            return $next($request);
        }

        return redirect('/');
    }
}
