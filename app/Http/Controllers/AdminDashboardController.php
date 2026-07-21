<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Normaliza un texto removiendo acentos y diacríticos para búsquedas más flexibles
     * Ejemplo: "JOÃO" -> "JOAO", "María" -> "Maria"
     *
     * @param  string  $text
     * @return string
     */
    private function normalizeText($text)
    {
        // Convertir a mayúsculas para comparación case-insensitive
        $text = mb_strtoupper($text, 'UTF-8');

        // Mapa de caracteres con acentos a sus equivalentes sin acentos
        $unwanted_array = [
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ñ' => 'N', 'Ç' => 'C',
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ñ' => 'n', 'ç' => 'c',
        ];

        return strtr($text, $unwanted_array);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $clinicaFilter = $request->input('clinica');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $estadoFilter = $request->input('estado');

        // Obtener todos los laudos
        $allLaudos = $this->firebaseService->getAllLaudos();

        // Obtener lista de clínicas para el filtro
        $clinicas = $this->firebaseService->getAllClinicas();

        // Filtrar por búsqueda si existe
        if ($search) {
            // Normalizar el término de búsqueda
            $normalizedSearch = $this->normalizeText($search);

            $allLaudos = array_filter($allLaudos, function ($laudo) use ($search, $normalizedSearch) {
                // Búsqueda exacta en documento (números no necesitan normalización)
                $documentoMatch = stripos($laudo['documento'], $search) !== false;

                // Búsqueda normalizada en nombres (ignora acentos)
                $normalizedNombre = $this->normalizeText($laudo['nombres']);
                $nombreMatch = stripos($normalizedNombre, $normalizedSearch) !== false;

                return $documentoMatch || $nombreMatch;
            });
        }

        // Filtrar por clínica si existe
        if ($clinicaFilter) {
            $allLaudos = array_filter($allLaudos, function ($laudo) use ($clinicaFilter) {
                return $laudo['id_clinica'] === $clinicaFilter;
            });
        }

        // Filtrar por rango de fechas
        if ($fechaInicio || $fechaFin) {
            $allLaudos = array_filter($allLaudos, function ($laudo) use ($fechaInicio, $fechaFin) {
                $fechaEstudio = $laudo['fecha_estudio'];

                // Convertir fecha del formato DD/MM/YYYY a YYYY-MM-DD para comparación
                if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fechaEstudio, $matches)) {
                    $fechaEstudioComparable = $matches[3].'-'.$matches[2].'-'.$matches[1];
                } else {
                    $fechaEstudioComparable = $fechaEstudio;
                }

                $cumpleFechaInicio = empty($fechaInicio) || $fechaEstudioComparable >= $fechaInicio;
                $cumpleFechaFin = empty($fechaFin) || $fechaEstudioComparable <= $fechaFin;

                return $cumpleFechaInicio && $cumpleFechaFin;
            });
        }

        // Filtrar por ESTADO si existe
        if ($estadoFilter) {
            $allLaudos = array_filter($allLaudos, function ($laudo) use ($estadoFilter) {
                $laudoEstado = $laudo['estado'] ?? 'aprovado';

                return $laudoEstado === $estadoFilter;
            });
        }

        // Convertir a colección para usar paginate
        $laudosCollection = collect($allLaudos);

        // Ordenar por fecha de estudio (más recientes primero)
        $laudosCollection = $laudosCollection->sortByDesc(function ($laudo) {
            $fechaEstudio = $laudo['fecha_estudio'];
            // Convertir fecha del formato DD/MM/YYYY a YYYY-MM-DD para ordenar correctamente
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fechaEstudio, $matches)) {
                return $matches[3].'-'.$matches[2].'-'.$matches[1];
            }

            return $fechaEstudio;
        });

        // Paginación manual
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $laudos = new \Illuminate\Pagination\LengthAwarePaginator(
            $laudosCollection->forPage($currentPage, $perPage),
            $laudosCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.dashboard', compact('laudos', 'clinicas', 'estadoFilter'));
    }

    public function verPDF($id_clinica, $id_laudo)
    {
        // En lugar de generar URL externa, usar endpoint interno
        $pdfUrl = route('admin.serve.pdf', ['id_clinica' => $id_clinica, 'id_laudo' => $id_laudo]);

        return view('ver_pdf', compact('pdfUrl'));
    }

    public function servePDF($id_clinica, $id_laudo)
    {
        $clinica = $this->firebaseService->getClinicaById($id_clinica);

        if (! $clinica) {
            abort(404, 'Clínica não encontrada');
        }

        $path = "LAUDOS/{$clinica['nombre']}/{$id_laudo}";
        if (! str_ends_with($id_laudo, '.pdf')) {
            $path .= '.pdf';
        }

        $content = $this->firebaseService->downloadFileContent($path);

        if (! $content) {
            abort(404, 'PDF não encontrado');
        }

        return response($content, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$id_laudo.'.pdf"');
    }

    public function downloadPDF($id_clinica, $id_laudo)
    {
        $clinica = $this->firebaseService->getClinicaById($id_clinica);

        if (! $clinica) {
            abort(404, 'Clínica não encontrada');
        }

        $path = "LAUDOS/{$clinica['nombre']}/{$id_laudo}";
        if (! str_ends_with($id_laudo, '.pdf')) {
            $path .= '.pdf';
        }

        $content = $this->firebaseService->downloadFileContent($path);

        if (! $content) {
            abort(404, 'PDF não encontrado');
        }

        return response($content, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$id_laudo.'.pdf"');
    }

    public function deleteLaudo($id_clinica, $id_laudo)
    {
        try {
            $success = $this->firebaseService->deleteLaudo($id_laudo, $id_clinica);

            if ($success) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('success', 'Laudo eliminado exitosamente.');
            } else {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('error', 'Erro ao eliminar o laudo. Por favor, tente novamente.');
            }
        } catch (\Exception $e) {
            \Log::error('Error al eliminar laudo desde controlador: '.$e->getMessage());

            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Erro interno ao eliminar o laudo.');
        }
    }
}
