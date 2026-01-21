<?php

namespace App\Http\Controllers;

use App\Models\StockData;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StockDataController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by') ?: 'created_at';
        $sortOrder = $request->input('sort_order') ?: 'desc';
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }
        
        $query = StockData::query();
        
        // Filter by kode saham
        if ($request->filled('kode_saham')) {
            $query->where('kode_saham', 'ILIKE', '%' . $request->kode_saham . '%');
        }
        
        // Filter by date range (tanggal perdagangan) - default hari ini
        $dateFrom = $request->input('date_from', date('Y-m-d'));
        $dateTo = $request->input('date_to', date('Y-m-d'));
        
        if ($dateFrom) {
            $query->whereDate('tanggal_perdagangan_terakhir', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('tanggal_perdagangan_terakhir', '<=', $dateTo);
        }
        
        $stockData = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
        return view('stock-data.index', compact('stockData'));
    }

    public function create()
    {
        return view('stock-data.upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            $header = array_shift($rows);
            
            $imported = 0;
            foreach ($rows as $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                StockData::create([
                    'kode_saham' => $row[1] ?? null,
                    'nama_perusahaan' => $row[2] ?? null,
                    'remarks' => $row[3] ?? null,
                    'sebelumnya' => $row[4] ?? null,
                    'open_price' => $row[5] ?? null,
                    'tanggal_perdagangan_terakhir' => !empty($row[6]) ? $this->parseExcelDate($row[6]) : null,
                    'first_trade' => $row[7] ?? null,
                    'tertinggi' => $row[8] ?? null,
                    'terendah' => $row[9] ?? null,
                    'penutupan' => $row[10] ?? null,
                    'selisih' => $row[11] ?? null,
                    'volume' => $row[12] ?? null,
                    'nilai' => $row[13] ?? null,
                    'frekuensi' => $row[14] ?? null,
                    'index_individual' => $row[15] ?? null,
                    'offer' => $row[16] ?? null,
                    'offer_volume' => $row[17] ?? null,
                    'bid' => $row[18] ?? null,
                    'bid_volume' => $row[19] ?? null,
                    'listed_shares' => $row[20] ?? null,
                    'tradable_shares' => $row[21] ?? null,
                    'weight_for_index' => $row[22] ?? null,
                    'foreign_sell' => $row[23] ?? null,
                    'foreign_buy' => $row[24] ?? null,
                    'non_regular_volume' => $row[25] ?? null,
                    'non_regular_value' => $row[26] ?? null,
                    'non_regular_frequency' => $row[27] ?? null,
                ]);
                $imported++;
            }

            return redirect()->route('stock-data.index')
                ->with('success', "Berhasil mengimport {$imported} data saham.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengupload file: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $stockData = StockData::findOrFail($id);
        $stockData->delete();

        return redirect()->route('stock-data.index')
            ->with('success', 'Data berhasil dihapus.');
    }

    public function deleteAll()
    {
        StockData::truncate();
        
        return redirect()->route('stock-data.index')
            ->with('success', 'Semua data berhasil dihapus.');
    }

    private function parseExcelDate($value)
    {
        if (is_numeric($value)) {
            // Excel date format (days since 1900-01-01)
            $unix_date = ($value - 25569) * 86400;
            return date('Y-m-d', $unix_date);
        }
        
        return $value;
    }
}
