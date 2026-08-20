<?php

namespace App\Providers;

use App\Services\Contracts\WhatsappServiceInterface;
use App\Services\Whatsapp\FonnteWhatsappService;
use App\Services\Whatsapp\LogWhatsappService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WhatsappServiceInterface::class, function ($app) {
            $driver = config('services.whatsapp.driver', 'log');

            if ($driver === 'fonnte') {
                return new FonnteWhatsappService;
            }

            return new LogWhatsappService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
