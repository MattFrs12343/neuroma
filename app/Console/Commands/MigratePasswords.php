<?php

namespace App\Console\Commands;

use App\Services\FirebaseService;
use Illuminate\Console\Command;

class MigratePasswords extends Command
{
    /**
     * El nombre y firma del comando de Artisan.
     *
     * @var string
     */
    protected $signature = 'auth:migrate-passwords';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Migra contraseñas en texto plano a bcrypt en Firestore';

    /**
     * Ejecuta el comando.
     *
     * Itera las colecciones USUARIOS y ADMINISTRADORES.
     * Las contraseñas que ya comienzan con $2y$ (bcrypt) se omiten.
     * Las contraseñas en texto plano se hashean con Hash::make() y se actualizan.
     */
    public function handle(FirebaseService $firebaseService): int
    {
        $collections = [
            'USUARIOS' => [
                'fetch' => fn () => $firebaseService->getAllUsers(),
                'update' => fn (string $id, array $data) => $firebaseService->updateUser($id, $data),
            ],
            'ADMINISTRADORES' => [
                'fetch' => fn () => $firebaseService->getAllAdmins(),
                'update' => fn (string $id, array $data) => $firebaseService->updateAdmin($id, $data),
            ],

        ];

        foreach ($collections as $name => $actions) {
            $this->info("Procesando {$name}...");

            $records = $actions['fetch']();
            $migrated = 0;
            $skipped = 0;

            foreach ($records as $record) {
                $password = $record['PASSWORD'] ?? '';

                // Saltar registros que ya tienen hash bcrypt
                if (str_starts_with($password, '$2y$')) {
                    $skipped++;

                    continue;
                }

                // Migrar: enviar el password actual (se hasheará en updateUser/updateAdmin)
                $actions['update']($record['id'], ['PASSWORD' => $password]);
                $migrated++;
            }

            $this->info("{$name}: {$migrated} migrados, {$skipped} omitidos");
        }

        $this->info('Migración completada.');

        return self::SUCCESS;
    }
}
