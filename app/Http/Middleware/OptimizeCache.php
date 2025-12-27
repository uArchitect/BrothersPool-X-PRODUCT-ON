<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizeCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Pre-warm cache for frequently accessed data
        if ($request->is('kitchen*') || $request->is('dashboard*')) {
            $this->preWarmCache();
        }

        $response = $next($request);

        // Add cache headers for better browser caching
        if ($request->is('api/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=30');
            $response->headers->set('ETag', md5($response->getContent()));
        }

        return $response;
    }

    /**
     * Pre-warm frequently accessed cache
     */
    private function preWarmCache()
    {
        // Pre-warm settings cache
        if (!Cache::has('settings')) {
            Cache::remember('settings', 3600, function () {
                return DB::table('settings')->find(1);
            });
        }

        // Pre-warm categories cache
        if (!Cache::has('categories')) {
            Cache::remember('categories', 3600, function () {
                return DB::table('categories')->select('id', 'name')->get();
            });
        }

        // Pre-warm tables cache
        if (!Cache::has('tables')) {
            Cache::remember('tables', 3600, function () {
                return DB::table('tables')->get();
            });
        }
    }
}

