<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Donation;
use App\Models\DonationRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // إحصائيات عامة
    public function stats()
    {
        return response()->json([
            'total_restaurants' => User::where('role', 'restaurant')->count(),
            'total_charities'   => User::where('role', 'charity')->count(),
            'pending_users'     => User::where('status', 'pending')->count(),
            'total_donations'   => Donation::count(),
            'active_donations'  => Donation::where('status', 'available')->count(),
            'collected'         => DonationRequest::where('status', 'collected')->count(),
        ]);
    }

    // عرض جميع المستخدمين
    public function users(Request $request)
    {
        $status = $request->query('status');

        $users = User::where('role', '!=', 'admin')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->get();

        return response()->json($users);
    }

    // تغيير حالة المستخدم (موافقة / حظر / رفع حظر)
    public function updateUserStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,banned,pending',
        ]);

        $user = User::findOrFail($id);
        $user->update(['status' => $request->status]);

        $messages = [
            'active'  => 'تمت الموافقة على الحساب ',
            'banned'  => 'تم حظر الحساب ',
            'pending' => 'تم إرجاع الحساب للانتظار',
        ];

        return response()->json([
            'message' => $messages[$request->status],
            'user'    => $user,
        ]);
    }

    // عرض جميع التبرعات
    public function donations()
    {
        $donations = Donation::with('restaurant:id,name,city')
            ->latest()
            ->get();

        return response()->json($donations);
    }
}