<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Apartment extends Model
{
    protected $fillable = ['title','description','rooms','bathrooms','area','price','city','status','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasmany(ApartmentImages::class);
    }

}
