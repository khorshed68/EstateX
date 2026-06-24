<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use PDO;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (class_exists(\Illuminate\Foundation\Console\ServeCommand::class)) {
            \Illuminate\Foundation\Console\ServeCommand::$passthroughVariables[] = 'USE_ZEND_ALLOC';
        }

        $this->app['db']->extend('oracle', function ($config, $name) {
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? '1521';
            $dbname = $config['database'] ?? 'xe';
            $username = $config['username'] ?? '';
            $password = $config['password'] ?? '';
            $charset = $config['charset'] ?? 'AL32UTF8';

            $dsn = "oci:dbname=//{$host}:{$port}/{$dbname};charset={$charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_CASE => PDO::CASE_LOWER,
            ];
            
            if (isset($config['options'])) {
                $options = array_replace($options, $config['options']);
            }

            $pdo = new PDO($dsn, $username, $password, $options);
            
            $config['name'] = $name;
            
            return new \App\Database\OracleConnection($pdo, $dbname, $config['prefix'] ?? '', $config);
        });
    }
}
