<?php

namespace App\Providers;

use App\Models\File;
use App\Models\User;
use App\Models\Expense;
use App\Models\Program;
use App\Models\Project;
use App\Models\FourthLayer;
use App\Models\ProjectYear;
use App\Models\ProjectQuarter;
use App\Models\QuarterExpense;
use App\Models\ProjectDevision;
use App\Observers\FileObserver;
use App\Observers\UserObserver;
use App\Observers\ExpenseObserver;
use App\Observers\ProgramObserver;
use App\Observers\ProjectObserver;
use Illuminate\Support\Facades\Event;
use App\Observers\ProjectYearObserver;
use Illuminate\Auth\Events\Registered;
use App\Models\ProjectDivisionCategory;
use App\Observers\ProjectQuarterObserver;
use App\Observers\QuarterExpenseObserver;
use App\Observers\ProjectDevisionObserver;
use App\Models\QuarterExpenseBudgetDivision;
use App\Models\ProjectDivisionSubCategoryExpense;
use App\Observers\FourthLayerObserver;
use App\Observers\ProjectDivisionCategoryObserver;
use App\Observers\QuarterExpenseBudgetDivisionObserver;
use App\Observers\ProjectDivisionSubCategoryExpenseObserver;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Program::observe(ProgramObserver::class);
        Project::observe(ProjectObserver::class);
        Expense::observe(ExpenseObserver::class);
        File::observe(FileObserver::class);
        ProjectYear::observe(ProjectYearObserver::class);
        ProjectQuarter::observe(ProjectQuarterObserver::class);
        ProjectDevision::observe(ProjectDevisionObserver::class);
        ProjectDivisionCategory::observe(ProjectDivisionCategoryObserver::class);
        ProjectDivisionSubCategoryExpense::observe(ProjectDivisionSubCategoryExpenseObserver::class);
        FourthLayer::observe(FourthLayerObserver::class);

        QuarterExpenseBudgetDivision::observe(QuarterExpenseBudgetDivisionObserver::class);
        QuarterExpense::observe(QuarterExpenseObserver::class);


    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
