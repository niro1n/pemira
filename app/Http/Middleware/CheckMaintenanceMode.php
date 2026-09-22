<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! SystemSetting::isMaintenanceMode()) {
            if ($request->routeIs('maintenance') || $request->is('maintenance')) {
                return redirect()->route('home');
            }

            return $next($request);
        }

        $user = $request->user();

        if ($user instanceof User && $user->isSuperAdmin()) {
            return $next($request);
        }

        if ($request->routeIs('maintenance') || $request->is('maintenance')) {
            return $next($request);
        }

        if ($request->routeIs('login') || $request->is('login') || $request->routeIs('logout') || $request->is('logout')) {
            return $next($request);
        }

        if ($request->is('livewire*') || $request->hasHeader('X-Livewire')) {
            $referer = (string) $request->headers->get('referer', '');
            if (str_contains($referer, '/login')) {
                return $next($request);
            }

            if ($request->expectsJson() || $request->hasHeader('X-Livewire')) {
                return response()->json([
                    'message' => 'Sistem sedang dalam mode pemeliharaan.',
                ], 503);
            }

            return redirect()->route('maintenance');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Sistem sedang dalam mode pemeliharaan.',
            ], 503);
        }

        return redirect()->route('maintenance');
    }
}
