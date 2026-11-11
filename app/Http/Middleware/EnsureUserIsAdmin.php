<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Chặn ở server: không phải quản trị viên thì trả 403.
        abort_unless($request->user()?->is_admin, 403);

        return $next($request);
    }
}
