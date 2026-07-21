<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class RegularUserController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index(Request $request)
    {
        $users = $this->firebaseService->getAllUsers();
        $clinicas = $this->firebaseService->getAllClinicas();
        $usersCollection = collect($users);
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $usersPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $usersCollection->forPage($currentPage, $perPage),
            $usersCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.usuarios.index', compact('usersPaginated', 'clinicas'));
    }

    public function create()
    {
        $clinicas = $this->firebaseService->getAllClinicas();

        return view('admin.usuarios.create', compact('clinicas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'USUARIO' => 'required|string|max:100',
            'PASSWORD' => 'required|string|min:4|max:100',
            'NOMBRES' => 'nullable|string|max:255',
            'APELLIDOS' => 'nullable|string|max:255',
            'ID_CLINICA' => 'nullable|string|max:255',
        ]);
        $result = $this->firebaseService->createUser($validated);
        if ($result['success']) {
            return redirect()->route('admin.usuarios.index')->with('success', $result['message']);
        }

        return back()->withInput()->with('error', $result['message']);
    }

    public function edit($id)
    {
        $user = $this->firebaseService->getUserById($id);
        $clinicas = $this->firebaseService->getAllClinicas();
        if (! $user) {
            return redirect()->route('admin.usuarios.index')->with('error', 'Usuario no encontrado.');
        }

        return view('admin.usuarios.edit', compact('user', 'clinicas'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'USUARIO' => 'required|string|max:100',
            'PASSWORD' => 'nullable|string|min:4|max:100',
            'NOMBRES' => 'nullable|string|max:255',
            'APELLIDOS' => 'nullable|string|max:255',
            'ID_CLINICA' => 'nullable|string|max:255',
        ]);
        if (empty($validated['PASSWORD'])) {
            unset($validated['PASSWORD']);
        }
        $result = $this->firebaseService->updateUser($id, $validated);
        if ($result['success']) {
            return redirect()->route('admin.usuarios.index')->with('success', $result['message']);
        }

        return back()->withInput()->with('error', $result['message']);
    }

    public function destroy($id)
    {
        $result = $this->firebaseService->deleteUser($id);
        if ($result['success']) {
            return redirect()->route('admin.usuarios.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
