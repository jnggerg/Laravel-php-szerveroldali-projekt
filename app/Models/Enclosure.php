<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Enclosure extends Model
{
    use HasFactory;


    /**name [string] – A kifutó elnevezése.
        limit [integer] – A kifutóban elhelyezhető állatok maximális száma.
        feeding_at [time] – A kifutóban elhelyezett állatok napi etetésének időpontja (napi egyszer van etetés mindegyik kifutóban) */
    protected $fillable = [
        'name',
        'limit',
        'feeding_at',
    ];


    public function users(): BelongsToMany{

        return $this->BelongsToMany(User::class);
    }

    public function animals()
    {
        return $this->HasMany(Animal::class);
    }

    protected function casts(): array
    {
        return [
            'limit' => 'integer',
            'feeding_at' => 'datetime',
        ];
    }

}
