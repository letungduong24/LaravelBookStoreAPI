<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User 
     */
    public function index()
{
    $userCount = User::count();
    $revenueThisMonth = Order::whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        ->sum('total');
    
    $lastMonth = Carbon::now()->subMonth();
    $revenueLastMonth = Order::whereMonth('created_at', $lastMonth->month)
        ->whereYear('created_at', $lastMonth->year)
        ->sum('total');

    $percentageIncrease = 0;
    if ($revenueLastMonth > 0) {
        $percentageIncrease = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
    } else if ($revenueThisMonth > 0) {
        $percentageIncrease = 100;
    }

    $bookCount = Book::count();

    $data = [
        'userCount' => $userCount,
        'revenueThisMonth' => $revenueThisMonth,
        'revenueLastMonth' => $revenueLastMonth,
        'percentageIncrease' => $percentageIncrease,
        'bookCount' => $bookCount
    ];

    return response()->json($data, 200);
}



    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lỗi',
                    'errors' => collect($validateUser->errors())->map(function ($messages) {
                        return array_map(function ($msg) {
                            return str_replace(
                                [
                                    'The email has already been taken.',
                                    'The name has already been taken.'
                                ],
                                [
                                    'Email đã được đăng ký, vui lòng chọn email khác.',
                                    'Tên người dùng đã tồn tại, vui lòng chọn tên khác.'
                                ],
                                $msg
                            );
                        }, $messages);
                    })
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Đăng ký thành công',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
{
    $user = $request->user();

    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:15',
    ]);

    $user->name = $validatedData['name'];
    $user->address = $validatedData['address'] ?? $user->address;
    $user->phone = $validatedData['phone'] ?? $user->phone;
    $user->save();

    return response()->json(['message' => 'Cập nhật thông tin thành công', 'user' => $user], 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }


    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Lỗi',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Thông tin đăng nhập không hợp lệ.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'Đăng nhập',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
    }
}