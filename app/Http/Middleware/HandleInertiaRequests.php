<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;
use Str;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            //
        ]);
    }

    public function handle(Request $request, Closure $next)
    {
        $parentResponse = parent::handle($request, $next);

        $location = $parentResponse->headers->get('Location');

        if ($this->isExternal($location)) {
            if (request()->inertia()) {
                return inertia()->location($location);
            }

            return redirect()->to($location);
        }

        return $parentResponse;
    }

    public function isExternal($url)
    {
        $internalHost = parse_url(config('app.url'), PHP_URL_HOST);
        $internalPort = parse_url(config('app.url'), PHP_URL_PORT);

        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);

        return $host !== $internalHost || $port !== $internalPort;
    }
}
