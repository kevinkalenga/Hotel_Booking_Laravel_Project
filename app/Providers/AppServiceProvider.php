<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Page;
use App\Models\Room;
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
        paginator::useBootstrap();

        $page_data = Page::where('id', 1)->first();
        $room_data = Room::get();

        view()->share('global_page_data', $page_data);
        view()->share('global_room_data', $room_data);
    }
}
