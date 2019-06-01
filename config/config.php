<?php

declare(strict_types=1);

use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = [
    'config_cache_path' => 'data/cache/config-cache.php',
];

$aggregator = new ConfigAggregator([
    \Zend\Db\ConfigProvider::class,
    \Zend\Mail\ConfigProvider::class,
    \Zend\Validator\ConfigProvider::class,
    \Zend\Expressive\Session\Ext\ConfigProvider::class,
    \Zend\Expressive\Session\ConfigProvider::class,
    \Zend\Expressive\Router\FastRouteRouter\ConfigProvider::class,
    \Zend\HttpHandlerRunner\ConfigProvider::class,
    \Zend\Expressive\Plates\ConfigProvider::class,
    // Include cache configuration
    new ArrayProvider($cacheConfig),
    \Zend\Expressive\Helper\ConfigProvider::class,
    \Zend\Expressive\ConfigProvider::class,
    \Zend\Expressive\Router\ConfigProvider::class,
    // Swoole config to overwrite some services (if installed)
    class_exists(\Zend\Expressive\Swoole\ConfigProvider::class)
        ? \Zend\Expressive\Swoole\ConfigProvider::class
        : function(){ return[]; },
    // Default App module config
    Api\ConfigProvider::class,
    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `global.php`
    //   - `*.global.php`
    //   - `local.php`
    //   - `*.local.php`
    new PhpFileProvider(realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'),
    // Load development config if it exists
    new PhpFileProvider(realpath(__DIR__) . '/development.config.php'),
], $cacheConfig['config_cache_path']);

return $aggregator->getMergedConfig();
