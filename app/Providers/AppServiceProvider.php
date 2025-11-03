<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\TodoRepositoryInterface;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\Contracts\TodoHistoryRepositoryInterface;
use App\Repositories\Contracts\TodoCommentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentTodoRepository;
use App\Repositories\Eloquent\EloquentTagRepository;
use App\Repositories\Eloquent\EloquentTodoHistoryRepository;
use App\Repositories\Eloquent\EloquentTodoCommentRepository;
use App\Repositories\Eloquent\EloquentUserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TodoRepositoryInterface::class, EloquentTodoRepository::class);
        $this->app->bind(TagRepositoryInterface::class, EloquentTagRepository::class);
        $this->app->bind(TodoHistoryRepositoryInterface::class, EloquentTodoHistoryRepository::class);
        $this->app->bind(TodoCommentRepositoryInterface::class, EloquentTodoCommentRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
