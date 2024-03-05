<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ZaposleniZvanje;

class Zvanje extends Model
{
    use HasFactory;

    protected $fillable=[
        "naziv",
        "nivo"
    ];

    public function zaposleniZvanja()
    {
        return $this->hasMany(ZaposleniZvanje::class);
    }
}

?>