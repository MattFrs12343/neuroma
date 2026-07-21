<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index(Request $request)
    {
        $admins = $this->firebaseService->getAllAdmins();
        $clinicas = $this->firebaseService->getAllClinicas();
        $adminsCollection = collect($admins);
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $adminsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $adminsCollection->forPage($currentPage, $perPage),
            $adminsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.admins.index', compact('adminsPaginated', 'clinicas'));
    }

    public function create()
    {
        $clinicas = $this->firebaseService->getAllClinicas();

        return view('admin.admins.create', compact('clinicas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'USUARIO' => 'required|string|max:100',
            'PASSWORD' => 'required|string|min:4|max:100',
            'NOMBRES' => 'required|string|max:255',
            'APELLIDOS' => 'required|string|max:255',
            'ID_CLINICA' => 'nullable|string|max:255',
        ]);
        $result = $this->firebaseService->createAdmin($validated);
        if ($result['success']) {
            return redirect()->route('admin.admins.index')->with('success', $result['message']);
        }

        return back()->withInput()->with('error', $result['message']);
    }

    public function edit($id)
    {
        $admin = $this->firebaseService->getAdminById($id);
        $clinicas = $this->firebaseService->getAllClinicas();
        if (! $admin) {
            return redirect()->route('admin.admins.index')->with('error', 'Administrador no encontrado.');
        }

        return view('admin.admins.edit', compact('admin', 'clinicas'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'USUARIO' => 'required|string|max:100',
            'PASSWORD' => 'nullable|string|min:4|max:100',
            'NOMBRES' => 'required|string|max:255',
            'APELLIDOS' => 'required|string|max:255',
            'ID_CLINICA' => 'nullable|string|max:255',
        ]);
        if (empty($validated['PASSWORD'])) {
            unset($validated['PASSWORD']);
        }
        $result = $this->firebaseService->updateAdmin($id, $validated);
        if ($result['success']) {
            return redirect()->route('admin.admins.index')->with('success', $result['message']);
        }

        return back()->withInput()->with('error', $result['message']);
    }

    public function destroy($id)
    {
        $result = $this->firebaseService->deleteAdmin($id);
        if ($result['success']) {
            return redirect()->route('admin.admins.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
