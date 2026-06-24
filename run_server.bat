@echo off
echo Starting EstateX Laravel Dev Server with USE_ZEND_ALLOC=0...
set USE_ZEND_ALLOC=0
php artisan serve
