<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AksesMenuController;
use App\Http\Controllers\EvenController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Admin\PerusahaanProfileController;
use App\Http\Controllers\Admin\PerusahaanLokerController;
use App\Http\Controllers\Admin\PerusahaanDashboardController;
use App\Http\Controllers\Admin\PerusahaanPelamarController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\EventRegistrationController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\SystemNotificationController;
use App\Http\Controllers\Admin\KategoriPerusahaanController;
use App\Http\Controllers\Admin\PerusahaanController;
use App\Http\Controllers\Admin\PencariKerjaController;
use App\Http\Controllers\Admin\LowonganKerjaController;
use App\Http\Controllers\Admin\PelamarEventController;
use App\Http\Controllers\Admin\RegistrasiLowonganController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Pelamar\RekomendasiController;
use App\Http\Controllers\PerusahaanRegisterController;

// Company Registration
Route::get('/register-perusahaan', [PerusahaanRegisterController::class, 'showRegistrationForm'])->name('perusahaan.register');
Route::get('/register-perusahaan/success', [PerusahaanRegisterController::class, 'registrationSuccess'])->name('perusahaan.register.success');
Route::post('/register-perusahaan', [PerusahaanRegisterController::class, 'register'])->name('perusahaan.register.post');
Route::get('/perusahaan/activate/{token}', [PerusahaanRegisterController::class, 'activate'])->name('perusahaan.activate');

// Applicant Registration Suite
use App\Http\Controllers\PelamarRegisterController;
Route::get('/register-pelamar', [PelamarRegisterController::class, 'showRegistrationForm'])->name('pelamar.register');
Route::post('/register-pelamar', [PelamarRegisterController::class, 'register'])->name('pelamar.register.post');
Route::get('/activate-pelamar/{token}', [PelamarRegisterController::class, 'activate'])->name('pelamar.activate');

Route::middleware('auth')->group(function () {
    Route::get('/complete-profile', [PelamarRegisterController::class, 'showCompleteDataForm'])->name('pelamar.complete-data');
    Route::post('/complete-profile', [PelamarRegisterController::class, 'storeCompleteData'])->name('pelamar.complete-data.post');
    
    // Applicant Dashboard & Activities
    Route::get('/pelamar/dashboard', [\App\Http\Controllers\Pelamar\DashboardController::class, 'index'])->name('pelamar.dashboard');
    Route::get('rekomendasi', [RekomendasiController::class, 'getRekomendasi'])->name('pelamar.rekomendasi');
    Route::post('/apply-job', [\App\Http\Controllers\Pelamar\ApplyController::class, 'apply'])->name('pelamar.apply-job');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\Pelamar\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/pelamar/absen/{id}', [AbsensiController::class, 'scanAbsen'])->name('pelamar.absen');
    Route::get('/pelamar/kartu/{ideven}', [\App\Http\Controllers\Pelamar\DashboardController::class, 'printCard'])->name('pelamar.print-card');
});

