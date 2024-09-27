<?php

namespace Webkul\ZoomMeeting\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ZoomMeetingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'zoom_meeting');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'zoom_meeting');

        $this->publishes([
            __DIR__.'/../../publishable/assets'                                            => public_path('zoom'),
            __DIR__.'/../Resources/views/components/activities/actions/activity.blade.php' => resource_path('views/vendor/admin/components/activities/actions/activity.blade.php'),
            __DIR__.'/../Resources/views/activities/edit.blade.php'                        => resource_path('views/vendor/admin/activities/edit.blade.php'),
        ], 'public');

        Blade::anonymousComponentPath(__DIR__.'/../Resources/views/components', 'zoom_meeting');

        Event::listen('admin.layout.head.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('zoom_meeting::components.layouts.style');
        });

        Event::listen('admin.components.activities.actions.activity.form_controls.modal.content.controls.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('zoom_meeting::leads.view.activities.create');
        });

        Event::listen('admin.activities.edit.form_controls.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('zoom_meeting::activities.zoom');
        });

        $this->app->register(ModuleServiceProvider::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/acl.php', 'acl'
        );
    }
}
