<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Current feature update notification version.
     * Bump this each time you add new feature announcements.
     */
    public const NOTIFICATION_VERSION = '2026-03-10';

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user ? array_merge($user->toArray(), [
                    'avatar' => $user->avatar,
                    'is_google_user' => $user->isGoogleUser(),
                    'has_password' => $user->password !== null,
                ]) : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'featureNotification' => $user && $user->notification_dismissed_version !== self::NOTIFICATION_VERSION
                ? [
                    'version' => self::NOTIFICATION_VERSION,
                    'title' => 'Update Fitur Baru! 🎉',
                    'features' => [
                        [
                            'icon' => 'clipboard-list',
                            'title' => 'Perencanaan Transaksi',
                            'description' => 'Fitur baru untuk merencanakan transaksi pemasukan dan pengeluaran di masa depan.',
                            'details' => [
                                'Buat rencana transaksi dengan kategori, jumlah, tanggal, dan catatan',
                                'Tombol "Post" untuk mengirim rencana ke transaksi real',
                                'Modal review sebelum posting — ubah data jika diperlukan',
                                'Filter berdasarkan status (Draft/Posted) dan tipe (Pemasukan/Pengeluaran)',
                                'Edit dan hapus rencana yang masih berstatus Draft',
                            ],
                        ],
                        [
                            'icon' => 'message-square-plus',
                            'title' => 'Saran & Masukan',
                            'description' => 'Berikan feedback dan saran untuk pengembangan aplikasi.',
                            'details' => [
                                'Upload gambar (screenshot/mockup) untuk melengkapi saran',
                                'Pilih fokus: Fitur Baru, Perubahan Tampilan, Performa, Keamanan, dll',
                                'Lihat saran dari semua pengguna',
                            ],
                        ],
                        [
                            'icon' => 'bar-chart-3',
                            'title' => 'Halaman Analitik',
                            'description' => 'Dashboard analitik profesional dengan grafik dan filter lengkap.',
                        ],
                        [
                            'icon' => 'trending-up',
                            'title' => 'Pemasukan per Kategori',
                            'description' => 'Dashboard kini menampilkan breakdown pemasukan berdasarkan kategori.',
                        ],
                    ],
                ]
                : null,
        ];
    }
}
