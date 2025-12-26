<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApartmentRequest;

use App\Http\Resources\ApartmentResource;

use App\Http\Requests\FilterRequest;

use App\Models\Apartment;
use App\Http\Requests\UpdateApartmentRequest;
use App\Models\ApartmentImages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Models\User;

class ApartmentController extends Controller
{

    use AuthorizesRequests;
    public function createApartments(ApartmentRequest $request)
    {

        $this->authorize('create', Apartment::class);
        $user = Auth::user();
        $apartment = Apartment::create(array_merge(
            $request->except('images'),
            ['owner_id' => $user->id]
        ));


        if ($request->hasFile('images')) {
            $folder = "apartments/{$user->id}/{$apartment->id}";
            foreach ($request->file('images') as $image) {
                // $filename = time() . '_' . $image->getClientOriginalName();
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
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
        $this->authorize('destroy', $apartment);

        $path = "apartments/{$user->id}/{$apartment->id}";

        if (Storage::disk('public')->exists($path)) {
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

        $this->authorize('update', $apartment);

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

                // $filename = time() . '_' . $image->getClientOriginalName();
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs($folder, $filename, 'public');

                $apartment->images()->create([
                    'apartment_id' => $apartment->id,
                    'apartment_image_path' => $path
                ]);
            }
        }

        $apartment->update($request->validated());

        return new ApartmentResource($apartment->load('images'));
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
        return ApartmentResource::collection($apartments);
    }

    //---- IN ALL THIS FUNCTIONS YOU SHOULD BE ACCEPTED IN THE APP (have token) -------

    //======== show all apartments in the app (to all users) =============
    public function showAllApartments()
    {
        $apartments = Apartment::with(['images:id,apartment_id,apartment_image_path'])
            ->latest()
            ->paginate(8);

        return ApartmentResource::collection($apartments);
    }

    //==========show apartment from the id + need to be your apartment ============
    public function showIdApartment(Apartment $apartment)
    {

        $this->authorize('showId', $apartment);

        return new ApartmentResource($apartment->load('images'));
    }

    //=========show the apartments to any user from his id + dont need to be your apartments =======
    public function usersApartments($userId)
    {
        $targetUser = User::findOrFail($userId);

        $apartments = $targetUser->apartments()
            ->with('images:id,apartment_id,apartment_image_path')
            ->latest()
            ->paginate(8);

        return ApartmentResource::collection($apartments);
    }

    //show all apartments to the user only
    public function myApartments(Request $request)
    {
        $user = $request->user();

        $apartments = $user->apartments()
            ->with(['images:id,apartment_id,apartment_image_path'])
            ->latest()
            ->paginate(8);

        return ApartmentResource::collection($apartments);
    }

    //=====for test to delete user + his files =======================
    // public function deleteUser(User $user)
    // {

    //     $user->delete();

    //     return response()->json([
    //         'message'=> 'User deleted'
    //     ]);
    // }
    //==========================================================

}
