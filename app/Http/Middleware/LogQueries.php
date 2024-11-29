<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogQueries
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Écouter les requêtes
        DB::listen(function ($query) {
            // Enregistre la requête et son temps d'exécution
            $sql = $query->sql;
            $bindings = $query->bindings;
            $time = $query->time;

            // Formate la requête avec les bindings
            $formattedQuery = vsprintf(str_replace("?", "'%s'", $sql), $bindings);

            // Log la requête
            Log::info('Query: ' . $formattedQuery . ' | Time: ' . $time . 'ms');
        });

        return $next($request);
    }
}
