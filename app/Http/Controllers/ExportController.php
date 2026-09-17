<?php

namespace App\Http\Controllers;

use App\Jobs\ExportMedicineJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    public function excel(Request $request)
    {
        return $this->dispatchExport($request, 'xlsx');
    }

    public function pdf(Request $request)
    {
        return $this->dispatchExport($request, 'pdf');
    }

    private function dispatchExport(Request $request, string $format)
    {
        $exportId = Str::uuid()->toString();
        $filters = $request->only(['search', 'category', 'status']);

        ExportMedicineJob::dispatch($filters, $exportId, $format);

        return response()->json([
            'export_id' => $exportId,
            'message' => 'Export job started successfully'
        ]);
    }

    public function progress($exportId)
    {
        $status = Cache::get('export_' . $exportId);
        if (!$status) {
            return response()->json([
                'status' => 'pending',
                'progress' => 0,
                'processed' => 0,
                'total' => 0
            ]);
        }

        return response()->json($status);
    }

    public function download($exportId)
    {
        foreach (['xlsx', 'pdf'] as $extension) {
            $filePath = Storage::disk('local')->path("exports/medicines_{$exportId}.{$extension}");

            if (file_exists($filePath)) {
                $downloadName = 'Laporan_Stok_Obat_' . date('Ymd') . '.' . $extension;

                return response()->download($filePath, $downloadName)->deleteFileAfterSend(true);
            }
        }

        abort(404, 'File belum selesai ditulis atau tidak ditemukan.');
    }
}
