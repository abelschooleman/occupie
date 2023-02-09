<?php

namespace App\Http\Middleware;

use App\Models\RequestLog;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|RedirectResponse|Response
     */
    public function handle(Request $request, Closure $next): JsonResponse|RedirectResponse|Response
    {
        try {
            $log = new RequestLog();

            $log->origin = $request->getClientIp();
            $log->path = $request->path();
            $log->headers = $request->headers;
            $log->body = http_build_query($request->all());

            $log->save();
        } catch (\Throwable $exception) {
            report($exception);
        }

        return $next($request);
    }
}
