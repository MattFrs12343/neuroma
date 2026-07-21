<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class ClinicaController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index(Request $request)
    {
        $clinicas = $this->firebaseService->getAllClinicasFull();
        $clinicasCollection = collect($clinicas);
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $clinicasPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $clinicasCollection->forPage($currentPage, $perPage),
            $clinicasCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.clinicas.index', compact('clinicasPaginated'));
    }

    public function create()
    {
        return view('admin.clinicas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'NOMBRE' => 'required|string|max:255',
            'DIRECCION' => 'nullable|string|max:500',
            'TELEFONO' => 'nullable|string|max:50',
            'EMAIL' => 'nullable|email|max:255',
        ]);
        $result = $this->firebaseService->createClinica($validated);
        if ($result['success']) {
            return redirect()->route('admin.clinicas.index')->with('success', $result['message']);
        }

        return back()->withInput()->with('error', $result['message']);
    }

    public function edit($id)
    {
        $clinica = $this->firebaseService->getClinicaFullById($id);
        if (! $clinica) {
            return redirect()->route('admin.clinicas.index')->with('error', 'Clinica no encontrada.');
        }

        return view('admin.clinicas.edit', compact('clinica'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'NOMBRE' => 'required|string|max:255',
            'DIRECCION' => 'nullable|string|max:500',
            'TELEFONO' => 'nullable|string|max:50',
            'EMAIL' => 'nullable|email|max:255',
        ]);
        $result = $this->firebaseService->updateClinica($id, $validated);
        if ($result['success']) {
            return redirect()->route('admin.clinicas.index')->with('success', $result['message']);
        }

        return back()->withInput()->with('error', $result['message']);
    }

    public function destroy($id)
    {
        $result = $this->firebaseService->deleteClinica($id);
        if ($result['success']) {
            return redirect()->route('admin.clinicas.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
