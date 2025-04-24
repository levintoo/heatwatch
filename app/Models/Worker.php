<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'height_cm',
        'weight_kg',
    ];

    protected $appends = ['bmi'];

    public function getBmiAttribute(): ?float
    {
        if (! $this->height_cm || ! $this->weight_kg) {
            return null;
        }

        $heightInMeters = $this->height_cm / 100;

        return round($this->weight_kg / ($heightInMeters ** 2), 1);
    }
}
