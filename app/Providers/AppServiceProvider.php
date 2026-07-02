<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\SalesIntegrationInterface;
use App\Filament\Commands\FileGenerators\Resources\ResourceClassGenerator;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\RegistrationResponse;
use App\Listeners\AssignTenantOnUserSync;
use App\Listeners\AssignUserToTenantOnTeamSync;
use App\Models\BugReport;
use App\Models\Campaign;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\CustomerConsent;
use App\Models\CustomerContact;
use App\Models\EmailTemplate;
use App\Models\Interaction;
use App\Models\Invoice;
use App\Models\LeadScore;
use App\Models\LoyaltyLevel;
use App\Models\LoyaltyPoint;
use App\Models\Opportunity;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Task;
use App\Models\User;
use App\Services\SalesIntegrationService;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse as RegistrationResponseContract;
use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator as BaseResourceClassGenerator;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Entry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Madbox99\UserTeamSync\Events\TeamCreatedFromSync;
use Madbox99\UserTeamSync\Events\UserCreatedFromSync;
use Override;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->bind(SalesIntegrationInterface::class, SalesIntegrationService::class);
        $this->app->singleton(RegistrationResponseContract::class, RegistrationResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureFilamentTranslations();
        $this->configureRateLimiting();
        $this->registerSyncListeners();

        FilamentTimezone::set('Europe/Budapest');

        $this->app->bind(BaseResourceClassGenerator::class, ResourceClassGenerator::class);

        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );

        Relation::enforceMorphMap([
            'bug_report' => BugReport::class,
            'campaign' => Campaign::class,
            'complaint' => Complaint::class,
            'customer' => Customer::class,
            'customer_consent' => CustomerConsent::class,
            'customer_contact' => CustomerContact::class,
            'email_template' => EmailTemplate::class,
            'interaction' => Interaction::class,
            'invoice' => Invoice::class,
            'lead_score' => LeadScore::class,
            'loyalty_level' => LoyaltyLevel::class,
            'loyalty_point' => LoyaltyPoint::class,
            'opportunity' => Opportunity::class,
            'order' => Order::class,
            'product' => Product::class,
            'quote' => Quote::class,
            'task' => Task::class,
            'user' => User::class,
        ]);
    }

    private function configureFilamentTranslations(): void
    {
        Field::configureUsing(fn (Field $c): Field => $c->translateLabel());
        Column::configureUsing(fn (Column $c): Column => $c->translateLabel());
        Entry::configureUsing(fn (Entry $c): Entry => $c->translateLabel());
        Filter::configureUsing(fn (Filter $c): Filter => $c->translateLabel());
        SelectFilter::configureUsing(fn (SelectFilter $c): SelectFilter => $c->translateLabel());
        Tab::configureUsing(fn (Tab $c): Tab => $c->translateLabel());
        Section::configureUsing(fn (Section $c): Section => $c->translateLabel());
        Action::configureUsing(fn (Action $c): Action => $c->translateLabel());
        Wizard::configureUsing(fn (Wizard $c): Wizard => $c->translateLabel());
    }

    private function registerSyncListeners(): void
    {
        Event::listen(UserCreatedFromSync::class, AssignTenantOnUserSync::class);
        Event::listen(TeamCreatedFromSync::class, AssignUserToTenantOnTeamSync::class);
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('global', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));

        RateLimiter::for('sync-api', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));
    }
}
