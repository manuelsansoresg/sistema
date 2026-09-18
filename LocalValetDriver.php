<?php

use Valet\Drivers\BasicValetDriver;

class LocalValetDriver extends BasicValetDriver
{
    /**
     * Envía las rutas amigables al enrutador MVC de la aplicación.
     */
    public function beforeLoading(string $sitePath, string $siteName, string $uri): void
    {
        parent::beforeLoading($sitePath, $siteName, $uri);

        $route = trim(parse_url($uri, PHP_URL_PATH) ?? '', '/');

        if ($route !== '') {
            $_GET['url'] = $route;
        }
    }
}
