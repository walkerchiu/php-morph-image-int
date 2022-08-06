<?php

namespace WalkerChiu\MorphImage;

use Illuminate\Support\ServiceProvider;

class MorphImageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/morph-image.php' => config_path('wk-morph-image.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_morph_image_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_morph_image_table.php',
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-morph-image');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-morph-image'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-morph-image.command.cleaner')
            ]);
        }

        config('wk-core.class.morph-image.image')::observe(config('wk-core.class.morph-image.imageObserver'));
        config('wk-core.class.morph-image.imageLang')::observe(config('wk-core.class.morph-image.imageLangObserver'));
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-morph-image')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/morph-image.php', 'wk-morph-image'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/morph-image.php', 'morph-image'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}
