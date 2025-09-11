<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLeadSource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tracking if user is already authenticated
        if ($request->user()) {
            return $next($request);
        }

        // Special handling for login page - check for intended URL
        if ($request->path() === 'login') {
            $this->handleLoginPage($request);
            return $next($request);
        }

        // Skip tracking for other auth pages (register, password reset, etc.)
        if ($this->isAuthPage($request)) {
            return $next($request);
        }

        // Get current page data
        $currentPageData = [
            'page_name' => $this->getPageName($request),
            'page_url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
        ];

        // Only update lead source if we don't already have one, or if this is a different page
        $existingLeadSource = $request->session()->get('lead_source');
        
        if (!$existingLeadSource || $existingLeadSource['page_url'] !== $currentPageData['page_url']) {
            // Store in session
            $request->session()->put('lead_source', $currentPageData);

            // Also store in cookie for persistence across sessions (30 days)
            $cookieData = json_encode($currentPageData);
            $request->session()->put('lead_source_cookie', $cookieData);
        }

        return $next($request);
    }

    /**
     * Check if the current request is for an auth page or should be excluded
     */
    private function isAuthPage(Request $request): bool
    {
        $authPaths = [
            'register',
            'forgot-password',
            'reset-password',
            'verify-email',
            'confirm-password',
            'unsubscribe',
            'campaign/unsubscribe',
        ];

        $excludedPaths = [
            'data',
            'data-db-api',
            'webhook',
            'api',
            'admin',
        ];

        $currentPath = $request->path();

        // Check if it's an auth page
        if (in_array($currentPath, $authPaths) || 
            str_starts_with($currentPath, 'verify-email') ||
            str_starts_with($currentPath, 'reset-password') ||
            str_starts_with($currentPath, 'unsubscribe')) {
            return true;
        }

        // Check if it's an excluded path (AJAX/data routes)
        foreach ($excludedPaths as $excludedPath) {
            if (str_starts_with($currentPath, $excludedPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle login page - capture intended URL if available
     */
    private function handleLoginPage(Request $request): void
    {
        // Check if there's an intended URL in the session (set by Laravel's auth middleware)
        $intendedUrl = $request->session()->get('url.intended');
        
        if ($intendedUrl) {
            // Create lead source data for the intended URL
            $intendedPageData = [
                'page_name' => $this->getPageNameFromUrl($intendedUrl),
                'page_url' => $intendedUrl,
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString(),
            ];

            // Store in session (this will be used when user registers)
            $request->session()->put('lead_source', $intendedPageData);
            $request->session()->put('lead_source_cookie', json_encode($intendedPageData));
        }
    }

    /**
     * Convert URL to readable page name
     */
    private function getPageNameFromUrl(string $url): string
    {
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';
        
        // Remove leading slash
        $path = ltrim($path, '/');
        
        // Handle root path
        if (empty($path)) {
            return 'Home Page';
        }

        // Convert path to readable format
        $segments = explode('/', $path);
        $pageName = '';

        foreach ($segments as $segment) {
            if (empty($segment)) continue;
            
            // Convert snake_case or kebab-case to Title Case
            $formatted = str_replace(['-', '_'], ' ', $segment);
            $formatted = ucwords($formatted);
            
            if (!empty($pageName)) {
                $pageName .= ' / ';
            }
            $pageName .= $formatted;
        }

        return $pageName;
    }

    /**
     * Convert route path to readable page name
     */
    private function getPageName(Request $request): string
    {
        $path = $request->path();
        
        // Handle root path
        if (empty($path) || $path === '/') {
            return 'Home Page';
        }

        // Convert path to readable format
        $segments = explode('/', $path);
        $pageName = '';

        foreach ($segments as $segment) {
            if (empty($segment)) continue;
            
            // Convert snake_case or kebab-case to Title Case
            $formatted = str_replace(['-', '_'], ' ', $segment);
            $formatted = ucwords($formatted);
            
            if (!empty($pageName)) {
                $pageName .= ' / ';
            }
            $pageName .= $formatted;
        }

        return $pageName;
    }
} 