// Location Select API for AJAX
Route::get('/perusahaan/registration/kategoris', [PerusahaanRegisterController::class, 'getKategoris']);
Route::get('/perusahaan/registration/provinsis', [PerusahaanRegisterController::class, 'getProvinsis']);
Route::get('/perusahaan/registration/kotas/{provinsiId}', [PerusahaanRegisterController::class, 'getKotas']);
Route::get('/perusahaan/registration/kecamatans/{kotaId}', [PerusahaanRegisterController::class, 'getKecamatans']);
Route::get('/perusahaan/registration/kelurahans/{kecamatanId}', [PerusahaanRegisterController::class, 'getKelurahans']);
Route::get('/v/p/{encrypted_id}/{ideven}', [\App\Http\Controllers\Pelamar\DashboardController::class, 'showApplicantStatus'])->name('public.applicant.status');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [HomeController::class, 'events'])->name('frontend.events');
Route::get('/event/{id}/lowongan', [HomeController::class, 'eventVacancies'])->name('frontend.event.vacancies');
Route::get('/lowongan-detail/{id}', [HomeController::class, 'vacancyDetail'])->name('vacancy.detail');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/company-validation/{id}', [AdminController::class, 'showValidationDetail'])->name('admin.perusahaan.validation-detail');
    Route::post('/company-validation/{id}/approve', [AdminController::class, 'validateCompany'])->name('admin.perusahaan.approve');
    Route::post('/company-validation/{id}/reject', [AdminController::class, 'rejectCompany'])->name('admin.perusahaan.reject');
    
    // Event Registration Approval
    Route::get('/event-registration/{id}', [AdminController::class, 'showEventRegistrationDetail'])->name('admin.event-registration.detail');
    Route::post('/event-registration/{id}/approve', [AdminController::class, 'approveEventRegistration'])->name('admin.event-registration.approve');
    Route::get('/perusahaan/dashboard', [PerusahaanDashboardController::class, 'index'])->name('admin.perusahaan.dashboard');
    Route::get('/perusahaan/event/{id}/detail', [PerusahaanDashboardController::class, 'eventDetail'])->name('admin.perusahaan.event.detail');
    Route::post('/perusahaan/event/{id}/register', [PerusahaanDashboardController::class, 'registerEvent'])->name('admin.perusahaan.event.register');
    Route::post('/perusahaan/payment/confirm', [PerusahaanDashboardController::class, 'storePayment'])->name('admin.perusahaan.payment.confirm');
    Route::get('/perusahaan/invoice/{id}/download', [PerusahaanDashboardController::class, 'downloadInvoice'])->name('admin.perusahaan.invoice.download');
    // Event Registration Hub (Admin Only)
    Route::get('/pendaftar-event', [EventRegistrationController::class, 'index'])->name('admin.pendaftar-event');
    Route::post('/pendaftar-event/{id}/toggle-aktivasi', [EventRegistrationController::class, 'toggleAktivasi'])->name('admin.pendaftar-event.toggle-aktivasi');
    Route::delete('/pendaftar-event/{id}', [EventRegistrationController::class, 'destroy'])->name('admin.pendaftar-event.destroy');

    // Pelamar Event (khusus list pelamar scope event)
    Route::get('/pelamar/even', [PelamarEventController::class, 'index'])->name('admin.pelamar.even');
    
    // Detailed Registration Hub (Audit & Approval)
    Route::get('/event-registration/{id}', [EventRegistrationController::class, 'showDetail'])->name('admin.event-registration.detail');
    Route::post('/event-registration/{id}/approve', [EventRegistrationController::class, 'approve'])->name('admin.event-registration.approve');
    
    // Kategori Perusahaan Management
    Route::resource('/kategori-perusahaan', KategoriPerusahaanController::class)->names([
        'index' => 'admin.kategori-perusahaan.index',
        'store' => 'admin.kategori-perusahaan.store',
        'update' => 'admin.kategori-perusahaan.update',
        'destroy' => 'admin.kategori-perusahaan.destroy',
    ])->except(['create', 'edit', 'show']); // Handled via Modals in Index for better UX

    // Perusahaan Data Mining & Audit
    Route::get('/perusahaan-data', [PerusahaanController::class, 'index'])->name('admin.perusahaan-data.index');
    Route::post('/perusahaan-data', [PerusahaanController::class, 'store'])->name('admin.perusahaan-data.store');
    Route::get('/perusahaan-data/{id}/edit', [PerusahaanController::class, 'edit'])->name('admin.perusahaan-data.edit');
    Route::post('/perusahaan-data/{id}/update', [PerusahaanController::class, 'update'])->name('admin.perusahaan-data.update');
    Route::post('/perusahaan-data/register-event', [PerusahaanController::class, 'registerEvent'])->name('admin.perusahaan-data.register-event');
    Route::get('/perusahaan-data/{id}', [PerusahaanController::class, 'show'])->name('admin.perusahaan-data.show');
    Route::get('/perusahaan-data/available-events/{idperusahaan}', [PerusahaanController::class, 'getAvailableEvents'])->name('admin.perusahaan-data.available-events');

    // Pencari Kerja Data Mining & Audit
    Route::get('/pencari-kerja', [PencariKerjaController::class, 'index'])->name('admin.pencari-kerja.index');
    Route::get('/pencari-kerja/export', [PencariKerjaController::class, 'export'])->name('admin.pencari-kerja.export');
    Route::get('/pencari-kerja/{id}', [PencariKerjaController::class, 'show'])->name('admin.pencari-kerja.show');
    Route::get('/pencari-kerja/{id}/cv', [PencariKerjaController::class, 'downloadCV'])->name('admin.pencari-kerja.download-cv');
    Route::post('/pencari-kerja/{id}/send-mail', [PencariKerjaController::class, 'sendMail'])->name('admin.pencari-kerja.send-mail');
    
    // Geographic AJAX Endpoints (Talent Focus)
    Route::get('/pencari-kerja/get-cities/{provinceId}', [PencariKerjaController::class, 'getCities']);
    Route::get('/pencari-kerja/get-districts/{cityId}', [PencariKerjaController::class, 'getDistricts']);
    Route::get('/pencari-kerja/get-villages/{districtId}', [PencariKerjaController::class, 'getVillages']);

    // Lowongan Kerja Data Mining & Audit
    Route::get('/lowongan-kerja', [LowonganKerjaController::class, 'index'])->name('admin.lowongan-kerja.index');
    Route::get('/lowongan-kerja/{id}', [LowonganKerjaController::class, 'show'])->name('admin.lowongan-kerja.show');

    // Registrasi Lowongan / Pendaftar Lowongan
    Route::get('/registrasi-lowongan', [RegistrasiLowonganController::class, 'index'])->name('admin.registrasi-lowongan.index');

    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    // Admin Perusahaan Profile
    Route::get('/perusahaan/profile', [PerusahaanProfileController::class, 'index'])->name('admin.perusahaan.profile');
    Route::post('/perusahaan/profile', [PerusahaanProfileController::class, 'update'])->name('admin.perusahaan.profile.update');
    
    // Personal Profile Management
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/perusahaan/profile/document/{id}', [PerusahaanProfileController::class, 'deleteDocument'])->name('admin.perusahaan.document.delete');
    Route::get('/perusahaan/event', [PerusahaanDashboardController::class, 'myEvents'])->name('admin.perusahaan.event');
    Route::get('/perusahaan/event/{id}/my-detail', [PerusahaanDashboardController::class, 'myEventDetail'])->name('admin.perusahaan.event.my-detail');
    Route::get('/perusahaan/dataloker', [PerusahaanLokerController::class, 'index'])->name('admin.perusahaan.loker.index');
    Route::get('/perusahaan/pelamar', [PerusahaanPelamarController::class, 'index'])->name('admin.perusahaan.pelamar.index');
    Route::get('/perusahaan/pelamar/{id}/detail', [PerusahaanPelamarController::class, 'show'])->name('admin.perusahaan.pelamar.show');
    Route::get('/perusahaan/pelamar/{id}/cv', [PerusahaanPelamarController::class, 'downloadCV'])->name('admin.perusahaan.pelamar.download-cv');
    Route::post('/perusahaan/pelamar/{id}/send-mail', [PerusahaanPelamarController::class, 'sendMail'])->name('admin.perusahaan.pelamar.send-mail');
    Route::get('/perusahaan/pelamar/export', [PerusahaanPelamarController::class, 'export'])->name('admin.perusahaan.pelamar.export');
    
    // Lowongan Perusahaan Management (Specific to Event Registration)
    Route::get('/perusahaan/event/{id}/create-loker', [PerusahaanLokerController::class, 'create'])->name('admin.perusahaan.loker.create');
    Route::post('/perusahaan/event/{id}/create-loker', [PerusahaanLokerController::class, 'store'])->name('admin.perusahaan.loker.store');
    Route::post('/perusahaan/event/{id}/import-loker', [PerusahaanLokerController::class, 'import'])->name('admin.perusahaan.loker.import');
    
    // Per-Vacancy Actions
    Route::get('/perusahaan/loker/{id}/edit', [PerusahaanLokerController::class, 'edit'])->name('admin.perusahaan.loker.edit');
    Route::post('/perusahaan/loker/{id}/update', [PerusahaanLokerController::class, 'update'])->name('admin.perusahaan.loker.update');
    Route::get('/perusahaan/loker/{id}/applicants', [PerusahaanLokerController::class, 'showApplicants'])->name('admin.perusahaan.loker.applicants');
    Route::get('/perusahaan/loker/{id}/applicants-ranking', [PerusahaanLokerController::class, 'loadApplicantsRanking'])->name('admin.perusahaan.loker.applicants-ranking');
    Route::get('/perusahaan/loker/{id}/attendance', [PerusahaanLokerController::class, 'attendance'])->name('admin.perusahaan.loker.attendance');
    Route::post('/perusahaan/loker/{id}/attendance', [PerusahaanLokerController::class, 'updateAttendance'])->name('admin.perusahaan.loker.attendance.update');
    Route::post('/perusahaan/loker/{id}/toggle-status', [PerusahaanLokerController::class, 'toggleStatus'])->name('admin.perusahaan.loker.toggle-status');

    // Attendance (Absensi) Management
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('admin.absensi.index');
    Route::get('/absensi/{id}', [AbsensiController::class, 'show'])->name('admin.absensi.show');
    Route::post('/absensi/{id}/manual', [AbsensiController::class, 'manualAbsen'])->name('admin.absensi.manual');

    // Perusahaan Attendance URLs
    Route::get('/perusahaan/absensi', [AbsensiController::class, 'index'])->name('admin.perusahaan.absensi.index');
    Route::get('/perusahaan/absensi/{id}', [AbsensiController::class, 'show'])->name('admin.perusahaan.absensi.show');
    Route::post('/perusahaan/absensi/{id}/manual', [AbsensiController::class, 'manualAbsen'])->name('admin.perusahaan.absensi.manual');

    // Laporan (Reporting) Management
    Route::get('/laporan/pelamar-loker', [LaporanController::class, 'pelamarLoker'])->name('admin.laporan.pelamar-loker');
    Route::get('/laporan/loker-event', [LaporanController::class, 'lokerEvent'])->name('admin.laporan.loker-event');
    Route::get('/laporan/kehadiran', [LaporanController::class, 'kehadiran'])->name('admin.laporan.kehadiran');
    Route::get('/laporan/pelamar-detail', [LaporanController::class, 'pelamarDetail'])->name('admin.laporan.pelamar-detail');

    // System Notifications
    Route::get('/notifications', [SystemNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/notifications/{id}/read', [SystemNotificationController::class, 'read'])->name('admin.notifications.read');
    Route::delete('/notifications/{id}', [SystemNotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::post('/notifications/mark-all-read', [SystemNotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');

    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/users/{id}/update', [UserController::class, 'update'])->name('admin.users.update');
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('admin.users.reset');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->name('admin.roles.show');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

    Route::get('/menu', [MenuController::class, 'index'])->name('admin.menu');
    Route::get('/menu/create', [MenuController::class, 'create'])->name('admin.menu.create');
    Route::post('/menu', [MenuController::class, 'store'])->name('admin.menu.store');
    Route::get('/menu/{id}/edit', [MenuController::class, 'edit'])->name('admin.menu.edit');
    Route::put('/menu/{id}', [MenuController::class, 'update'])->name('admin.menu.update');
    Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('admin.menu.destroy');

    Route::get('/role-menu', [AksesMenuController::class, 'index'])->name('admin.role-menu');
    Route::post('/role-menu', [AksesMenuController::class, 'store'])->name('admin.role-menu.store');

    Route::get('/event', [EvenController::class, 'index'])->name('admin.event');
    Route::post('/event/{id}/toggle-status', [EvenController::class, 'toggleStatus'])->name('admin.event.toggle-status');
    Route::post('/event/{id}/toggle-headline', [EvenController::class, 'toggleHeadline'])->name('admin.event.toggle-headline');
    Route::get('/event/create', [EvenController::class, 'create'])->name('admin.event.create');
    Route::post('/event', [EvenController::class, 'store'])->name('admin.event.store');
    Route::get('/event/{id}', [EvenController::class, 'show'])->name('admin.event.show');
    Route::get('/event/{id}/edit', [EvenController::class, 'edit'])->name('admin.event.edit');
    Route::put('/event/{id}', [EvenController::class, 'update'])->name('admin.event.update');
    Route::get('/event/{id}/sponsor', [EvenController::class, 'sponsor'])->name('admin.event.sponsor');
    Route::post('/event/{id}/sponsor', [EvenController::class, 'storeSponsor'])->name('admin.event.store_sponsor');
    Route::delete('/sponsor/{id}', [EvenController::class, 'destroySponsor'])->name('admin.even.destroy_sponsor');

    Route::get('/register', [RegisterController::class, 'index'])->name('admin.register');
    Route::get('/register/create', [RegisterController::class, 'create'])->name('admin.register.create');
    Route::post('/register', [RegisterController::class, 'store'])->name('admin.register.store');
    Route::post('/register/{id}/toggle-aktivasi', [RegisterController::class, 'toggleAktivasi'])->name('admin.register.toggle-aktivasi');
    Route::delete('/register/{id}', [RegisterController::class, 'destroy'])->name('admin.register.destroy');
});
// Unified Artisan System Maintenance Route
Route::get('/clear-all-cache', function() {
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    return 'Configuration, route, and view cache have been successfully rebuilt and optimized!';
});
