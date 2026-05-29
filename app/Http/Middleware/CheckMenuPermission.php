<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Aksesmenu;
use App\Models\Menu;

class CheckMenuPermission
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        $path = $request->path();
        
        // Exclude root and non-admin paths
        if (!str_starts_with($path, 'admin')) {
            return $next($request);
        }

        // Exclude core administrative pathways
        $excludedPaths = [
            'admin/dashboard',
            'admin/perusahaan/dashboard',
            'admin/perusahaan/payment',
            'admin/perusahaan/event',
            'admin/perusahaan/loker',
            'admin/perusahaan/profile',
            'admin/profile',
            'admin/notifications',
            'admin/logout'
        ];

        foreach ($excludedPaths as $excludedPath) {
            if ($path === $excludedPath || str_starts_with($path, $excludedPath . '/')) {
                return $next($request);
            }
        }

        $roleIds = $user->roles->pluck('id')->toArray();
        if (in_array(1, $roleIds)) { // Superadmin bypass
            return $next($request);
        }

        // Fetch all permitted menu URLs for this user's roles
        $permittedUrls = Menu::whereHas('aksesmenus', function($query) use ($roleIds) {
            $query->whereIn('idrole', $roleIds);
        })->pluck('alamat_url')->toArray();

        // Process current path for segment-safe comparison
        $currentPathRaw = '/' . trim($path, '/');
        $currentPathComp = $currentPathRaw . '/'; // Append slash for safe segment matching

        foreach ($permittedUrls as $url) {
            if (!$url || $url == '#') continue;
            
            $permittedPathComp = '/' . trim($url, '/') . '/';
            
            // Check if current path matches or is a descendant of the permitted menu path
            if (str_starts_with($currentPathComp, $permittedPathComp)) {
                return $next($request);
            }
        }

        // Access denied if no matching menu permission exists
        abort(403, 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini secara langsung.');
    }
}
