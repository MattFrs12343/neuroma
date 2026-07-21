<?php

namespace App\Providers;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\ServiceProvider;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FirestoreClient::class, function ($app) {
            $config = config('firebase.credentials');

            return new FirestoreClient([
                'projectId' => $config['project_id'],
                'keyFile' => $config,
            ]);
        });
    }
}
