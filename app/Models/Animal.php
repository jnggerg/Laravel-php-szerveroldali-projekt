<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'species',
        'is_predator',
        'born_at',
        'deleted_at',
        'kep',
        /**name [string] – Az állat neve.
        species [string] – Az állat faja.
        is_predator [boolean] – Az állat ragadozó-e.
        born_at [timestamp] – Az állat születésének időpontja.
        deleted_at [timestamp] – Az állat archiválásának időpontja (soft delete).
        Kép az állatról (nullable). */
    ];

    public function enclosure(){
        return $this->belongsTo(Enclosure::class);
    }

    protected function casts(): array
    {
        return [
            'is_predator' => 'boolean',
            'born_at' => 'datetime',
            'deleted_at' => 'timestamp',
        ];

    }

}
