<?php namespace Mascame\Artificer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Mascame\Artificer\Middleware\InstalledMiddleware;


class InstallServiceProvider extends ServiceProvider {


	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
        \App::make('router')->middleware('artificer-installed', InstalledMiddleware::class);

        // Avoid redirection when using CLI
        if (\App::runningInConsole() || \App::runningUnitTests()) return true;

        if (! self::isInstalling() && ! self::isInstalled()) {
            $this->goToInstall();
        }
    }


    public static function isInstalled() {
        if (! self::isExtensionDriverReady()) {
            return false;
        }

        $pluginManager = Artificer::pluginManager();
        $widgetManager = Artificer::widgetManager();

        foreach (Artificer::getCoreExtensions() as $coreExtension) {

            if (! $pluginManager->isInstalled($coreExtension)
                && ! $widgetManager->isInstalled($coreExtension)) {
                return false;
            }
        }

        return true;
    }

    public static function isExtensionDriverReady() {
        $driver = config('admin.extension_driver');

        return ($driver == 'file' || $driver == 'database' && \Schema::hasTable(config('admin.migrations')));
    }

    public static function isInstalling() {
        return (Str::contains(request()->path(), 'install'));
    }

    protected function goToInstall() {
        abort(200, '', ['Location' => route('admin.install')]);
    }
}