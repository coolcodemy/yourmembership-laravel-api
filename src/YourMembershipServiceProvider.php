<?php

namespace CoolCodeMY\YourMembershipLaravelAPI;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class YourMembershipServiceProvider extends ServiceProvider
{
    protected $defer = true;

    private $name = 'yourmembership-laravel-api';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            sprintf('%s/config/%s.php', __DIR__, $this->name) => config_path(sprintf('%s.php', $this->name)),
        ]);

        Response::macro('xml', function (\SimpleXMLElement $value) {
            return Response::make($value->asXML())
                ->header('Content-type', 'text/xml');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $that = $this;

        $this->mergeConfigFrom(
            sprintf('%s/config/%s.php', __DIR__, $this->name), $this->name
        );

        $this->app->bind(\CoolCodeMY\YourMembershipLaravelAPI\YMLA::class, function ($app) use ($that) {
            return new \CoolCodeMY\YourMembershipLaravelAPI\YMLA(
                app(\GuzzleHttp\Client::class),
                app(\Illuminate\Cache\Repository::class),
                $app['config'][$that->name]['API_KEY'],
                $app['config'][$that->name]['SA_PASSCODE']
            );
        });
    }

    public function provides()
    {
        return [\CoolCodeMY\YourMembershipLaravelAPI\YMA::class];
    }
}
