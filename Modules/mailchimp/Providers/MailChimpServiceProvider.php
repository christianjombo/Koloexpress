<?php

namespace Modules\MailChimp\Providers;

use App\Events\EmailVerified;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\MailChimp\Listeners\SubscribeToNewsletter;
use Crypt;

class MailChimpServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        try {
            config(['newsletter.apiKey' => Crypt::decryptString(setting('mailchimp_api_key'))]);
            config(['newsletter.lists.subscribers.id' => setting('mailchimp_list_id')]);
        } catch (\Exception $e) {

        }

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app['events']->listen(EmailVerified::class, SubscribeToNewsletter::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('mailchimp.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'mailchimp'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/mailchimp');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/mailchimp';
        }, \Config::get('view.paths')), [$sourcePath]), 'mailchimp');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/mailchimp');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'mailchimp');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'mailchimp');
        }
    }

    /**
     * Register an additional directory of factories.
     * 
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
