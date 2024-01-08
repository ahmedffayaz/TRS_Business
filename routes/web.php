<?php

use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\LeavesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('modules.dashboard.index');
})->middleware(['auth' ,'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/employees', [UserController::class, 'index'])->name('employees');
    Route::get('companies', [CompanyController::class, 'index'])->name('companies');
    Route::get('clients', [ClientController::class, 'index'])->name('clients');
    Route::get('projects', [ProjectController::class, 'index'])->name('projects');
    Route::get('roles', [RoleController::class, 'index'])->name('roles');
});

Route::group(['middleware' => ['auth' , 'contract']], function () {
    // Route::get('/dashboard' , [PagesController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/events' , [PagesController::class, 'events'])->name('dashboard.events');
    Route::get('/dashboard/events/attendance' , [PagesController::class, 'attendance'])->name('dashboard.attendance');
    // Companies
    // Route::resource('companies', CompaniesController::class);
    Route::get('/companies/data/json' , [CompaniesController::class, 'companies'])->name('companies.json');

    Route::get('/company' , [CompaniesController::class, 'getCompany'])->name('company.json');
    // Roles & permissions
    // Route::resource('roles' , RolesController::class);
    Route::get('/roles/data/json' , [RolesController::class, 'roles'])->name('roles.json');
    Route::group(['middleware' => 'permission:view_permissions'], function () {
        Route::get('/permissions/{role_id}' , [PermissionsController::class, 'index'])->name('permissions.index');
    });

    Route::resource('contracts' , ContractController::class);
    Route::get('/contracts/data/json' , [ContractController::class, 'contracts'])->name('contracts.json');
    // Users
    Route::resource('users' , UsersController::class);
    Route::get('/users/data/json' , [UsersController::class, 'users'])->name('users.json');
    Route::get('/users/update/password' , [UsersController::class, 'showUpdatePasswordView'])->name('users.updatePasswordView');
    Route::post('/users/update/password' , [UsersController::class, 'updatePassword'])->name('users.updatePassword');

    Route::get('/user/profile' , [UsersController::class, 'showProfileView'])->name('users.showProfileView');
    Route::put('/user/profile/update' , [UsersController::class, 'updateProfile'])->name('users.updateProfile');
    Route::get('/users/chart/data/{id}' , [UsersController::class, 'chartData']);
    //user active records
    Route::get('/users/details/counters' , [UsersController::class, 'counters'])->name('users.counters');
    Route::delete('/users/{id}/archive' , [UsersController::class, 'archive'])->name('users.archive');
    Route::get('/users/archived/data' , [UsersController::class, 'archived'])->name('users.archived');
    Route::get('/users/archived/data/json' , [UsersController::class, 'archivedUsers'])->name('archived-users.json');
    Route::put('/users/{id}/restore' , [UsersController::class, 'restore'])->name('users.restore');
    //show contract
    Route::get('/user/{user_id}/user-contracts' , [UsersController::class, 'getContracts'])->name('user-contracts');
    Route::get('/user/{user_id}/contract-dt' , [UsersController::class, 'userContractsDatatable'])->name('user-contracts-dt');

    Route::post('/user/avatar/upload' , [UsersController::class, 'uploadAvatar'])->name('user.updateAvatar');
    // Projects & tasks
    Route::get('/projects/archived' , [ProjectsController::class, 'archived'])->name('projects.archived');
    Route::get('/projects/archived/data/json' , [ProjectsController::class, 'archivedProjects'])->name('archived-projects.json');
    // Archive project
    Route::delete('/projects/{id}/archive' , [ProjectsController::class, 'archive'])->name('projects.archive');
    // Restore project
    Route::put('/projects/{id}/restore' , [ProjectsController::class, 'restore'])->name('projects.restore');
    Route::resource('projects' , ProjectsController::class);
    Route::get('/projects/{id}/members' , [ProjectsController::class, 'projectMembers'])->name('projects.members');
    Route::get('/projects/data/json' , [ProjectsController::class, 'projects'])->name('projects.json');
    Route::get('/projects/members/data/json/{id}' , [ProjectsController::class, 'members'])->name('members.json');
    Route::get('/projects/revenue/{id}' , [ProjectsController::class, 'revenueReport'])->name('projects.revenue');
    Route::group(['middleware' => 'permission:assign_member'], function () {
        Route::post('/projects/assign/{id}' , [ProjectsController::class, 'assign'])->name('projects.assign');
    });
    Route::group(['middleware' => 'permission:remove_member'], function () {
        Route::delete('/projects/remove/{project_id}/{member_id}' , [ProjectsController::class, 'remove'])->name('projects.remove');
    });
    Route::group(['middleware' => 'permission:deliver_project'], function () {
        Route::post('/projects/mark/completed/{id}' , [ProjectsController::class, 'deliverProject'])->name('projects.deliver');
    });
    Route::get('/projects/chart/data/{id}' , [ProjectsController::class, 'chartData']);
    Route::get('/projects/details/counters' , [ProjectsController::class, 'counters'])->name('projects.counters');
    // Tasks
    Route::get('/tasks/data/json/{project_id?}' , [TasksController::class, 'tasks'])->name('tasks.json');
    Route::get('/tasks' , [TasksController::class, 'index'])->name('tasks.index');
    Route::delete('/tasks/{id}' , [TasksController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks' , [TasksController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}/edit' , [TasksController::class, 'edit'])->name('tasks.edit');
    Route::get('/tasks/{id}' , [TasksController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/chat/{task_id}' , [TasksController::class, 'chat'])->name('tasks.chat');
    Route::post('/tasks/selected/list' , [TasksController::class, 'listWithBillableComments'])->name('tasks.list');
    Route::group(['middleware' => 'permission:mark_completed'], function () {
        Route::post('/task/mark/completed/{id}' , [TasksController::class, 'markCompleted'])->name('tasks.complete');
    });
    Route::delete('/tasks/{id}/archive' , [TasksController::class, 'archive'])->name('task.archive');
    Route::get('/tasks/archived/data' , [TasksController::class, 'archived'])->name('tasks.archived');
    Route::get('/task/archived/data/json' , [TasksController::class, 'archivedTasks'])->name('archived-tasks.json');
    Route::get('/tasks/details/counters' , [TasksController::class, 'counters'])->name('tasks.counters');
    Route::put('/tasks/{id}/restore' , [TasksController::class, 'restore'])->name('tasks.restore');
    Route::post('/tasks/store-attachments' , [TasksController::class, 'store_attachments'])->name('tasks.attachments.store');
    Route::post('/tasks/show-attachments' , [TasksController::class, 'showAttachments'])->name('tasks.attachments.show');
    Route::post('/tasks/attachments/delete' , [TasksController::class, 'deleteAttachments'])->name('tasks.attachments.delete');

    // Contract
    Route::resource('contracts', ContractController::class);
    // Comments
    Route::get('/comments/data/json/{id}' , [CommentsController::class, 'index'])->name('comments.json');
    Route::get('/comments/{id}/edit' , [CommentsController::class, 'edit'])->name('comments.edit');
    Route::post('/comments' , [CommentsController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{id}' , [CommentsController::class, 'destroy'])->name('comments.destroy');
    // Attendance
    Route::get('/attendance/data/json' , [AttendanceController::class, 'attendance'])->name('attendance.json');
    Route::resource('attendance' , AttendanceController::class);
    // Leaves
    Route::resource('leaves' , LeavesController::class);
    Route::post('/leaves/process' , [LeavesController::class, 'process'])->name('leaves.process');
    // Attachments
    Route::resource('attachments' , AttachmentsController::class);
    // Invoices
    Route::resource('invoices' , InvoicesController::class)->except(['edit']);
    Route::get('/invoices/{id}/refresh' , [InvoicesController::class, 'regenerateInvoice'])->name('invoices.refresh');
    Route::get('/invoices/data/json' , [InvoicesController::class, 'invoices'])->name('invoices.json');
    // resend email in Invoice Module
    Route::get('/resend/email/{id}' , [InvoicesController::class, 'resendEmail'])->name('resend.email');
    Route::post('/invoice/payment/{id}' , [InvoicesController::class, 'addInvoicePayment'])->name('invoice.payment');
    Route::get('/invoice/payments/{id}' , [InvoicesController::class, 'invoicePayments'])->name('invoice.payments');

    // Faqs
    Route::resource('knowledgebase' , KnowledgeBaseController::class);
    Route::get('/knowledgebase/data/json' , [KnowledgeBaseController::class, 'knowledgeBase'])->name('knowledge_base.json');
    Route::get('/knowledgebase/admin/data' , [KnowledgeBaseController::class, 'AdminKb'])->name('knowledgebase.admin');
    Route::get('/knowledgebase/admin/data/json' , [KnowledgeBaseController::class, 'AdminRecord'])->name('admin-record.json');
    Route::get('/knowledgebase/details/counters' , [KnowledgeBaseController::class, 'counters'])->name('knowledgebase.counters');

    // clear all notifications
    Route::get('/clearAllNotifications' , [NotificationController::class, 'clearAllNotifications'])->name('notifications.clear');
});

Route::get('clearNotification' , [ReportsController::class, 'clearNotification']);

// Cron jobs
Route::get('/cron/process/invoices' , [InvoicesController::class, 'process'])->name('invoices.process');

// save contract
Route::post('/save-contract' , [UsersController::class, 'saveContract'])->name('user.save.contract');

// image pad
Route::post('/signature/upload' , [UsersController::class, 'uploadDigitalSignature'])->name('signature.upload');

Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    // system
    Route::get('system-create' , [SettingsController::class, 'create'])->name('system.create');
    Route::post('system' , [SettingsController::class, 'store'])->name('system.store');
});

require __DIR__ . '/auth.php';
