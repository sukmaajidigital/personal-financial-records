<?php

namespace App\Http\Middleware;

use App\Models\SiteView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteView
{
    /**
     * Track unique page views on the welcome/home page.
     *
     * Security measures:
     * - IP addresses are hashed with HMAC-SHA256 (never stored raw)
     * - One record per IP per day (prevents artificial inflation)
     * - Database unique constraint prevents race condition duplicates
     * - Only tracks GET requests (not bots submitting POST/PUT)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful GET requests
        if ($request->isMethod('GET') && $response->isSuccessful()) {
            try {
                SiteView::recordView($request->ip());
            } catch (\Throwable) {
                // Silently fail — tracking should never break the app
            }
        }

        return $response;
    }
}
