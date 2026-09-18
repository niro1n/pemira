<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_404_error_page_renders_custom_view(): void
    {
        $response = $this->get('/halaman-yang-tidak-ada-dalam-sistem');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Halaman tidak ditemukan');
        $response->assertSee('Halaman yang kamu cari mungkin sudah dipindahkan, dihapus, atau alamatnya tidak benar.');
        $response->assertSee('KEMBALI KE BERANDA');
    }

    public function test_403_error_page_renders_custom_view(): void
    {
        $voter = User::factory()->create([
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($voter)->get('/admin');

        $response->assertStatus(403);
        $response->assertSee('403');
        $response->assertSee('Akses ditolak');
        $response->assertSee('Kamu tidak memiliki izin untuk mengakses halaman ini.');
        $response->assertSee('KEMBALI KE BERANDA');
    }

    public function test_419_error_view_renders_correctly(): void
    {
        $view = $this->view('errors.419');

        $view->assertSee('419');
        $view->assertSee('Halaman sudah kedaluwarsa');
        $view->assertSee('Sesi kamu sudah tidak berlaku. Silakan kembali dan coba lagi.');
        $view->assertSee('KEMBALI KE LOGIN');
    }

    public function test_429_error_view_renders_correctly(): void
    {
        $view = $this->view('errors.429');

        $view->assertSee('429');
        $view->assertSee('Terlalu banyak percobaan');
        $view->assertSee('Kamu melakukan terlalu banyak permintaan dalam waktu singkat. Silakan tunggu beberapa saat sebelum mencoba lagi.');
        $view->assertSee('KEMBALI KE BERANDA');
    }

    public function test_500_error_view_renders_correctly(): void
    {
        $view = $this->view('errors.500');

        $view->assertSee('500');
        $view->assertSee('Terjadi kesalahan');
        $view->assertSee('Terjadi masalah pada sistem. Silakan coba lagi beberapa saat.');
        $view->assertSee('KEMBALI KE BERANDA');
    }

    public function test_503_error_view_renders_correctly(): void
    {
        $view = $this->view('errors.503');

        $view->assertSee('503');
        $view->assertSee('PEMIRA sedang tidak tersedia');
        $view->assertSee('Layanan sedang dalam proses pemeliharaan. Silakan coba lagi beberapa saat.');
        $view->assertSee('COBA LAGI');
    }
}
