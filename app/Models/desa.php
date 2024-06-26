<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class desa extends Model
{
    use HasFactory;

    public $timestamps  = false;

    protected $guarded  = [
        'id'
    ];

    protected $table = 'desa';

    public function wilayah()
    {
        return $this->hasOne(wilayah::class, 'id_desa');
    }
}
