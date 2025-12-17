<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 

use App\Helpers\ApiFormatter;
use App\Models\LogModel; // Model Log Anda

class LogController extends Controller
{

    public function index(Request $request)
    {
        $limit = $request->query('limit', 20); 
        
        try {
            // Menggunakan Primary Key 'log_id' untuk pengurutan, 
            // karena ini pasti ada di model Anda (setelah kita coba koreksi).
            $logs = LogModel::orderBy('log_id', 'desc') 
                            ->limit($limit)
                            ->get();

            return response()->json(ApiFormatter::createJson(200, 'Data Log berhasil diambil', $logs), 200);

        } catch (\Exception $e) {
            Log::error('LogController@index - Gagal mengambil data log: ' . $e->getMessage());

            return response()->json(ApiFormatter::createJson(500, 'Internal Server Error', $e->getMessage()), 500);
        }
    }
}