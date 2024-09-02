<?php

use App\Livewire\Auth\LoginComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\Backend\RoleComponent;
use App\Livewire\Auth\RegisterComponent;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LeavesController;
use App\Livewire\Backend\SettingComponent;
use App\Http\Controllers\ProjectController;

use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\ProjectsController;
use App\Livewire\Backend\DashboardComponent;
use App\Livewire\Backend\Task\TaskComponent;
use App\Livewire\Backend\User\UserComponent;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\CompaniesController;
use App\Livewire\Auth\ResetPasswordComponent;
use App\Livewire\Backend\PermissionComponent;
use App\Http\Controllers\AttendanceController;
use App\Livewire\Auth\ForgotPasswordComponent;
use App\Http\Controllers\AttachmentsController;
use App\Livewire\Backend\Leaves\LeaveComponent;
use App\Livewire\Backend\User\ProfileComponent;
use App\Http\Controllers\NotificationController;
use App\Livewire\Backend\Client\ClientComponent;
use App\Livewire\Backend\Task\TaskDataComponent;
use App\Livewire\Backend\Task\ViewTaskComponent;
use App\Livewire\Backend\UpdatePasswordComponent;
use App\Livewire\Backend\Invoice\InvoiceComponent;
use App\Livewire\Backend\Project\ProjectComponent;
use App\Livewire\Backend\User\UserProfileComponent;
use App\Livewire\Backend\CalendarDashboardComponent;
use App\Livewire\Backend\Client\EditClientComponent;
use App\Livewire\Backend\User\UserContractComponent;
use App\Livewire\Backend\Email\EmailSettingComponent;
use App\Livewire\Backend\Client\CreateClientComponent;
use App\Livewire\Backend\Email\EmailTemplateComponent;
use App\Livewire\Backend\Invoice\EditInvoiceComponent;
use App\Livewire\Backend\Business\EditBusinessComponent;
use App\Livewire\Backend\Invoice\CreateInvoiceComponent;
use App\Livewire\Backend\Project\ProjectDetailComponent;
use App\Livewire\Backend\Invoice\PreviewInvoiceComponent;
use App\Livewire\Backend\Business\CreateBusinessComponent;
use App\Livewire\Backend\Business\SelectBusinessComponent;
use App\Livewire\Backend\Cms\Businesses\BusinessComponent;
use App\Livewire\Backend\Cms\Dashboard\CmsDashboardComponent;
use App\Livewire\Backend\KnowledgeBase\KnowledgeBaseComponent;
use App\Livewire\Backend\TermsCondition\TermsConditionComponent;
use App\Livewire\Backend\TermsCondition\TermsConditionAcceptComponent;
use App\Livewire\Backend\KnowledgeBase\SearchKnowledgeBaseKeywordComponent;

Route::middleware(['guest'])->group(function () {
    Route::get('/', LoginComponent::class);
    Route::get('/login', LoginComponent::class)->name('login');
    Route::get('/register/{encryption}', RegisterComponent::class)->name('register');
    Route::get('/forgot-password', ForgotPasswordComponent::class)->name('password.request');
    Route::get('/password/reset/{token}', ResetPasswordComponent::class)->name('password.reset');
});

