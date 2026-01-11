<?php

namespace App\Models;

use App\Models\RadioStation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RadioStationReport extends Model
{
    use HasFactory;

    public function radioStation()
    {
        return $this->belongsTo(RadioStation::class);
    }
}
