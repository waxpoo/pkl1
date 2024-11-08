<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Tentang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Tampilkan halaman dashboard admin
    public function adminDashboard()
    {
        $totalBerita = Berita::count();
        $totalGaleri = Galeri::count();
        $allBerita = Berita::all();
        $allGaleri = Galeri::all();

        return view('admin.dashboard', compact('totalBerita', 'totalGaleri', 'allBerita', 'allGaleri'));
    }

    // TENTANG
    public function editTentangKami(Request $request)
    {
        $tentang = Tentang::first();
        return view('admin.edit-tentang', compact('tentang'));
    }

    public function updateTentangKami(Request $request)
    {
        $request->validate([
            'about_text' => 'required',
            'vision_text' => 'required',
            'mission_text' => 'required',
        ]);

        $tentang = Tentang::first();
        $tentang->update($request->only(['about_text', 'vision_text', 'mission_text']));

        return redirect()->route('admin.dashboard')->with('success', 'Data tentang berhasil diperbarui.');
    }

    // FUNGSI BERITA

    public function createBerita()
    {
        return view('partials.create-berita');
    }

    public function daftarBerita()
    {
        $allBerita = Berita::all(); // Ambil semua berita
        return view('admin.daftar-berita', compact('allBerita'));
    }


    public function storeBerita(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|max:5048',
        ]);

        if ($request->hasFile('gambar')) {
            $imagePath = $request->file('gambar')->store('public/gambar');
            $validated['gambar'] = basename($imagePath);
        }

        Berita::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Berita berhasil ditambahkan.');
    }

    // Metode untuk menampilkan halaman edit
    public function editBerita($id)
    {
        // Cari berita berdasarkan ID
        $berita = Berita::findOrFail($id);

        // Tampilkan halaman edit dengan data berita
        return view('partials.edit-berita', compact('berita'));
    }
    // Metode untuk memperbarui berita
    public function updateBerita(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|max:5048',
        ]);

        if ($request->hasFile('gambar')) {
            $imagePath = $request->file('gambar')->store('public/gambar');
            $validated['gambar'] = basename($imagePath);
        } else {
            $validated['gambar'] = $berita->gambar; // Tetap gunakan gambar yang ada jika tidak ada gambar baru
        }

        $berita->update($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroyBerita(Berita $berita)
    {
        $berita->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Berita berhasil dihapus.');
    }


    // FUNGSI GALERI
    public function createGaleri()
    {
        return view('partials.create-galeri');
    }

    public function daftarGaleri()
    {
        $allGaleri = Galeri::all();
        return view('admin.daftar-galeri', compact('allGaleri'));
    }
    public function showGaleri()
    {
        $galeriItems = Galeri::all();
        return view('admin.galeri-kami', compact('galeriItems'));
    }

    public function storeGaleri(Request $request)
    {
        $validated = $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        $imagePath = $request->file('gambar')->store('public/galeri');
        $validated['gambar'] = basename($imagePath);

        Galeri::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Gambar berhasil ditambahkan.');
    }

    public function editGaleri($id)
    {
        $galeriItem = Galeri::findOrFail($id);
        return view('partials.edit-galeri', compact('galeriItem'));
    }

    public function updateGaleri(Request $request, $id)
    {
        $galeriItem = Galeri::findOrFail($id);

        $validated = $request->validate([
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ]);

        if ($request->hasFile('gambar')) {
            $imagePath = $request->file('gambar')->store('public/galeri');
            $validated['gambar'] = basename($imagePath);
        } else {
            $validated['gambar'] = $galeriItem->gambar;
        }

        $galeriItem->update($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Gambar berhasil diperbarui.');
    }

    public function destroyGaleri(Galeri $galeri)
    {
        $galeri->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Gambar berhasil dihapus.');
    }

    // Fungsi Registrasi dan Login
    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,user',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan login.');
    }
}
