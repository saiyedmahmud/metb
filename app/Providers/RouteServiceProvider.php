<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000)->by($request->ip())->response(function () {
                return response()->json(['error' => 'Too Many Attempts.'], 429);
            });
        });

        $this->routes(function () {

            //Dashboard
            Route::middleware('dashboard')
                ->prefix('dashboard')
                ->group(base_path('app/Http/Controllers/Dashboard/dashboardRoutes.php'));

            //Files
            Route::middleware('files')
                ->prefix('files')
                ->group(base_path('app/Http/Controllers/Files/filesRoutes.php'));

            //HR
            Route::middleware('permission')
                ->prefix('permission')
                ->group(base_path('app/Http/Controllers/HR/RolePermission/permissionRoutes.php'));
            Route::middleware('role')
                ->prefix('role')
                ->group(base_path('app/Http/Controllers/HR/RolePermission/roleRoutes.php'));
            Route::middleware('role-permission')
                ->prefix('role-permission')
                ->group(base_path('app/Http/Controllers/HR/RolePermission/rolePermissionRoutes.php'));

            //Accounting
            Route::middleware('account')
                ->prefix('account')
                ->group(base_path('app/Http/Controllers/Accounting/Account/accountRoutes.php'));
            Route::middleware('transaction')
                ->prefix('transaction')
                ->group(base_path('app/Http/Controllers/Accounting/Transaction/transactionRoutes.php'));

            //Settings
            Route::middleware('setting')
                ->prefix('setting')
                ->group(base_path('app/Http/Controllers/Settings/AppSetting/appSettingRoutes.php'));
            route::middleware('currency')
                ->prefix('currency')
                ->group(base_path('app/Http/Controllers/Settings/Currency/currencyRoutes.php'));
            route::middleware('terms-and-condition')
                ->prefix('terms-and-condition')
                ->group(base_path('app/Http/Controllers/Settings/TermsAndCondition/termsAndConditionRoutes.php'));

            Route::middleware('announcement')
                ->prefix('announcement')
                ->group(base_path('app/Http/Controllers/HR/Announcement/announcementRoutes.php'));
            Route::middleware('page-size')
                ->prefix('page-size')
                ->group(base_path('app/Http/Controllers/Settings/PageSize/pageSizeRoutes.php'));

            //user
            Route::middleware('user')
                ->prefix('user')
                ->group(base_path('app/Http/Controllers/User/userRoutes.php'));

            //media
            Route::middleware('media')
                ->prefix('media')
                ->group(base_path('app/Http/Controllers/MediaFiles/MediaFileRoutes.php'));

            //invoice
            Route::middleware('invoiceCategory')
                ->prefix('invoice-category')
                ->group(base_path('app/Http/Controllers/InvoiceCategory/invoiceCategoryRoutes.php'));
            Route::middleware('invoice')
                ->prefix('invoice')
                ->group(base_path('app/Http/Controllers/Invoice/invoiceRoutes.php'));

            //Report
            Route::middleware('report')
                ->prefix('report')
                ->group(base_path('app/Http/Controllers/Report/reportRoutes.php'));

            //Namaz Time
            Route::middleware('namazTime')
                ->prefix('namaz-time')
                ->group(base_path('app/Http/Controllers/NamazTime/namazTimeRoutes.php'));
        });
    }
}
