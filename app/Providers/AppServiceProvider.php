<?php

namespace App\Providers;

use App\Services\ABTestManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('ab-test', function () {
            return new ABTestManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        // For demo purposes only
        Blade::directive('abClass', function ($expression) {
            [$testName, $variantName, $className] = explode(',', str_replace(['(', ')', ' '], '', $expression));
            return "<?php echo AB::isVariantSelected($testName, $variantName) ? $className : ''; ?>";
        });
    }
}
