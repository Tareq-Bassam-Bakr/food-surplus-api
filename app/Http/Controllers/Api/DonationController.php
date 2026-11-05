<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    // الجمعية: عرض التبرعات المتاحة
    public function index(Request $request)
    {
        $city = $request->query('city', $request->user()->city);

        $donations = Donation::with('restaurant:id,name,city,phone')
            ->where('city', $city)
            ->where('status', 'available')
            ->where('expiry_time', '>', now())
            ->latest()
            ->get();

        return response()->json($donations);
    }

    // المطعم: عرض تبرعاته فقط
    public function myDonations(Request $request)
    {
        $donations = Donation::with('requests.charity:id,name,phone')
            ->where('restaurant_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($donations);
    }

    // المطعم: إضافة تبرع جديد
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string',
            'description'   => 'nullable|string',
            'quantity'      => 'required|integer|min:1',
            'quantity_unit' => 'nullable|string',
            'expiry_time'   => 'required|date|after:now',
            'image'         => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('donations', 'public');
        }

        $donation = Donation::create([
            'restaurant_id' => $request->user()->id,
            'title'         => $request->title,
            'description'   => $request->description,
            'quantity'      => $request->quantity,
            'quantity_unit' => $request->quantity_unit ?? 'وجبة',
            'expiry_time'   => $request->expiry_time,
            'image'         => $imagePath,
            'city'          => $request->user()->city,
            'status'        => 'available',
        ]);

        return response()->json([
            'message'  => 'تم إضافة التبرع بنجاح',
            'donation' => $donation,
        ], 201);
    }

    // المطعم: حذف تبرع
    public function destroy(Request $request, $id)
    {
        $donation = Donation::where('id', $id)
            ->where('restaurant_id', $request->user()->id)
            ->firstOrFail();

        $donation->delete();

        return response()->json(['message' => 'تم حذف التبرع']);
    }
}