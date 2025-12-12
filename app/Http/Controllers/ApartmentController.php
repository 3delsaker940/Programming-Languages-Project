<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Models\Apartment;
use App\Models\ApartmentImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class ApartmentController extends Controller
{
    public function createApartments(ApartmentRequest $request)
    {

        $this->authorize('create', Apartment::class);
        $user = auth()->user();

        $apartment = Apartment::create(array_merge(
            $request->except('images'),
            ['owner_id' => $user->id]
        ));

        if($request->hasFile('images'))
            {
                
                $folder = "apartments/{$user->id}/{$apartment->id}";

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
        $apartmentWithImages['images'] = collect($apartmentWithImages['images'])->map(function ($img) {
            return [
                'id' => $img['id'],
                'url' => asset('storage/' . $img['apartment_image_path'])
            ];
        });

        return response()->json($apartmentWithImages, 200);
    }

    public function destroyApartments(Request $request, Apartment $apartment)
    {
        $user = $request->user();
        if($apartment->user_id !== $user->id)
            {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }

        $path = "apartments/{$user->id}/{$apartment->id}";
        
        if(Storage::disk('public')->exists($path))
        {
            Storage::disk('public')->deleteDirectory($path);
        }
        
        $apartment->delete();

        return response()->json([
            'message' => 'Apartment Deleted Successfuly.'
        ], 200);
    }

    public function updateApartments(UpdateApartmentRequest $request, Apartment $apartment)
    {
        $user = $request->user();

        if ($apartment->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $folder = "apartments/{$user->id}/{$apartment->id}";

        if ($request->filled('delete_images')) {

            foreach ($request->delete_images as $imageId) {

                $image = $apartment->images()->where('id', $imageId)->first();

                if ($image) {
                    Storage::disk('public')->delete($image->apartment_image_path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {

                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs($folder, $filename, 'public');

                $apartment->images()->create([
                    'apartment_id' => $apartment->id,
                    'apartment_image_path' => $path
                ]);
            }
        }

        $apartment->update($request->validated());

        return response()->json([
            'message' => 'Apartment Updated Successfully',
            'apartment' => $apartment->load('images'),
        ], 200);
    }

    // public function showAllApartments()
    // {
    //     $apartments = Apartment::select('id', 'user_id', 'title','description','price', 'city', 'area', 'rooms', 'bathrooms')
    //     ->with(['images:id,apartment_id,apartment_image_path'])
    //     ->latest()
    //     ->paginate(10);

    //     return response()->json([
    //         'success' => true,
    //         'apartments' => $apartments
    //     ]);
    // }

    public function showIdApartment(Apartment $apartment)
    {
        $apartment->load(['images','user']);

        return response()->json([
            'success' => true,
            'apartment' => $apartment
        ]);
    }

    public function userApartments(Request $request)
    {
        $user = $request->user();

        $apartments = Apartment::select('id', 'user_id', 'title','description','price', 'city', 'area', 'rooms', 'bathrooms')
        ->where('user_id', $user->id)
        ->with(['images:id,apartment_id,apartment_image_path'])
        ->latest()
        ->paginate(10);

        return response()->json([
            'success' => true,
            'apartments' => $apartments
        ]);
    }
}
