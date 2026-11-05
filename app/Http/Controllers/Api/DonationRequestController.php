<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationRequest;
use Illuminate\Http\Request;

class DonationRequestController extends Controller
{
    // الجمعية: طلب استلام تبرع
    public function store(Request $request, $donationId)
    {
        $donation = Donation::where('id', $donationId)
            ->where('status', 'available')
            ->firstOrFail();

        // تحقق إذا طلبت مسبقاً
        $exists = DonationRequest::where('donation_id', $donationId)
            ->where('charity_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'لقد طلبت هذا التبرع مسبقاً'
            ], 400);
        }

        $donationRequest = DonationRequest::create([
            'donation_id'  => $donationId,
            'charity_id'   => $request->user()->id,
            'status'       => 'pending',
            'requested_at' => now(),
        ]);

        // تحديث حالة التبرع
        $donation->update(['status' => 'pending']);

        return response()->json([
            'message' => 'تم إرسال طلب الاستلام',
            'request' => $donationRequest,
        ], 201);
    }

    // الجمعية: عرض طلباتها
    public function myRequests(Request $request)
    {
        $requests = DonationRequest::with('donation.restaurant:id,name,phone,city')
            ->where('charity_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($requests);
    }

    // المطعم: موافقة أو رفض الطلب
    public function updateStatus(Request $request, $requestId)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $donationRequest = DonationRequest::with('donation')
            ->whereHas('donation', function ($q) use ($request) {
                $q->where('restaurant_id', $request->user()->id);
            })
            ->findOrFail($requestId);

        $donationRequest->update([
            'status'      => $request->status,
            'approved_at' => $request->status === 'approved' ? now() : null,
        ]);

        // تحديث حالة التبرع
        $newStatus = $request->status === 'approved'
            ? 'approved'
            : 'available';

        $donationRequest->donation->update(['status' => $newStatus]);

        return response()->json(['message' => 'تم تحديث الحالة بنجاح']);
    }

    // الجمعية: تأكيد الاستلام
    public function confirmCollection(Request $request, $requestId)
    {
        $donationRequest = DonationRequest::where('id', $requestId)
            ->where('charity_id', $request->user()->id)
            ->where('status', 'approved')
            ->firstOrFail();

        $donationRequest->update([
            'status'       => 'collected',
            'collected_at' => now(),
        ]);

        $donationRequest->donation->update(['status' => 'collected']);

        return response()->json(['message' => 'تم تأكيد الاستلام ']);
    }
}