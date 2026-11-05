<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    // تسجيل حساب جديد
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:restaurant,charity',
            'city'     => 'required|string',
            'phone'    => 'nullable|string',
            'address'  => 'nullable|string',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'city'     => $request->city,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'status'   => 'pending',
        ]);

        return response()->json([
            'message' => 'تم التسجيل، في انتظار موافقة الإدارة',
            'user'    => $user,
        ], 201);
    }

    // تسجيل الدخول
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ], 401);
        }

        if ($user->status === 'pending') {
            return response()->json([
                'message' => 'حسابك قيد المراجعة، انتظر موافقة الإدارة'
            ], 403);
        }

        if ($user->status === 'banned') {
            return response()->json([
                'message' => 'تم حظر حسابك، تواصل مع الإدارة'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج']);
    }

    // بيانات المستخدم الحالي
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // تحديث الملف الشخصي
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'    => 'sometimes|string|max:255',
            'phone'   => 'sometimes|string',
            'address' => 'sometimes|string',
            'city'    => 'sometimes|string',
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'phone', 'address', 'city']));

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي',
            'user'    => $user,
        ]);
    }

    // تغيير كلمة المرور



public function changePassword(Request $request)
{
    // 1. التحقق من المدخلات
    $request->validate([
        'current_password' => 'required',
        'password' => 'required|min:8|confirmed', // confirmed تعني يجب تطابق password_confirmation
    ]);

    $user = $request->user();

    // 2. التحقق من صحة كلمة المرور الحالية
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'message' => 'كلمة المرور الحالية التي أدخلتها غير صحيحة'
        ], 422);
    }

    // 3. تحديث كلمة المرور الجديدة
    $user->update([
        'password' => Hash::make($request->password)
    ]);

    return response()->json([
        'message' => 'تم تغيير كلمة المرور بنجاح '
    ], 200);
}
}