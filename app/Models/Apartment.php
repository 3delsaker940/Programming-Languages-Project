<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Apartment extends Model
{
    protected $fillable = ['title','description','rooms','bathrooms','area','price','city','status','owner_id'];

    public function user()
    {
        return $this->belongsTo(User::class,'owner_id');
    }

    public function images()
    {
        return $this->hasmany(ApartmentImages::class);
    }

    // protected static function booted()
    // {
    //     static::deleting(function ($apartment) {

    //         if (!empty($apartment->images)) {
    //             foreach ($apartment->images as $path) {
    //                 Storage::disk('public')->delete($path);
    //             }
    //         }

    //         // إذا الصور مخزنة داخل مجلد خاص بالشقة
    //         // مثال: apartments/{id}/image.jpg
    //         $folder = "apartments/{$apartment->id}";
    //         Storage::disk('public')->deleteDirectory($folder);
    //     });
    // }


}
