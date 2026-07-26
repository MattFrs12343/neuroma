<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Factory;

class FirebaseService
{
    private $projectId;

    private $apiKey;

    private $baseUrl;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->apiKey = config('firebase.api_key');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";

        $this->firebase = (new Factory)->withServiceAccount(
            base_path('storage/app/firebase/credenciales.json'),
        );

        // Inicializar los servicios que necesites
        $this->firebaseAuth = $this->firebase->createAuth(); // Servicio de autenticación
        $this->firebaseStorage = $this->firebase->createStorage(); // Servicio

        // Configurar el bucket desde la configuración
        $this->storageBucket = config('firebase.storage_bucket');
    }

    private function getHttpClient()
    {
        return Http::withOptions([
            'verify' => false, // Deshabilita la verificación SSL
        ]);
    }

    public function validateUser($username, $password)
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => 'USUARIOS']],
                'where' => [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'USUARIO'],
                        'op' => 'EQUAL',
                        'value' => ['stringValue' => $username],
                    ],
                ],
            ],
        ];

        $response = $this->getHttpClient()->post(
            "{$this->baseUrl}:runQuery?key={$this->apiKey}",
            $query,
        );

        if ($response->successful() && ! empty($response[0]['document'])) {
            $data = $this->parseFirestoreDocument($response[0]['document']);

            // Verificar la contraseña con Hash::check()
            if (Hash::check($password, $data['PASSWORD'] ?? '')) {
                return $data;
            }
        }

        return null;
    }

    public function validateAdmin($username, $password)
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => 'ADMINISTRADORES']],
                'where' => [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'USUARIO'],
                        'op' => 'EQUAL',
                        'value' => ['stringValue' => $username],
                    ],
                ],
            ],
        ];

        $response = $this->getHttpClient()->post(
            "{$this->baseUrl}:runQuery?key={$this->apiKey}",
            $query,
        );

        if ($response->successful() && ! empty($response[0]['document'])) {
            $data = $this->parseFirestoreDocument($response[0]['document']);

            // Verificar la contraseña con Hash::check()
            if (Hash::check($password, $data['PASSWORD'] ?? '')) {
                return $data;
            }
        }

        return null;
    }

    public function getLaudos($idClinica)
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => 'LAUDOS']],
                'where' => [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'ID_CLINICA'],
                        'op' => 'EQUAL',
                        'value' => ['stringValue' => $idClinica],
                    ],
                ],
                'select' => [
                    'fields' => [
                        ['fieldPath' => 'DOCUMENTO'],
                        ['fieldPath' => 'FECHA_ESTUDIO'],
                        ['fieldPath' => 'NOMBRES'],
                        ['fieldPath' => 'TIPO_ESTUDIO'],
                        ['fieldPath' => 'ESTADO'],
                        ['fieldPath' => 'REVISADO_POR'],
                        ['fieldPath' => 'MOTIVO_RECHAZO'],
                        ['fieldPath' => 'LAUDO_ORIGINAL'],
                    ],
                ],
            ],
        ];

        $response = $this->getHttpClient()->post(
            "{$this->baseUrl}:runQuery?key={$this->apiKey}",
            $query,
        );

        if (! $response->successful()) {
            return [];
        }

        $tiposEstudios = $this->getTiposEstudios();
        $laudos = [];

        foreach ($response->json() as $item) {
            if (isset($item['document'])) {
                $data = $this->parseFirestoreDocument($item['document']);
                $documentName = $item['document']['name'] ?? '';
                $documentId = basename($documentName);
                $tipoEstudioNombre =
                    $tiposEstudios[$data['TIPO_ESTUDIO']] ?? 'Desconocido';

                $laudos[] = [
                    'documento' => $data['DOCUMENTO'],
                    'fecha_estudio' => $data['FECHA_ESTUDIO'],
                    'nombres' => $data['NOMBRES'],
                    'tipo_estudio' => $tipoEstudioNombre,
                    'id_documento' => $documentId,
                    'estado' => $data['ESTADO'] ?? 'aprovado',
                    'revisado_por' => $data['REVISADO_POR'] ?? null,
                    'motivo_rechazo' => $data['MOTIVO_RECHAZO'] ?? null,
                    'laudo_original' => $data['LAUDO_ORIGINAL'] ?? null,
                ];
            }
        }

        return $laudos;
    }

    private function getTiposEstudios()
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => 'TIPOS_ESTUDIOS']],
                'select' => [
                    'fields' => [
                        ['fieldPath' => 'ID'],
                        ['fieldPath' => 'NOMBRE_ESTUDIO'],
                    ],
                ],
            ],
        ];

        $response = $this->getHttpClient()->post(
            "{$this->baseUrl}:runQuery?key={$this->apiKey}",
            $query,
        );

        $tipos = [];
        if ($response->successful()) {
            foreach ($response->json() as $item) {
                if (isset($item['document'])) {
                    $data = $this->parseFirestoreDocument($item['document']);
                    if (!empty($data['ID']) && !empty($data['NOMBRE_ESTUDIO'])) {
                        $tipos[$data['ID']] = $data['NOMBRE_ESTUDIO'];
                    }
                }
            }
        }

        return $tipos;
    }

    private function parseFirestoreDocument($document)
    {
        $result = [];
        if (empty($document['fields'])) {
            return $result;
        }
        foreach ($document['fields'] as $field => $value) {
            $valueType = array_key_first($value);
            $result[$field] = $value[$valueType];
        }

        return $result;
    }

    public function getClinicaById($id_clinica)
    {
        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/CLINICAS/{$id_clinica}";

            $response = $this->getHttpClient()->get($baseUrl);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'nombre' => $data['fields']['NOMBRE']['stringValue'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error al obtener clínica: '.$e->getMessage());

            return null;
        }
    }

    public function getFirebaseToken()
    {
        try {
            $auth = $this->firebase->createAuth();
            $customToken = $auth->createCustomToken(
                'QyLK8yuVpEeCCsZ2LO3HelAicYa2',
            );

            return $customToken->toString();
        } catch (\Exception $e) {
            Log::error(
                'Error al generar token de Firebase: '.$e->getMessage(),
            );

            return null;
        }
    }

    public function getFirebaseStorageToken(
        $path,
        $expirationTimeInSeconds = 3600,
    ) {
        try {
            $storage = $this->firebase->createStorage();
            $bucket = $storage->getBucket($this->storageBucket);

            $object = $bucket->object($path);

            $options = [
                'version' => 'v4',
                'action' => 'read',
                'expires' => new \DateTime(
                    '+ '.$expirationTimeInSeconds.' seconds',
                ),
            ];

            $url = $object->signedUrl($options);
            \Log::info("URL generada para {$path}: ".$url);

            return $url;
        } catch (\Exception $e) {
            \Log::error(
                'Error al generar URL de Firebase Storage: '.$e->getMessage(),
            );

            return null;
        }
    }

    public function getPDFUrl($nombre_clinica, $id_laudo)
    {
        try {
            $path = "LAUDOS/{$nombre_clinica}/{$id_laudo}";
            if (! str_ends_with($id_laudo, '.pdf')) {
                $path .= '.pdf';
            }

            if (! $this->fileExists($path)) {
                \Log::warning("Archivo PDF no encontrado: {$path}");

                return null;
            }

            return $this->getFirebaseStorageToken($path);
        } catch (\Exception $e) {
            \Log::error('Error al generar URL de PDF: '.$e->getMessage());

            return null;
        }
    }

    public function fileExists($path)
    {
        try {
            $storage = $this->firebase->createStorage();
            $bucket = $storage->getBucket($this->storageBucket);
            $object = $bucket->object($path);

            return $object->exists();
        } catch (\Exception $e) {
            \Log::error(
                'Error al verificar existencia del archivo: '.
                    $e->getMessage(),
            );

            return false;
        }
    }

    public function listLaudosFolders()
    {
        try {
            $storage = $this->firebase->createStorage();
            $bucket = $storage->getBucket($this->storageBucket);
            $objects = $bucket->objects(['prefix' => 'LAUDOS/']);

            $folders = [];
            foreach ($objects as $object) {
                $name = $object->name();
                \Log::info("Objeto encontrado: {$name}");
                if (preg_match("/^LAUDOS\/([^\/]+)\//", $name, $matches)) {
                    $folderName = $matches[1];
                    if (! in_array($folderName, $folders)) {
                        $folders[] = $folderName;
                    }
                }
            }

            \Log::info(
                'Carpetas encontradas en LAUDOS: '.implode(', ', $folders),
            );

            return $folders;
        } catch (\Exception $e) {
            \Log::error('Error al listar carpetas: '.$e->getMessage());

            return [];
        }
    }

    public function downloadFileContent($path)
    {
        try {
            $storage = $this->firebase->createStorage();
            $bucket = $storage->getBucket($this->storageBucket);

            $possiblePaths = [
                $path,
                str_replace('CLINICA PRUEBA', 'CLINICA%20PRUEBA', $path),
                str_replace(' ', '%20', $path),
                str_replace(' ', '_', $path),
                str_replace('CLINICA PRUEBA', 'CLINICA_PRUEBA', $path),
            ];

            foreach ($possiblePaths as $testPath) {
                \Log::info("Probando ruta: {$testPath}");
                $object = $bucket->object($testPath);

                if ($object->exists()) {
                    \Log::info("¡Archivo encontrado en: {$testPath}!");
                    $content = $object->downloadAsString();
                    \Log::info(
                        "Archivo descargado exitosamente desde: {$testPath}",
                    );

                    return $content;
                }
            }

            \Log::warning(
                'Archivo no encontrado en ninguna de las rutas probadas: '.json_encode($possiblePaths),
            );

            return null;
        } catch (\Exception $e) {
            \Log::error(
                'Error al descargar archivo desde Firebase Storage: '.
                    $e->getMessage(),
            );
            throw $e;
        }
    }

    public function getAllLaudos()
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => 'LAUDOS']],
                'select' => [
                    'fields' => [
                        ['fieldPath' => 'DOCUMENTO'],
                        ['fieldPath' => 'FECHA_ESTUDIO'],
                        ['fieldPath' => 'NOMBRES'],
                        ['fieldPath' => 'TIPO_ESTUDIO'],
                        ['fieldPath' => 'ID_CLINICA'],
                        ['fieldPath' => 'ESTADO'],
                        ['fieldPath' => 'REVISADO_POR'],
                        ['fieldPath' => 'MOTIVO_RECHAZO'],
                        ['fieldPath' => 'LAUDO_ORIGINAL'],
                    ],
                ],
            ],
        ];

        $response = $this->getHttpClient()->post(
            "{$this->baseUrl}:runQuery?key={$this->apiKey}",
            $query,
        );

        if (! $response->successful()) {
            return [];
        }

        $tiposEstudios = $this->getTiposEstudios();
        $laudos = [];

        foreach ($response->json() as $item) {
            if (isset($item['document'])) {
                $data = $this->parseFirestoreDocument($item['document']);
                $documentName = $item['document']['name'] ?? '';
                $documentId = basename($documentName);
                $tipoEstudioNombre =
                    $tiposEstudios[$data['TIPO_ESTUDIO']] ?? 'Desconocido';

                $laudos[] = [
                    'documento' => $data['DOCUMENTO'],
                    'fecha_estudio' => $data['FECHA_ESTUDIO'],
                    'nombres' => $data['NOMBRES'],
                    'tipo_estudio' => $tipoEstudioNombre,
                    'id_documento' => $documentId,
                    'id_clinica' => $data['ID_CLINICA'],
                    'estado' => $data['ESTADO'] ?? 'aprovado',
                    'revisado_por' => $data['REVISADO_POR'] ?? null,
                    'motivo_rechazo' => $data['MOTIVO_RECHAZO'] ?? null,
                    'laudo_original' => $data['LAUDO_ORIGINAL'] ?? null,
                ];
            }
        }

        return $laudos;
    }

    public function getAllClinicas()
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => 'CLINICAS']],
                'select' => [
                    'fields' => [['fieldPath' => 'NOMBRE']],
                ],
            ],
        ];

        $response = $this->getHttpClient()->post(
            "{$this->baseUrl}:runQuery?key={$this->apiKey}",
            $query,
        );

        $clinicas = [];
        if ($response->successful()) {
            foreach ($response->json() as $item) {
                if (isset($item['document'])) {
                    $data = $this->parseFirestoreDocument($item['document']);
                    $documentName = $item['document']['name'] ?? '';
                    $documentId = basename($documentName);

                    $clinicas[] = [
                        'id' => $documentId,
                        'nombre' => $data['NOMBRE'],
                    ];
                }
            }
        }

        return $clinicas;
    }

    /**
     * Elimina un laudo de Firestore y su archivo PDF de Storage
     *
     * @param  string  $id_documento  ID del documento en Firestore (nombre del archivo)
     * @param  string  $id_clinica  ID de la clínica
     * @return bool True si se eliminó correctamente, false en caso contrario
     */
    public function deleteLaudo($id_documento, $id_clinica)
    {
        try {
            // 1. Obtener el nombre de la clínica
            $clinica = $this->getClinicaById($id_clinica);

            if (! $clinica) {
                \Log::error("No se encontró la clínica con ID: {$id_clinica}");

                return false;
            }

            $nombre_clinica = $clinica['nombre'];
            \Log::info(
                "Eliminando laudo - ID Documento: {$id_documento}, Clínica: {$nombre_clinica}",
            );

            // 2. Eliminar el archivo PDF de Firebase Storage
            $path = "LAUDOS/{$nombre_clinica}/{$id_documento}";
            if (! str_ends_with($id_documento, '.pdf')) {
                $path .= '.pdf';
            }

            try {
                $storage = $this->firebase->createStorage();
                $bucket = $storage->getBucket($this->storageBucket);
                $object = $bucket->object($path);

                if ($object->exists()) {
                    $object->delete();
                    \Log::info("Archivo PDF eliminado exitosamente: {$path}");
                } else {
                    \Log::warning(
                        "Archivo PDF no encontrado en Storage: {$path}",
                    );
                }
            } catch (\Exception $e) {
                \Log::error(
                    'Error al eliminar archivo de Storage: '.$e->getMessage(),
                );
                // Continuamos con la eliminación del documento aunque falle el archivo
            }

            // 3. Eliminar el documento de Firestore
            $documentPath = "{$this->baseUrl}/LAUDOS/{$id_documento}";

            $response = $this->getHttpClient()->delete($documentPath);

            if ($response->successful()) {
                \Log::info(
                    "Documento eliminado exitosamente de Firestore: {$id_documento}",
                );

                return true;
            } else {
                \Log::error(
                    'Error al eliminar documento de Firestore. Status: '.
                        $response->status(),
                );

                return false;
            }
        } catch (\Exception $e) {
            \Log::error('Error al eliminar laudo: '.$e->getMessage());

            return false;
        }
    }

    // ===================================================================
    // CRUD: ADMINISTRADORES
    // ===================================================================

    public function getAllAdmins()
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => 'ADMINISTRADORES']],
            ],
        ];

        $response = $this->getHttpClient()->post(
            "{$this->baseUrl}:runQuery?key={$this->apiKey}",
            $query,
        );

        $admins = [];
        if ($response->successful()) {
            foreach ($response->json() as $item) {
                if (isset($item['document'])) {
                    $data = $this->parseFirestoreDocument($item['document']);
                    $documentName = $item['document']['name'] ?? '';
                    $documentId = basename($documentName);
                    $admins[] = array_merge(['id' => $documentId], $data);
                }
            }
        }

        return $admins;
    }

    public function getAdminById($id)
    {
        try {
            $response = $this->getHttpClient()->get(
                "{$this->baseUrl}/ADMINISTRADORES/{$id}?key={$this->apiKey}",
            );
            if ($response->successful()) {
                $data = $this->parseFirestoreDocument($response->json());
                $documentName = $response->json()['name'] ?? '';
                $documentId = basename($documentName);

                return array_merge(['id' => $documentId], $data);
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Error al obtener administrador: '.$e->getMessage());

            return null;
        }
    }

    public function createAdmin($data)
    {
        try {
            $documentId = $data['USUARIO'];
            $existing = $this->getAdminById($documentId);
            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'Ya existe un administrador con ese usuario.',
                ];
            }
            // Hash de la contraseña antes de persistir
            if (isset($data['PASSWORD'])) {
                $data['PASSWORD'] = Hash::make($data['PASSWORD']);
            }
            $fields = [];
            foreach ($data as $key => $value) {
                $fields[$key] = ['stringValue' => $value];
            }
            $payload = ['fields' => $fields];
            $response = $this->getHttpClient()->patch(
                "{$this->baseUrl}/ADMINISTRADORES/{$documentId}?key={$this->apiKey}",
                $payload,
            );
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Administrador creado exitosamente.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al crear administrador.',
            ];
        } catch (\Exception $e) {
            \Log::error('Error al crear administrador: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error interno al crear administrador.',
            ];
        }
    }

    public function updateAdmin($id, $data)
    {
        try {
            // Hash de la contraseña si se incluye en la actualización
            if (isset($data['PASSWORD'])) {
                $data['PASSWORD'] = Hash::make($data['PASSWORD']);
            }
            $fields = [];
            foreach ($data as $key => $value) {
                $fields[$key] = ['stringValue' => $value];
            }
            $payload = ['fields' => $fields];
            $updateMask = implode('&', array_map(fn ($key) => "updateMask.fieldPaths={$key}", array_keys($data)));
            $response = $this->getHttpClient()->patch(
                "{$this->baseUrl}/ADMINISTRADORES/{$id}?key={$this->apiKey}&{$updateMask}",
                $payload,
            );
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Administrador actualizado exitosamente.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al actualizar administrador.',
            ];
        } catch (\Exception $e) {
            \Log::error(
                'Error al actualizar administrador: '.$e->getMessage(),
            );

            return [
                'success' => false,
                'message' => 'Error interno al actualizar administrador.',
            ];
        }
    }

    public function deleteAdmin($id)
    {
        try {
            $response = $this->getHttpClient()->delete(
                "{$this->baseUrl}/ADMINISTRADORES/{$id}?key={$this->apiKey}",
            );
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Administrador eliminado exitosamente.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al eliminar administrador.',
            ];
        } catch (\Exception $e) {
            \Log::error('Error al eliminar administrador: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error interno al eliminar administrador.',
            ];
        }
    }

    // ===================================================================
    // CRUD: USUARIOS (usuarios normales, no administradores)
    // ===================================================================

    public function getAllUsers()
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => 'USUARIOS']],
            ],
        ];

        $response = $this->getHttpClient()->post(
            "{$this->baseUrl}:runQuery?key={$this->apiKey}",
            $query,
        );

        $users = [];
        if ($response->successful()) {
            foreach ($response->json() as $item) {
                if (isset($item['document'])) {
                    $data = $this->parseFirestoreDocument($item['document']);
                    $documentName = $item['document']['name'] ?? '';
                    $documentId = basename($documentName);
                    $users[] = array_merge(['id' => $documentId], $data);
                }
            }
        }

        return $users;
    }

    public function getUserById($id)
    {
        try {
            $response = $this->getHttpClient()->get(
                "{$this->baseUrl}/USUARIOS/{$id}?key={$this->apiKey}",
            );
            if ($response->successful()) {
                $data = $this->parseFirestoreDocument($response->json());
                $documentName = $response->json()['name'] ?? '';
                $documentId = basename($documentName);

                return array_merge(['id' => $documentId], $data);
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Error al obtener usuario: '.$e->getMessage());

            return null;
        }
    }

    public function createUser($data)
    {
        try {
            $documentId = $data['USUARIO'];
            $existing = $this->getUserById($documentId);
            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'Ya existe un usuario con ese nombre de usuario.',
                ];
            }
            // Hash de la contraseña antes de persistir
            if (isset($data['PASSWORD'])) {
                $data['PASSWORD'] = Hash::make($data['PASSWORD']);
            }
            $fields = [];
            foreach ($data as $key => $value) {
                $fields[$key] = ['stringValue' => $value];
            }
            $payload = ['fields' => $fields];
            $response = $this->getHttpClient()->patch(
                "{$this->baseUrl}/USUARIOS/{$documentId}?key={$this->apiKey}",
                $payload,
            );
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Usuario creado exitosamente.',
                ];
            }

            return ['success' => false, 'message' => 'Error al crear usuario.'];
        } catch (\Exception $e) {
            \Log::error('Error al crear usuario: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error interno al crear usuario.',
            ];
        }
    }

    public function updateUser($id, $data)
    {
        try {
            // Hash de la contraseña si se incluye en la actualización
            if (isset($data['PASSWORD'])) {
                $data['PASSWORD'] = Hash::make($data['PASSWORD']);
            }
            $fields = [];
            foreach ($data as $key => $value) {
                $fields[$key] = ['stringValue' => $value];
            }
            $payload = ['fields' => $fields];
            $updateMask = implode('&', array_map(fn ($key) => "updateMask.fieldPaths={$key}", array_keys($data)));
            $response = $this->getHttpClient()->patch(
                "{$this->baseUrl}/USUARIOS/{$id}?key={$this->apiKey}&{$updateMask}",
                $payload,
            );
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Usuario actualizado exitosamente.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al actualizar usuario.',
            ];
        } catch (\Exception $e) {
            \Log::error('Error al actualizar usuario: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error interno al actualizar usuario.',
            ];
        }
    }

    public function deleteUser($id)
    {
        try {
            $response = $this->getHttpClient()->delete(
                "{$this->baseUrl}/USUARIOS/{$id}?key={$this->apiKey}",
            );
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Usuario eliminado exitosamente.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al eliminar usuario.',
            ];
        } catch (\Exception $e) {
            \Log::error('Error al eliminar usuario: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error interno al eliminar usuario.',
            ];
        }
    }

    // ===================================================================
    // CRUD: CLINICAS
    // ===================================================================

    public function getAllClinicasFull()
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => 'CLINICAS']],
            ],
        ];

        $response = $this->getHttpClient()->post(
            "{$this->baseUrl}:runQuery?key={$this->apiKey}",
            $query,
        );

        $clinicas = [];
        if ($response->successful()) {
            foreach ($response->json() as $item) {
                if (isset($item['document'])) {
                    $data = $this->parseFirestoreDocument($item['document']);
                    $documentName = $item['document']['name'] ?? '';
                    $documentId = basename($documentName);
                    $clinicas[] = array_merge(['id' => $documentId], $data);
                }
            }
        }

        return $clinicas;
    }

    public function getClinicaFullById($id)
    {
        try {
            $response = $this->getHttpClient()->get(
                "{$this->baseUrl}/CLINICAS/{$id}?key={$this->apiKey}",
            );
            if ($response->successful()) {
                $data = $this->parseFirestoreDocument($response->json());
                $documentName = $response->json()['name'] ?? '';
                $documentId = basename($documentName);

                return array_merge(['id' => $documentId], $data);
            }

            return null;
        } catch (\Exception $e) {
            \Log::error(
                'Error al obtener clínica completa: '.$e->getMessage(),
            );

            return null;
        }
    }

    public function createClinica($data)
    {
        try {
            $documentId = $data['NOMBRE'];
            $encodedId = rawurlencode($documentId);
            $existing = $this->getClinicaFullById($encodedId);
            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'Ya existe una clínica con ese nombre.',
                ];
            }
            $fields = [];
            foreach ($data as $key => $value) {
                if ($value === '' || $value === null) {
                    continue;
                }
                $fields[$key] = ['stringValue' => $value];
            }
            $payload = ['fields' => $fields];
            $response = $this->getHttpClient()->patch(
                "{$this->baseUrl}/CLINICAS/{$encodedId}?key={$this->apiKey}",
                $payload,
            );
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Clínica creada exitosamente.',
                ];
            }

            return ['success' => false, 'message' => 'Error al crear clínica.'];
        } catch (\Exception $e) {
            \Log::error('Error al crear clínica: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error interno al crear clínica.',
            ];
        }
    }

    public function updateClinica($id, $data)
    {
        try {
            $fields = [];
            foreach ($data as $key => $value) {
                if ($value === '' || $value === null) {
                    continue;
                }
                $fields[$key] = ['stringValue' => $value];
            }
            $encodedId = rawurlencode($id);
            $payload = ['fields' => $fields];
            $response = $this->getHttpClient()->patch(
                "{$this->baseUrl}/CLINICAS/{$encodedId}?key={$this->apiKey}",
                $payload,
            );
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Clínica actualizada exitosamente.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al actualizar clínica.',
            ];
        } catch (\Exception $e) {
            \Log::error('Error al actualizar clínica: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error interno al actualizar clínica.',
            ];
        }
    }

    public function deleteClinica($id)
    {
        try {
            $response = $this->getHttpClient()->delete(
                "{$this->baseUrl}/CLINICAS/{$id}?key={$this->apiKey}",
            );
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Clínica eliminada exitosamente.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al eliminar clínica.',
            ];
        } catch (\Exception $e) {
            \Log::error('Error al eliminar clínica: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error interno al eliminar clínica.',
            ];
        }
    }

    // ===================================================================
    // LAUDOS — Operaciones de revisión médica
    // ===================================================================

    /**
     * Obtiene un laudo por su ID de documento en Firestore.
     *
     * @param  string  $documentId  ID del documento en la colección LAUDOS.
     * @return array|null Datos del laudo o null si no existe.
     */
    public function getLaudoById(string $documentId): ?array
    {
        try {
            $response = $this->getHttpClient()->get(
                "{$this->baseUrl}/LAUDOS/{$documentId}?key={$this->apiKey}",
            );

            if ($response->successful()) {
                $data = $this->parseFirestoreDocument($response->json());

                return $data;
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Error al obtener laudo por ID: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Actualiza el estado de revisión de un laudo mediante PATCH con updateMask.
     * Solo modifica los campos especificados, sin sobrescribir el resto del documento.
     *
     * @param  string  $documentId  ID del documento en LAUDOS.
     * @param  string  $clinicaId  ID de la clínica (no usado en el PATCH, reservado).
     * @param  string  $estado  "aprovado" | "rechazado".
     * @param  string  $revisadoPor  Nombre del doctor revisor.
     * @param  string|null  $motivoRechazo  Motivo del rechazo (solo para rechazado).
     * @return bool true si el PATCH fue exitoso, false en caso contrario.
     */
    public function updateLaudoEstado(
        string $documentId,
        string $clinicaId,
        string $estado,
        string $revisadoPor,
        ?string $motivoRechazo
    ): bool {
        try {
            // Construir fields payload
            $fields = [
                'ESTADO' => ['stringValue' => $estado],
                'REVISADO_POR' => ['stringValue' => $revisadoPor],
            ];

            // Construir updateMask
            $fieldPaths = ['ESTADO', 'REVISADO_POR'];

            if ($motivoRechazo !== null) {
                $fields['MOTIVO_RECHAZO'] = ['stringValue' => $motivoRechazo];
                $fieldPaths[] = 'MOTIVO_RECHAZO';
            }

            // Construir query string con updateMask.fieldPaths
            $updateMaskParams = implode('&', array_map(
                fn (string $fp) => 'updateMask.fieldPaths='.$fp,
                $fieldPaths,
            ));

            $response = $this->getHttpClient()->patch(
                "{$this->baseUrl}/LAUDOS/{$documentId}?key={$this->apiKey}&{$updateMaskParams}",
                ['fields' => $fields],
            );

            if ($response->successful()) {
                return true;
            }

            \Log::error(
                'Error al actualizar estado del laudo. Status: '.$response->status(),
            );

            return false;
        } catch (\Exception $e) {
            \Log::error('Error al actualizar estado del laudo: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Elimina el archivo PDF de un laudo en Firebase Storage.
     * No modifica el documento en Firestore.
     *
     * @param  string  $documentId  ID del laudo.
     * @param  string  $clinicaId  ID de la clínica para resolver el nombre de la carpeta.
     * @return bool true si el archivo fue eliminado, false si no existía o hubo error.
     */
    public function deleteLaudoPDF(string $documentId, string $clinicaId): bool
    {
        try {
            // Resolver nombre de la clínica para construir la ruta
            $clinica = $this->getClinicaById($clinicaId);
            if (! $clinica || ! isset($clinica['nombre'])) {
                \Log::error("No se encontró la clínica para eliminar PDF: {$clinicaId}");

                return false;
            }

            $nombreClinica = $clinica['nombre'];
            $path = "LAUDOS/{$nombreClinica}/{$documentId}";

            // Agregar extensión .pdf si no la tiene
            if (! str_ends_with($path, '.pdf')) {
                $path .= '.pdf';
            }

            $storage = $this->firebaseStorage;
            $bucket = $storage->getBucket($this->storageBucket);
            $object = $bucket->object($path);

            if ($object->exists()) {
                $object->delete();
                \Log::info("PDF de laudo eliminado: {$path}");

                return true;
            }

            \Log::warning("PDF de laudo no encontrado en Storage: {$path}");

            return false;
        } catch (\Exception $e) {
            \Log::error('Error al eliminar PDF de laudo: '.$e->getMessage());

            return false;
        }
    }
}
