<?php

declare(strict_types=1);

namespace LBHurtado\FormHandlerSelfie\Tests;

use LBHurtado\FormHandlerSelfie\SelfieHandlerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\LaravelData\LaravelDataServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            SelfieHandlerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('inertia.testing.ensure_pages_exist', false);

        // Laravel Data configuration
        $app['config']->set('data.validation_strategy', 'only_requests');
        $app['config']->set('data.max_transformation_depth', 6);
        $app['config']->set('data.throw_when_max_transformation_depth_reached', 6);

        // Selfie handler configuration
        $app['config']->set('selfie-handler.width', 640);
        $app['config']->set('selfie-handler.height', 480);
        $app['config']->set('selfie-handler.quality', 0.85);
        $app['config']->set('selfie-handler.format', 'image/jpeg');
        $app['config']->set('selfie-handler.facing_mode', 'user');
        $app['config']->set('selfie-handler.show_guide', true);
    }
}
