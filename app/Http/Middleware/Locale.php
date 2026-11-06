<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\DB;
class Locale
{
    public function handle($request, Closure $next)
    {
        $locales=DB::table('locales')->where('is_active',true)->orderBy('sort_order')->get();
        $locale=$request->route('locale');
        abort_unless($locales->contains('code',$locale),404);
        app()->setLocale($locale);
        view()->share(compact('locales','locale'));
        return $next($request)->header('Cache-Control','private, no-store');
    }
}
