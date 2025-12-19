<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Exception;

class DemoController extends Controller
{
    /**
     * Reset database to "Demo Ready" state (Saturday Scenario)
     */
    public function reset()
    {
        try {
            // Run the seeder programmatically
            // We use --force to bypass confirmation in production/demo mode if needed
            Artisan::call('db:seed', [
                '--class' => 'SaturdayPresentationSeeder',
                '--force' => true 
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Aplikasi berhasil di-reset ke Mode Demo (Sabtu).'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal reset: ' . $e->getMessage()
            ], 500);
        }
    }
}