Route::post('/logout', [LoginComponent::class, 'logout'])->name('logout')->middleware(['auth', 'verified']);
Route::middleware(['auth', 'verified', 'user-account-type', 'set_session_data'])->group(function () {
    Route::get('select-business', SelectBusinessComponent::class)->name('select-business');

    Route::prefix('cms')->name('cms.')->group(function () {
        Route::get('system-settings', SettingComponent::class)->name('system-settings')->middleware('permission:manage_system_settings');
        Route::get('businesses', BusinessComponent::class,)->name('businesses')->middleware('permission:view_businesses');
        Route::get('dashboard', CmsDashboardComponent::class,)->name('dashboard');
    });

    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::middleware(['terms.acceptance'])->group(function () {
            Route::get('/', DashboardComponent::class)->name('home');
            Route::get('calendar', CalendarDashboardComponent::class)->name('home.calendar');

            // Businesses routes
            Route::prefix('businesses')->name('businesses.')->middleware('permission:add_businesses|edit_businesses')->group(function () {
                Route::get('create', CreateBusinessComponent::class)->name('create')->middleware('permission:add_businesses');
                Route::get('edit/{slug}', EditBusinessComponent::class)->name('edit')->middleware('permission:edit_businesses');
            });

            // Clients routes
            Route::prefix('clients')->name('clients.')->middleware('permission:view_clients|add_clients|edit_clients')->group(function () {
                Route::get('/', ClientComponent::class)->name('index');
                Route::get('add-user-fields', [ClientComponent::class, 'addUserFields'])->name('add-user-fields');
                Route::get('create', CreateClientComponent::class)->name('create')->middleware('permission:add_clients');
                Route::get('edit/{slug}', EditClientComponent::class)->name('edit')->middleware('permission:edit_clients');
            });

            // Knowledge base routes
            Route::prefix('knowledgebase')->name('knowledgebase.')->middleware('permission:view_knowledgeBase')->group(function () {
                Route::get('/', KnowledgeBaseComponent::class)->name('index');
                Route::get('search/{keyword}', SearchKnowledgeBaseKeywordComponent::class)->name('search-keyword');
            });

            // Terms & conditions routes
            Route::prefix('terms-conditions')->name('terms-conditions.')->group(function () {
                Route::get('/', TermsConditionComponent::class)->name('index');
            });

            // Users routes
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', UserComponent::class)->name('index')->middleware('permission:view_users');
                Route::get('profile/{id}', ProfileComponent::class)->name('profile')->middleware('permission:view_users');
                Route::get('contracts', UserContractComponent::class)->name('contracts')->middleware('permission:view_contracts');
                Route::get('contracts/view/{id}', [UserContractComponent::class, 'view'])
                ->name('contracts.view')->middleware('permission:view_contracts');
            });

            // User leave routes
            Route::prefix('leave')->name('leave.')->group(function () {
                Route::get('/', LeaveComponent::class)->name('index')->middleware('permission:view_leaves');
            });

            // Projects routes
            Route::get('projects/chart-data/{slug}', [ProjectDetailComponent::class, 'chartData'])->name('project.chart-data');
            Route::prefix('projects')->name('projects.')->group(function () {
                Route::get('/', ProjectComponent::class)->name('index')->middleware('permission:view_projects|view_associated_projects');
                Route::get('{slug}', ProjectDetailComponent::class)->name('detail')->middleware('permission:view_users|view_associated_projects');
            });

            // Tasks routes
            Route::prefix('tasks')->name('tasks.')->group(function () {
                Route::get('/', TaskComponent::class)->name('index')->middleware('permission:view_tasks');
                Route::get('{id}', ViewTaskComponent::class)->name('view');
                Route::post('list-with-billable-comments', [TaskDataComponent::class, 'listWithBillableComments'])->name('list-with-billable-comments');
            });

            Route::prefix('invoices')->name('invoices.')->group(function () {
                Route::get('/', InvoiceComponent::class)->name('index');
                Route::get('/view-draft/{id}', EditInvoiceComponent::class);
                Route::get('/preview/invoice', PreviewInvoiceComponent::class)->name('preview');

            });
            Route::get('/invoices/create', CreateInvoiceComponent::class)->name('create-invoice');

            Route::get('/accept-terms-conditions', TermsConditionAcceptComponent::class)->name('terms-conditions.accept');
            Route::get('/roles', RoleComponent::class)->name('roles');
            Route::get('profile', UserProfileComponent::class)->name('profile');
            Route::get('update-password', UpdatePasswordComponent::class)->name('update-password');

            Route::get('emails', EmailTemplateComponent::class)->name('emails');
            Route::get('email/settings', EmailSettingComponent::class)->name('emails.settings');
        });
        Route::post('upload-digital-image', [TermsConditionAcceptComponent::class , 'uploadDigitalImage'])->name('upload-digital-image');
    });
});

