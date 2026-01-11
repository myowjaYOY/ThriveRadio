<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $hidden = ["created_at","updated_at"];

    public function radiostations()
    {
        return $this->hasMany(RadioStation::class);
    }

    public function getImageAttribute($value){
        return url(Storage::url($value));
    }
}
