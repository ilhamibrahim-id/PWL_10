<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use App\Models\Mahasiswa_Matakuliah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;
class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mahasiswa=Mahasiswa::with('kelas')->get();
        // Mengambil semua isi tabel
        $paginate=Mahasiswa::orderBy('nim','asc')->paginate(3);
        return view('mahasiswas.index',['mahasiswa'=>$mahasiswa,'posts'=>$paginate]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('mahasiswas.create',['kelas' => $kelas]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
        'nim'=>'required'
        ,'nama'=>'required'
        ,'kelas'=>'required'
        ,'jurusan'=>'required'
        ,'no_handphone'=>'required'
        ,'email'=>'required'
        ,'tanggal_lahir'=>'required'
        ]);
        if($request->file('foto')) {
            $photo_name = $request->file('foto')->store('fotos', 'public');
        }
        $kelas = Kelas::find($request->get('kelas'));


        $mahasiswa = new Mahasiswa();
        $mahasiswa->nim = $request->get('nim');
        $mahasiswa->nama = $request->get('nama');
        $mahasiswa->foto = $photo_name;
        $mahasiswa->jurusan = $request->get('jurusan');
        $mahasiswa->no_handphone = $request->get('no_handphone');
        $mahasiswa->email = $request->get('email');
        $mahasiswa->tanggal_lahir = $request->get('tanggal_lahir');
        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();


        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($nim)
    {
        $mahasiswa=Mahasiswa::with('kelas')->where('nim',$nim)->first();
        return view('mahasiswas.detail',['mahasiswa'=> $mahasiswa]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($nim)
    {
        $mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        $kelas = Kelas::all();
        return view('mahasiswas.edit',compact('mahasiswa','kelas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $nim)
    {
        $request->validate(['nim'=>'required'
        ,'nama'=>'required'
        ,'kelas'=>'required'
        ,'jurusan'=>'required'
        ,'no_handphone'=>'required'
        ,'email'=>'required'
        ,'tanggal_lahir'=>'required'
        ]);
        $kelas = Kelas::find($request->get('kelas'));
        $mahasiswa = Mahasiswa::find($nim);
        if($mahasiswa->foto && file_exists( storage_path('app/public/' . $mahasiswa->foto))){
            Storage::delete('public/' . $mahasiswa->foto);
        }
        $photo_name = $request->file('foto')->store('fotos','public');
        $mahasiswa->foto = $photo_name;
        $mahasiswa->nim = $request->get('nim');
        $mahasiswa->nama = $request->get('nama');
        $mahasiswa->jurusan = $request->get('jurusan');
        $mahasiswa->no_handphone = $request->get('no_handphone');
        $mahasiswa->email = $request->get('email');
        $mahasiswa->tanggal_lahir = $request->get('tanggal_lahir');
        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();
        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($nim)
    {
        Mahasiswa::find($nim)->delete();
        return redirect()->route('mahasiswa.index')->with('success','Mahasiswa Berhasil  Dihapus');
    }
    public function cari(Request $request)
	{
		$mahasiswa=Mahasiswa::where('nim',$request->nim)->first();
        return view('mahasiswas.cari',compact('mahasiswa'));

	}
    public function nilai($nim)
    {
        $mahasiswa = Mahasiswa::with('kelas', 'matakuliah')->find($nim);
        return view('mahasiswas.nilai', compact('mahasiswa'));
    }
    public function cetak_pdf($nim){
        $mahasiswa = Mahasiswa::find($nim);

        $pdf= PDF::loadview('mahasiswas.cetak', ['mahasiswa' => $mahasiswa]);
        return $pdf->stream();
    }
}
