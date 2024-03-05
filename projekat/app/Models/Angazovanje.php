<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Katedra;
use App\Models\Zaposleni;

class Angazovanje extends Model
{
    use HasFactory;

    protected $fillable=[
        "datumOd",
        "datumDo",
        "pozicija",
    ];

    public function katedre()
    {
        return $this->belongsTo(Zaposleni::class);
    }

    public function zapZvanje()
    {
        return $this->hasMany(Katedra::class);
    }

}

?>