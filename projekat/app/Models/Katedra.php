<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Glavni;
use App\Models\Angazovanje;

class Katedra extends Model
{
    use HasFactory;

    protected $fillable=[
        "naziv",
        "aktivno"
    ];

    public function glavni()
    {
        return $this->hasMany(Glavni::class);
    }

    public function angazovanje()
    {
        return $this->belongsTo(Angazovanje::class);
    }

}

?>