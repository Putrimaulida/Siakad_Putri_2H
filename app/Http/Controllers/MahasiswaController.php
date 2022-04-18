<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\Mahasiswa; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Kelas; 
 
class MahasiswaController extends Controller 
{ 
   /** 
*	Display a listing of the resource. 
     * 
*	@return \Illuminate\Http\Response 
     */ 
    public function index(Request $request) 
    { 
        //fungsi eloquent menampilkan data menggunakan pagination
        //$mahasiswa = DB::table('mahasiswa')->paginate(4);
        //return view('mahasiswa.index', compact('mahasiswa'));
    
        //$mahasiswa = Mahasiswa::all(); // Mengambil semua isi tabel 
        //yang semula Mahasiswa::all, diubah menjadi with() yang menyetakan relasi 
        $mahasiswa = Mahasiswa::with('kelas')->get();
        $paginate = Mahasiswa::orderBy('Nama', 'asc')->paginate(3);         
        return view('mahasiswa.index', ['mahasiswa' => $mahasiswa,'paginate'=>$paginate]); 
    }

    public function create() 
    { 
        $Kelas = Kelas::all(); //mendapatkan data dari tabel kelas
        return view('mahasiswa.create', ['kelas' => $Kelas]); 
    } 

    public function store(Request $request) 
    { 
 
    //melakukan validasi data 
        $request->validate([ 
            'Nim' => 'required', 
            'Nama' => 'required', 
            'Kelas' => 'required', 
            'Jurusan' => 'required',
            'Foto' => 'required|file|image|mimes:jpeg,png,jpg|max:1024'             
        ]); 
 
        //fungsi eloquent untuk menambah data 
        //Mahasiswa::create($request->all());
        $mahasiswa = new Mahasiswa;
        $mahasiswa->Nim = $request->get('Nim');
        $mahasiswa->Nama = $request->get('Nama'); 
        $mahasiswa->Jurusan = $request->get('Jurusan');
        $mahasiswa->Foto = $request->file('Foto')->store('images', 'public');

        $kelas = new Kelas;
        $kelas->id = $request->get('kelas');

        //fungsi eloquent untuk menambahkan data dengan relasi belongsTo
        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();

        //jika data berhasil ditambahkan, akan kembali ke halaman utama         
        return redirect()->route('mahasiswa.index') 
            ->with('success', 'Mahasiswa Berhasil Ditambahkan'); 
    } 
 
    public function show($Nim) 
    { 
        //menampilkan detail data dengan menemukan/berdasarkan Nim Mahasiswa 
        $Mahasiswa = Mahasiswa::where('nim', $Nim)->first();               
        return view('mahasiswa.detail', compact('Mahasiswa')); 
    } 
 
    public function edit($Nim) 
    { 
        //menampilkan detail data dengan menemukan berdasarkan Nim Mahasiswa untuk diedit 
        //$Mahasiswa = DB::table('mahasiswa')->where('nim', $nim)->first();         
        //return view('mahasiswa.edit', compact('Mahasiswa')); 

        $mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        $kelas = Kelas::all(); //mendapatkan data dari tabel kelas
        return view('mahasiswa.edit', compact('mahasiswa', 'kelas'));
    } 
 
    public function update(Request $request, $Nim) 
    { 
 
    //melakukan validasi data 
    $request->validate([ 
        'Nim' => 'required', 
        'Nama' => 'required', 
        'Kelas' => 'required', 
        'Jurusan' => 'required',
        'Foto' => 'required|file|image|mimes:jpeg,png,jpg|max:1024'           
    ]); 
 
    //fungsi eloquent untuk mengupdate data inputan kita 
    /*Mahasiswa::where('nim', $nim) 
    ->update([ 
        'nim'=>$request->Nim, 
        'nama'=>$request->Nama, 
        'kelas'=>$request->Kelas, 
        'jurusan'=>$request->Jurusan, 
        'email'=>$request->Email,
        'alamat'=>$request->Alamat,
        'tanggal lahir'=>$request->Tanggal_Lahir
    ]);*/
        $mahasiswa = Mahasiswa::with('kelas')->where('nim', $Nim)->first();
        $mahasiswa->Nim = $request->get('Nim');
        $mahasiswa->Nama = $request->get('Nama');
        $mahasiswa->Jurusan = $request->get('Jurusan');
        if($mahasiswa->Foto && file_exists(storage_path('app/public/'. $mahasiswa->Foto))){
            \Storage::delete('public/'. $mahasiswa->Foto);
        }
        $image_name = $request->file('foto')->store('images', 'public');
        $mahasiswa->Foto = $image_name;
        $mahasiswa->save();

        $kelas = new Kelas;
        $kelas->id = $request->get('Kelas');

      //fungsi eloquent untuk mengupdate data dengan relasi belongsTo
      $mahasiswa->kelas()->associate($kelas);
      $mahasiswa->save();


    //jika data berhasil diupdate, akan kembali ke halaman utama 
        return redirect()->route('mahasiswa.index') 
            ->with('success', 'Mahasiswa Berhasil Diupdate'); 
    }

    public function destroy( $Nim) 
    { 
    //fungsi eloquent untuk menghapus data 
    Mahasiswa::find($Nim)->delete();
    return redirect()->route('mahasiswa.index')
        ->with('success', 'Mahasiswa Berhasil Dihapus');
    }
    
    public function search(Request $request)
    {
        $keyword = $request->search;
        $mahasiswa = Mahasiswa::where('Nama', 'like', '%' . $keyword . '%')->paginate(4);
        return view('mahasiswa.index', compact('mahasiswa'));
    }


};

    
   