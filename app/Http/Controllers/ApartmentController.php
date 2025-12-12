<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApartmentRequest;
use App\Http\Requests\FilterRequest;
use App\Models\Apartment;
use App\Models\ApartmentImages;
use Illuminate\Support\Facades\Auth;

class ApartmentController extends Controller
{
    public function store(ApartmentRequest $request)
    {

        $user = Auth::user();
        $apartment = Apartment::create(array_merge(
            $request->except('images'),
            ['user_id' => $user->id]
        ));

        if ($request->hasFile('images')) {
            $folder = "apartments/{$apartment->id}";

            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs($folder, $filename, 'public');

                ApartmentImages::create([
                    'apartment_id' => $apartment->id,
                    'apartment_image_path' => $path
                ]);
            }
        }

        //================================================
        $apartmentWithImages = $apartment->load('images')->toArray();
        $apartmentWithImages['images'] = collect($apartmentWithImages['images'])->map(function ($img) {
            return [
                'id' => $img['id'],
                'url' => asset('storage/' . $img['apartment_image_path'])
            ];
        });

        return response()->json($apartmentWithImages, 200);
    }

    public function filtering(FilterRequest $request)
    {
        $min_price = $request->min_price ?? 0;
        $max_price = $request->max_price ?? 1000000;
        $min_rooms = $request->min_rooms ?? 0;
        $max_rooms = $request->max_rooms ?? 20;
        $min_bathrooms = $request->min_bathrooms ?? 0;
        $max_bathrooms = $request->max_bathrooms ?? 20;
        $min_area = $request->min_area ?? 0;
        $max_area = $request->max_area ?? 6000;
        $city = $request->city ?? null;

        $query = Apartment::query();
        if ($city !== null) {
            $query->where('city', $city);
        }
        $query->whereBetween('price', [$min_price, $max_price]);
        $query->whereBetween('rooms', [$min_rooms, $max_rooms]);
        $query->whereBetween('bathrooms', [$min_bathrooms, $max_bathrooms]);
        $query->whereBetween('area', [$min_area, $max_area]);

        $apartments = $query->paginate(8);
        return response()->json($apartments, 200);
    }
}
