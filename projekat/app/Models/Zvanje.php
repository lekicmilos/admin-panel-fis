<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Zvanje extends Model
{
    use HasFactory;

    private const table_name = 'zvanje';
    protected $table = self::table_name;

    protected $fillable = [
        'naziv_zvanja',
        'nivo'
    ];

    public function zaposleni() : BelongsToMany
    {
        return $this->belongsToMany(Zaposleni::class,
        'izbor_u_zvanje',
        'zvanje_id',
        'zaposleni_id')
            ->withPivot('datum_od', 'datum_do');
    }
}
