<?php

namespace App\Http\Controllers;

use App\Models\desa;
use App\Models\wilayah;
use App\Models\Pengguna;
use App\Models\kecamatan;
use App\Models\dokterhewan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DokterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $photo= $user->avatar;
        if ($photo != null) {
            $photo = 'storage/'.$user->avatar;
            return view('dokter.dashboard', compact('user','photo'));
        }
        $photo = '/images/defaultprofile.png';
        return view('dokter.dashboard', compact('user','photo'));
    }

    public function profil(Request $request) {
        $user = Auth::user();
        $aktor = dokterhewan::with('pengguna','puskeswan')->where('id_pengguna', $user->id)->first();
        $kecamatan = kecamatan::all();
        $desa = desa::all();

        // dd($aktor);

        $photo = $user->avatar;
        if ($photo != null) {
            $photo = 'storage/'.$user->avatar;
            return view('dokter.profil',compact('user','photo','kecamatan','desa'));
        } 
            $photo = '/images/defaultprofile.png';
        return view('dokter.profil',compact('user','aktor','photo','kecamatan','desa'));
    }

    public function saveprofil(Request $request) {
        $user = Auth::user();
        
        $aktor = dokterhewan::with('pengguna', 'alamat.wilayah.kecamatan', 'alamat.wilayah.desa')->where('id_pengguna', $user->id)->first();
        
        $request->validate([
            'alamat' => 'required|string|max:255',
            'kecamatan' => 'required',
            'desa' => 'required',
            'dusun' => 'required|string',
            'telepon' => 'required|string|max:20',
            'nama_pengguna' => 'required|string|max:255',
            'password' => 'required|string|min:5',
            'file_input' => 'image|mimes:jpeg,png,jpg,gif,svg'
            ],
            
            [
                
                'alamat' => [ 'required' => 'Alamat wajib diisi.', 'string' => 'Alamat harus berupa string.', 'max' => 'Alamat maksimal : 255 karakter.', ], 
                
                'kecamatan' => [ 'required' => 'Kecamatan wajib diisi.', 'string' => 'Kecamatan harus berupa string.', ], 
                
                'desa' => [ 'required' => 'Desa wajib diisi.', 'string' => 'Desa harus berupa string.', ], 
                
                'dusun' => [ 'required' => 'Dusun wajib diisi.', 'string' => 'Dusun harus berupa string.', ], 
                
                'telepon' => [ 'required' => 'No. Telepon wajib diisi.', 'string' => 'No. Telepon harus berupa string.', 'max' => 'No. Telepon maksimal : 20 karakter.', 'unique' => 'No. Telepon sudah terdaftar.', ], 
                
                'nama_pengguna' => [ 'required' => 'Nama Pengguna wajib diisi.', 'string' => 'Nama Pengguna harus berupa string.', 'max' => 'Nama Pengguna maksimal : 255 karakter.', 'unique' => 'Nama Pengguna sudah terdaftar.', ], 
                
                'password' => [ 'required' => 'Kata Sandi wajib diisi.', 'string' => 'Kata Sandi harus berupa string.', 'min' => 'Kata Sandi minimal 5 karakter.', 'confirmed' => 'Konfirmasi Kata Sandi tidak sesuai.'],
                
                'file_input' => ['image' => 'File harus berupa gambar.'],
                'billing_same' => 'accepted',
            ]);
        $wilayah = wilayah::where('id_kecamatan',$request->kecamatan)->where('id_desa',$request )->first();
        // dd($aktor->pengguna->nama_pengguna);
        // dd($request->nama_pengguna);
        $aktor->alamat->jalan = $request->alamat;
        // $aktor->alamat->wilayah->id_kecamatan = $request->kecamatan;
        // $aktor->alamat->wilayah->id_desa = $request->desa;
        $aktor->alamat->dusun = $request->dusun;

        $aktor->telepon = $request->telepon;
        $aktor->pengguna->nama_pengguna = $request->nama_pengguna;
        $aktor->pengguna->password = Hash::make($request->password);
        dd($request);
        $aktor->save();

        return view('dokter.profil',compact('user','aktor','kecamatan','desa','dusun','kec','des','dus','photo'))->with('success', 'Profil berhasil diperbarui.');
    }

    public function konsultasi(Request $request)
    {
        $user = Auth::user();
        $photo = $user->avatar;
        // dd($user);
        // $user = $request->session()->get('user');
        // $request->session()->get('user');
        // dd($request);

        $aktor = dokterhewan::with('pengguna', 'alamat')->where('id_pengguna', $user->id)->first();
        // dd($aktor);
        $data["friends"] = pengguna::whereNot("id", $user->id)->get();
        if ($photo != null) {
            $photo = 'storage/'.$user->avatar;
            return view('dokter.konsultasi',compact('user','data','aktor','photo') );
        }
        $photo = '/images/defaultprofile.png';
        return view('dokter.konsultasi',compact('user','data','aktor','photo') );
    }
}