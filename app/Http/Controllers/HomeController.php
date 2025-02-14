<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Tentang;
use App\Models\ContactInfo;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    // Menampilkan halaman utama
    public function index()
    {
        $recentBerita = Berita::latest()->take(5)->get(); // Mengambil 5 berita terbaru
        return view('home', compact('recentBerita'));
    }

    // Menampilkan halaman tentang kami
    public function tentang()
    {
        $tentang = Tentang::first(); // Ambil data pertama dari tabel 'tentang'
        return view('tentang-kami', compact('tentang'));
    }

    // Menampilkan daftar berita dengan paginasi
    public function berita()
    {
        $berita = Berita::paginate(8); // Ambil berita dengan paginasi
        return view('berita-kami', compact('berita'));
    }

    // Menampilkan halaman galeri
    public function galeri()
    {
        $galeriList = Galeri::paginate(12); // Ambil semua data galeri
        return view('galeri-kami', compact('galeriList')); // Kirim data galeri ke view
    }
    public function kontak()
    {
        $contact = ContactInfo::first(); // Ambil data kontak pertama dari tabel 'contact_info'
        return view('kontak-kami', compact('contact')); // Kirim data 'contact' ke tampilan
    }


    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('login');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek peran user
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard'); // Admin diarahkan ke dashboard admin
            } else {
                return redirect()->route('home'); // User diarahkan ke halaman utama
            }
        }

        // Jika login gagal, kembali ke form login dengan pesan kesalahan
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
