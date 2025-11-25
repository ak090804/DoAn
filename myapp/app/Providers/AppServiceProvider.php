<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() : void
    {
        // Set pagination view to Bootstrap 5
        Paginator::useBootstrap(5);

        // 🔹 Tắt kiểm tra khóa ngoại trước khi chạy migrate:refresh
        Schema::disableForeignKeyConstraints();

        // 🔹 Sau đó bật lại khi hoàn tất migrate
        $this->app->terminating(function () {
            Schema::enableForeignKeyConstraints();
        });
    }
}
