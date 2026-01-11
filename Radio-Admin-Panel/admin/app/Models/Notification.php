<?php

namespace App\Models;

use App\Models\City;
use App\Models\Category;
use App\Models\RadioStation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function radioStation()
    {
        return $this->belongsTo(RadioStation::class);
    }
    public function getImageAttribute($value)
    {
        if($value)
        {
            return url(Storage::url($value));
        }
        return '';
    }
}
