<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    private $firebaseService;

    private const PER_PAGE = 20; // Número de items por página

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
        if (session('id_clinica') == '') {
            return redirect()->route('login.show')->with('message', 'Sessão fechada por inatividade');
        }

        $searchTerm = $request->input('search', '');
        $fechaInicio = $request->input('fecha_inicio', '');
        $fechaFin = $request->input('fecha_fin', '');
        $id_clinica = session('id_clinica');
        $currentPage = $request->get('page', 1);

        // Obtener nombre de la clínica
        $clinica = $this->firebaseService->getClinicaById($id_clinica);
        $nombre_clinica = $clinica['nombre'] ?? '';
        session([
            'nombre_clinica' => $nombre_clinica,
        ]);

        // Obtener todos los laudos
        $allLaudos = $this->firebaseService->getLaudos($id_clinica);

        // Convertir a Collection para usar métodos de Laravel
        $laudosCollection = collect($allLaudos);

        // Filtrar laudos si hay término de búsqueda
        if (! empty($searchTerm)) {
            // Normalizar el término de búsqueda
            $normalizedSearchTerm = $this->normalizeText($searchTerm);

            $laudosCollection = $laudosCollection->filter(function ($laudo) use ($searchTerm, $normalizedSearchTerm) {
                // Búsqueda exacta en documento (números no necesitan normalización)
                $documentoMatch = stripos($laudo['documento'], $searchTerm) !== false;

                // Búsqueda normalizada en nombres (ignora acentos)
                $normalizedNombre = $this->normalizeText($laudo['nombres']);
                $nombreMatch = stripos($normalizedNombre, $normalizedSearchTerm) !== false;

                return $documentoMatch || $nombreMatch;
            });
        }

        // Filtrar por rango de fechas
        if (! empty($fechaInicio) || ! empty($fechaFin)) {
            $laudosCollection = $laudosCollection->filter(function ($laudo) use ($fechaInicio, $fechaFin) {
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

        // Ordenar los laudos por fecha de estudio (más recientes primero)
        $laudosCollection = $laudosCollection->sortByDesc(function ($laudo) {
            $fechaEstudio = $laudo['fecha_estudio'];
            // Convertir fecha del formato DD/MM/YYYY a YYYY-MM-DD para ordenar correctamente
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fechaEstudio, $matches)) {
                return $matches[3].'-'.$matches[2].'-'.$matches[1];
            }

            return $fechaEstudio;
        });

        // Crear paginador manual
        $total = $laudosCollection->count();
        $start = ($currentPage - 1) * self::PER_PAGE;

        // Obtener solo los items de la página actual
        $laudosForCurrentPage = $laudosCollection->slice($start, self::PER_PAGE)->values();

        // Crear instancia de LengthAwarePaginator
        $laudos = new LengthAwarePaginator(
            $laudosForCurrentPage,
            $total,
            self::PER_PAGE,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('dashboard', compact('laudos', 'id_clinica'));
    }

    public function verPDF($id_clinica, $id_laudo)
    {
        // En lugar de generar URL externa, usar endpoint interno
        $pdfUrl = route('serve.pdf', ['id_clinica' => $id_clinica, 'id_laudo' => $id_laudo]);

        return view('ver_pdf', compact('pdfUrl'));
    }

    /**
     * Sirve el PDF directamente desde el servidor con headers correctos
     */
    public function servePDF($id_clinica, $id_laudo)
    {
        try {
            // Obtener nombre de la clínica
            $clinica = $this->firebaseService->getClinicaById($id_clinica);
            $nombre_clinica = $clinica['nombre'] ?? '';

            // No agregar .pdf si ya está incluido en el id_laudo
            $path = "LAUDOS/{$nombre_clinica}/{$id_laudo}";
            if (! str_ends_with($id_laudo, '.pdf')) {
                $path .= '.pdf';
            }

            // Descargar el contenido directamente desde Firebase Storage
            $pdfContent = $this->firebaseService->downloadFileContent($path);

            if (! $pdfContent) {
                abort(404, 'PDF no encontrado');
            }

            // Servir el PDF con headers correctos para visualización
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="laudo-'.$id_laudo.'.pdf"')
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('X-Frame-Options', 'SAMEORIGIN');

        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                throw $e;
            }
            \Log::error('Error al servir PDF: '.$e->getMessage());
            abort(500, 'Error interno del servidor');
        }
    }

    /**
     * Descarga directamente un PDF sin visualización previa
     *
     * @param  string  $id_clinica  ID de la clínica
     * @param  string  $id_laudo  ID del laudo a descargar
     * @return \Illuminate\Http\Response
     */
    public function downloadPDF($id_clinica, $id_laudo)
    {
        try {
            // Obtener nombre de la clínica
            $clinica = $this->firebaseService->getClinicaById($id_clinica);
            $nombre_clinica = $clinica['nombre'] ?? '';

            \Log::info("Descarga PDF - ID Clínica: {$id_clinica}, Nombre Clínica: '{$nombre_clinica}', ID Laudo: {$id_laudo}");

            // No agregar .pdf si ya está incluido en el id_laudo
            $path = "LAUDOS/{$nombre_clinica}/{$id_laudo}";
            if (! str_ends_with($id_laudo, '.pdf')) {
                $path .= '.pdf';
            }

            \Log::info("Ruta completa del archivo: {$path}");

            // Descargar el contenido directamente desde Firebase Storage
            $pdfContent = $this->firebaseService->downloadFileContent($path);

            if (! $pdfContent) {
                \Log::error("No se pudo obtener el contenido del archivo: {$path}");

                return response()->json(['error' => 'El archivo PDF no existe o no se pudo descargar'], 404);
            }

            // Generar nombre de archivo para descarga
            $filename = str_ends_with($id_laudo, '.pdf') ? $id_laudo : $id_laudo.'.pdf';

            \Log::info("Descarga exitosa del archivo: {$path}");

            // Generar respuesta para descarga directa
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');

        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                throw $e;
            }
            \Log::error('Error al descargar PDF: '.$e->getMessage());

            return response()->json(['error' => 'Error interno del servidor: '.$e->getMessage()], 500);
        }
    }

    /**
     * Helper method to create paginator for arrays or collections
     *
     * @param  Collection|array  $items
     * @param  int  $perPage
     * @param  int|null  $page
     * @param  array  $options
     * @return LengthAwarePaginator
     */
    private function paginate($items, $perPage = self::PER_PAGE, $page = null, $options = [])
    {
        $page = $page ?: (request()->get('page', 1));

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }
}
