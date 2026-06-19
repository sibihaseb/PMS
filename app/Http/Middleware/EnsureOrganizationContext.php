<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationContext
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->organization_id === null) {
            return response()->json([
                'message' => 'User is not associated with an organization.',
            ], 403);
        }

        TenantContext::set($user->organization_id);

        return $next($request);
    }
}
