<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $admin = $this->firebaseService->validateAdmin(
            $credentials['username'],
            $credentials['password']
        );

        if ($admin) {
            session([
                'admin_nombres' => $admin['NOMBRES'],
                'admin_apellidos' => $admin['APELLIDOS'],
                'is_admin' => true,
            ]);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'username' => 'Credenciais incorretas.',
        ]);
    }

    public function logout()
    {
        session()->flush();

        return redirect()->route('admin.login.show');
    }
}
