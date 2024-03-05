<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ZaposleniZvanje;
use App\Models\Angazovanje;

class Katedra extends Model
{
    use HasFactory;

    protected $fillable=[
        "ime",
        "prezime",
        "srednjeSlovo",
        "email",
        "pol",
        "fisBroj",
        "katedra",
        "zvanje",
        "uPenziji",
        "datumPenzije"
    ];

    public function angazovanja()
    {
        return $this->belongsTo(ZaposleniZvanje::class);
    }

    public function zapZvanje()
    {
        return $this->hasMany(Angazovanje::class);
    }

}

?>