<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSectionAccess
{
    public function handle(Request $request, Closure $next, string $section): Response
    {
        if (!in_array($section, $request->user()->getSectionSlugs())) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a esta sección.',
            ], 403);
        }

        return $next($request);
    }
}
