<?php

namespace App\Models;

use App\Models\City;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RadioStation extends Model
{
    use HasFactory;

    protected $hidden = ["created_at","updated_at"];


    public function getImageAttribute($value){
        return url(Storage::url($value));
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
