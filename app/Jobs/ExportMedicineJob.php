<?php

namespace App\Jobs;

use App\Models\Medicine;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Throwable;

class ExportMedicineJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    /**
     * DomPDF's layout engine does not scale to large tables: rendering
     * grows worse than linearly (500 rows ~9s, 1000 rows ~26s measured),
     * and 10k+ rows exhausts memory even with 1GB+ available. Beyond this
     * cap we fail fast with a friendly message instead of hanging/crashing.
     */
    private const PDF_ROW_LIMIT = 1000;

    public function __construct(
        private array $filters,
        private string $exportId,
        private string $format = 'xlsx'
    ) {}

    public function handle(): void
    {
        try {
            $this->putStatus('processing', 0, 0, 0);

            match ($this->format) {
                'pdf' => $this->exportPdf(),
                default => $this->exportExcel(),
            };
        } catch (Throwable $e) {
            Cache::put('export_' . $this->exportId, [
                'status' => 'failed',
                'error' => $e->getMessage()
            ], now()->addHours(2));
        }
    }

    private function exportExcel(): void
    {
        $query = Medicine::query()->filter($this->filters);

        $total = $query->count();
        $processed = 0;

        $filePath = $this->exportPath('xlsx');
        $writer = SimpleExcelWriter::create($filePath);

        $query->chunk(500, function ($medicines) use ($writer, &$processed, $total) {
            foreach ($medicines as $medicine) {
                $writer->addRow([
                    'Kode Obat' => $medicine->code,
                    'Nama Obat' => $medicine->name,
                    'Kategori' => $medicine->category,
                    'Satuan' => $medicine->unit,
                    'Harga Beli' => $medicine->purchase_price,
                    'Harga Jual' => $medicine->selling_price,
                    'Stok' => $medicine->stock,
                    'Min Stok' => $medicine->minimum_stock,
                    'Kedaluwarsa' => $medicine->expired_date,
                    'Status' => $medicine->status_label,
                    'Catatan' => $medicine->notes,
                ]);
                $processed++;
            }

            $this->putStatus('processing', $total > 0 ? (int) round(($processed / $total) * 100) : 100, $processed, $total);
        });

        $writer->close();

        $this->putStatus('completed', 100, $total, $total, route('export.download', $this->exportId));
    }

    private function exportPdf(): void
    {
        $query = Medicine::query()->filter($this->filters);
        $total = $query->count();

        if ($total > self::PDF_ROW_LIMIT) {
            $this->putStatus(
                'failed',
                0,
                0,
                $total,
                error: sprintf(
                    'Data terlalu banyak untuk PDF (%s baris, maksimum %s). Persempit pencarian/filter, atau gunakan Export Excel untuk data dalam jumlah besar.',
                    number_format($total, 0, ',', '.'),
                    number_format(self::PDF_ROW_LIMIT, 0, ',', '.')
                )
            );

            return;
        }

        // DomPDF loads the whole HTML tree in memory, so even a capped
        // table needs more headroom than a typical web request gets.
        ini_set('memory_limit', '512M');

        $this->putStatus('processing', 10, 0, $total);

        $medicines = $query->get();

        $this->putStatus('processing', 50, $total, $total);

        $pdf = Pdf::loadView('exports.pdf', [
            'medicines' => $medicines,
            'total' => $total,
            'date' => now()->format('Y-m-d H:i:s'),
        ])->setPaper('a4', 'landscape');

        $filePath = $this->exportPath('pdf');
        $pdf->save($filePath);

        $this->putStatus('completed', 100, $total, $total, route('export.download', $this->exportId));
    }

    private function exportPath(string $extension): string
    {
        $directory = 'exports';
        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        return Storage::disk('local')->path("{$directory}/medicines_{$this->exportId}.{$extension}");
    }

    private function putStatus(string $status, int $progress, int $processed, int $total, ?string $fileUrl = null, ?string $error = null): void
    {
        Cache::put('export_' . $this->exportId, [
            'status' => $status,
            'progress' => $progress,
            'processed' => $processed,
            'total' => $total,
            'file_url' => $fileUrl,
            'error' => $error,
        ], now()->addHours(2));
    }
}
