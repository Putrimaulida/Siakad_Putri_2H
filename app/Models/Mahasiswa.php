<?php 
 
namespace App\Models; 
 
//use Illuminate\Contracts\Auth\MustVerifyEmail;  
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model; 
//use Illuminate\Foundation\Auth\Mahasiswa as Authenticatable;  
//use Illuminate\Notifications\Notifiable;
use App\Models\Mahasiswa;  
//use Illuminate\Database\Eloquent\Model; //Model Eloquent 
 
class Mahasiswa extends Model 
{ 
    protected $table='mahasiswa'; // Eloquent akan membuat model mahasiswa menyimpan record di tabel mahasiswa     
    protected $primaryKey = 'nim'; // Memanggil isi DB Dengan primarykey 
    /** 
*	The attributes that are mass assignable. 
     * 
*	@var array 
     */     
    protected $fillable = [ 
        'nim', 
        'nama', 
        'kelas_id', 
        'jurusan',
        'email',
        'alamat',
        'tanggal_lahir',
    ];
    
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}; 
