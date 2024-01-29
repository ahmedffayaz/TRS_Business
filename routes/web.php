<?php

use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\LeavesController;
use App\Http\Controllers\NewKnowledgeBaseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ReportsController;

use App\Http\Controllers\TasksController;
use App\Http\Controllers\UsersController;
use App\Livewire\Backend\ClientComponent;
use App\Livewire\Backend\CompanyComponent;
use App\Livewire\Backend\DashboardComponent;
use App\Livewire\Backend\ProjectComponent;
use App\Livewire\Backend\UserComponent;
use Illuminate\Support\Facades\Route;

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

use App\Livewire\Auth\LoginComponent;
use App\Livewire\Auth\RegisterComponent;
use App\Livewire\Auth\ForgotPasswordComponent;
use App\Livewire\Auth\ResetPasswordComponent;
use App\Livewire\Backend\KnowledgeBaseComponent;
use App\Livewire\Backend\PermissionComponent;
use App\Livewire\Backend\RoleComponent;
use App\Livewire\Backend\SettingComponent;
use App\Livewire\Backend\UpdatePasswordComponent;
use App\Livewire\Backend\UserTermsConditionComponent;
use App\Livewire\Backend\UserProfileComponent;

Route::middleware(['guest'])->group(function () {
    Route::get('/', LoginComponent::class);
    Route::get('/login', LoginComponent::class)->name('login');
    Route::get('/register', RegisterComponent::class)->name('register');
    Route::get('/forgot-password', ForgotPasswordComponent::class)->name('password.request');
    Route::post('/logout', [LoginComponent::class, 'logout'])->name('logout');
    Route::get('/password/reset/{token}', ResetPasswordComponent::class)->name('password.reset');
});
Route::get('/dashboard', DashboardComponent::class)->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/user-profile', UserProfileComponent::class)->name('user-profile');

Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::get('/companies', CompanyComponent::class)->name('companies');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/employees', UserComponent::class)->name('employees');
        Route::get('/clients', ClientComponent::class)->name('clients');
        Route::get('projects', ProjectComponent::class)->name('projects');
        Route::get('/roles', RoleComponent::class)->name('roles');
        Route::get('/permissions', PermissionComponent::class)->name('permissions');
        // Route::get('/knowledge-base', KnowledgeBaseComponent::class)->name('knowledge-base');
        Route::resource('knowledge-bases', NewKnowledgeBaseController::class);
        Route::get('update-password', UpdatePasswordComponent::class)->name('update-password');
        Route::get('/user-contracts', UserTermsConditionComponent::class)->name('user-contracts');
        Route::get('/system-setting', SettingComponent::class)->name('system-setting');
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
