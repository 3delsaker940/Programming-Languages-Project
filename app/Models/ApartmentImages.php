<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentImages extends Model
{
    protected $fillable = ['apartment_id','apartment_image_path'];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}
