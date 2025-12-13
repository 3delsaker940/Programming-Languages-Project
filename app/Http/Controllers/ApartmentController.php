<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApartmentRequest;
use App\Models\Apartment;
use App\Models\ApartmentImages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApartmentController extends Controller
{
    public function store(ApartmentRequest $request)
    {
        $this->authorize('create', Apartment::class);
        $user = Auth::user();
        $apartment = Apartment::create(array_merge(
            $request->except('images'),
            ['owner_id' => $user->id]
        ));

        if($request->hasFile('images'))
            {
                $folder = "apartments/{$apartment->id}";

                foreach($request->file('images') as $image)
                    {
                        $filename = time().'_'.$image->getClientOriginalName();
                        $path = $image->storeAs($folder, $filename, 'public');

                        ApartmentImages::create([
                            'apartment_id' => $apartment->id,
                            'apartment_image_path' => $path
                        ]);
                    }
            }

        //================================================
        $apartmentWithImages = $apartment->load('images')->toArray();
        $apartmentWithImages['images'] = collect($apartmentWithImages['images'])->map(function ($img){
            return [
                'id' => $img['id'],
                'url' => asset('storage/' . $img['apartment_image_path'])
            ];
        });

        return response()->json($apartmentWithImages);
    }
}
