<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File; // For loading translations
use Illuminate\Support\Facades\Cache; // Optional: for caching translations

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'student_id' => $request->user()->student_id,
                    'name' => $request->user()->name,
                    'name_th' => $request->user()->name_th,
                    'surname_th' => $request->user()->surname_th,
                    'email' => $request->user()->email,
                    'avatar' => $request->user()->avatar,
                    'avatar_url' => $request->user()->avatar_url,
                    // Add these lines to share permissions and roles
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                    'roles' => $request->user()->getRoleNames(),
                ] : null,
            ],

            'ziggy' => function () use ($request) { // If you use Ziggy
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'flash' => [ // If you handle flash messages
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],

            'appName' => config('app.name', 'AppSchool'), // คุณอาจจะมี appName อยู่แล้ว
          //  'appLogo' => asset('images/edueva_logo1.png'), // <--- เพิ่มตรงนี้
            'appLogo' => asset('images/appschool_logo_o.svg'), // <--- เพิ่มตรงนี้

                    // Add locale and translations
                    'locale' => fn () => App::getLocale(),
                    'translations' => function () {
                        $locale = App::getLocale();
                        $cacheKey = "translations_{$locale}";

                        // Cache translations for better performance in production
                        return Cache::rememberForever($cacheKey, function () use ($locale) {
                            $phpTranslations = [];
                            $jsonTranslations = [];

                            $phpLangPath = lang_path("{$locale}/messages.php"); // Assuming 'messages.php'
                            $jsonLangPath = lang_path("{$locale}.json");

                            if (File::exists($phpLangPath)) {
                                $phpTranslations = include $phpLangPath;
                            }
                            if (File::exists($jsonLangPath)) {
                                $jsonTranslations = json_decode(File::get($jsonLangPath), true);
                            }
                            return array_merge($phpTranslations, $jsonTranslations);
                        });
                    },
                    // Share available locales for the switcher component
                    'available_locales' => fn () => config('app.available_locales'),
        ]);
    }
}