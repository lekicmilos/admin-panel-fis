<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Katedra;

class Glavni extends Model
{
    use HasFactory;

    protected $fillable=[
        "pozicija",
        "datumOd",
        "datumDo"
    ];

    public function katedra()
    {
        return $this->belongsTo(Katedra::class);
    }
}

?>