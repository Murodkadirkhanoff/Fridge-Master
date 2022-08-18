<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public function freezers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Freezer::class);
    }

    public function blocks(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Block::class, Freezer::class);
    }
}