Route::group(['middleware' => ['auth', 'contract']], function () {
    Route::get('/dashboard/events', [PagesController::class, 'events'])->name('dashboard.events');
    Route::get('/dashboard/events/attendance', [PagesController::class, 'attendance'])->name('dashboard.attendance');
    Route::get('/companies/data/json', [CompaniesController::class, 'companies'])->name('companies.json');
    Route::get('/company', [CompaniesController::class, 'getCompany'])->name('company.json');
    Route::resource('contracts', ContractController::class);
    Route::get('/contracts/data/json', [ContractController::class, 'contracts'])->name('contracts.json');
    Route::resource('users', UsersController::class);
    Route::get('/users/data/json', [UsersController::class, 'users'])->name('users.json');
    Route::get('/users/update/password', [UsersController::class, 'showUpdatePasswordView'])->name('users.updatePasswordView');
    Route::put('/user/profile/update', [UsersController::class, 'updateProfile'])->name('users.updateProfile');
    Route::get('/users/chart/data/{id}', [UsersController::class, 'chartData']);
    Route::get('/users/details/counters', [UsersController::class, 'counters'])->name('users.counters');
    Route::delete('/users/{id}/archive', [UsersController::class, 'archive'])->name('users.archive');
    Route::get('/users/archived/data', [UsersController::class, 'archived'])->name('users.archived');
    Route::get('/users/archived/data/json', [UsersController::class, 'archivedUsers'])->name('archived-users.json');
    Route::put('/users/{id}/restore', [UsersController::class, 'restore'])->name('users.restore');
    Route::get('/user/{user_id}/contract-dt', [UsersController::class, 'userContractsDatatable'])->name('user-contracts-dt');
    Route::post('/user/avatar/upload', [UsersController::class, 'uploadAvatar'])->name('user.updateAvatar');
    Route::get('/projects/archived', [ProjectsController::class, 'archived'])->name('projects.archived');
    Route::get('/projects/archived/data/json', [ProjectsController::class, 'archivedProjects'])->name('archived-projects.json');
    Route::delete('/projects/{id}/archive', [ProjectsController::class, 'archive'])->name('projects.archive');
    Route::put('/projects/{id}/restore', [ProjectsController::class, 'restore'])->name('projects.restore');
    Route::resource('projects', ProjectsController::class);
    Route::get('/projects/{id}/members', [ProjectsController::class, 'projectMembers'])->name('projects.members');
    Route::get('/projects/data/json', [ProjectsController::class, 'projects'])->name('projects.json');
    Route::get('/projects/members/data/json/{id}', [ProjectsController::class, 'members'])->name('members.json');
    Route::get('/projects/revenue/{id}', [ProjectsController::class, 'revenueReport'])->name('projects.revenue');
    Route::group(['middleware' => 'permission:assign_member'], function () {
        Route::post('/projects/assign/{id}', [ProjectsController::class, 'assign'])->name('projects.assign');
    });
    Route::group(['middleware' => 'permission:remove_member'], function () {
        Route::delete('/projects/remove/{project_id}/{member_id}', [ProjectsController::class, 'remove'])->name('projects.remove');
    });
    Route::group(['middleware' => 'permission:deliver_project'], function () {
        Route::post('/projects/mark/completed/{id}', [ProjectsController::class, 'deliverProject'])->name('projects.deliver');
    });
    Route::get('/projects/chart/data/{id}', [ProjectsController::class, 'chartData']);
    Route::get('/projects/details/counters', [ProjectsController::class, 'counters'])->name('projects.counters');
    Route::get('/tasks/data/json/{project_id?}', [TasksController::class, 'tasks'])->name('tasks.json');
    Route::get('/tasks', [TasksController::class, 'index'])->name('tasks.index');
    Route::delete('/tasks/{id}', [TasksController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks', [TasksController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}/edit', [TasksController::class, 'edit'])->name('tasks.edit');
    Route::get('/tasks/{id}', [TasksController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/chat/{task_id}', [TasksController::class, 'chat'])->name('tasks.chat');
    Route::post('/tasks/selected/list', [TasksController::class, 'listWithBillableComments'])->name('tasks.list');
    Route::group(['middleware' => 'permission:mark_completed'], function () {
        Route::post('/task/mark/completed/{id}', [TasksController::class, 'markCompleted'])->name('tasks.complete');
    });
    Route::delete('/tasks/{id}/archive', [TasksController::class, 'archive'])->name('task.archive');
    Route::get('/tasks/archived/data', [TasksController::class, 'archived'])->name('tasks.archived');
    Route::get('/task/archived/data/json', [TasksController::class, 'archivedTasks'])->name('archived-tasks.json');
    Route::get('/tasks/details/counters', [TasksController::class, 'counters'])->name('tasks.counters');
    Route::put('/tasks/{id}/restore', [TasksController::class, 'restore'])->name('tasks.restore');
    Route::post('/tasks/store-attachments', [TasksController::class, 'store_attachments'])->name('tasks.attachments.store');
    Route::post('/tasks/show-attachments', [TasksController::class, 'showAttachments'])->name('tasks.attachments.show');
    Route::post('/tasks/attachments/delete', [TasksController::class, 'deleteAttachments'])->name('tasks.attachments.delete');
    Route::resource('contracts', ContractController::class);
    Route::get('/comments/data/json/{id}', [CommentsController::class, 'index'])->name('comments.json');
    Route::get('/comments/{id}/edit', [CommentsController::class, 'edit'])->name('comments.edit');
    Route::post('/comments', [CommentsController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{id}', [CommentsController::class, 'destroy'])->name('comments.destroy');
    Route::get('/attendance/data/json', [AttendanceController::class, 'attendance'])->name('attendance.json');
    Route::resource('attendance', AttendanceController::class);
    Route::resource('leaves', LeavesController::class);
    Route::post('/leaves/process', [LeavesController::class, 'process'])->name('leaves.process');
    Route::resource('attachments', AttachmentsController::class);
    Route::resource('invoices', InvoicesController::class)->except(['edit']);
    Route::get('/invoices/{id}/refresh', [InvoicesController::class, 'regenerateInvoice'])->name('invoices.refresh');
    Route::get('/invoices/data/json', [InvoicesController::class, 'invoices'])->name('invoices.json');
    Route::get('/resend/email/{id}', [InvoicesController::class, 'resendEmail'])->name('resend.email');
    Route::post('/invoice/payment/{id}', [InvoicesController::class, 'addInvoicePayment'])->name('invoice.payment');
    Route::get('/invoice/payments/{id}', [InvoicesController::class, 'invoicePayments'])->name('invoice.payments');
});

Route::get('clearNotification', [ReportsController::class, 'clearNotification']);
Route::get('/cron/process/invoices', [InvoicesController::class, 'process'])->name('invoices.process');
Route::post('/save-contract', [UsersController::class, 'saveContract'])->name('user.save.contract');
Route::post('/signature/upload', [UsersController::class, 'uploadDigitalSignature'])->name('signature.upload');

Route::get('/invite/{encrypted}', [ProjectController::class, 'invite']);
Route::get('/view-pdf', function(){
    return view('livewire.backend.invoice.invoice-pdf');
});

Route::get('dashboard/events', [EventController::class, 'events'])->name('dashboard.events');
