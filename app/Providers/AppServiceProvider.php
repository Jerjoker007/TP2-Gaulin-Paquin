<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Repository\RepositoryInterface;
use App\Http\Repository\UserRepositoryInterface;
use App\Http\Repository\AuthRepositoryInterface;
use App\Http\Repository\EquipmentRepositoryInterface;
use App\Http\Repository\ReviewRepositoryInterface;
use App\Http\Repository\Eloquent\BaseRepository;
use App\Http\Repository\Eloquent\UserRepository;
use App\Http\Repository\Eloquent\AuthRepository;
use App\Http\Repository\Eloquent\EquipmentRepository;
use App\Http\Repository\Eloquent\ReviewRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(EquipmentRepositoryInterface::class, EquipmentRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
