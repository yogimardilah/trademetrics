<?php

namespace App\Http\Controllers;

use App\Models\StockData;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        
        if (is_string($value)) {
            $raw = trim($value);
            // Handle Indonesian month abbreviations: Jan Feb Mar Apr Mei Jun Jul Agt Sep Okt Nov Des
            $months = [
                'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04', 'mei' => '05',
                'jun' => '06', 'jul' => '07', 'agt' => '08', 'agu' => '08', 'ags' => '08',
                'sep' => '09', 'okt' => '10', 'nov' => '11', 'des' => '12',
            ];

            // Try generic token split: e.g. "30 Des 2025", "30-Des-2025", "30/Des/2025"
            $tokens = preg_split('/[\s\-\/]+/u', $raw);
            if (count($tokens) === 3) {
                [$d, $m, $y] = $tokens;
                if (ctype_digit($d) && ctype_digit($y)) {
                    $mNum = null;
                    $mLower = strtolower($m);
                    if (isset($months[$mLower])) {
                        $mNum = $months[$mLower];
                    } elseif (ctype_digit($m) && (int)$m >= 1 && (int)$m <= 12) {
                        $mNum = str_pad($m, 2, '0', STR_PAD_LEFT);
                    }
                    if ($mNum) {
                        $day = str_pad($d, 2, '0', STR_PAD_LEFT);
                        $year = strlen($y) === 2 ? (int)($y) + 2000 : (int)$y;
                        return sprintf('%04d-%02d-%02d', $year, (int)$mNum, (int)$day);
                    }
                }
            }

            // Fallback to strtotime if possible (handles many English formats)
            $ts = strtotime($raw);
            if ($ts !== false) {
                return date('Y-m-d', $ts);
            }
        }

        // Return as-is when no known parsing applies
        return $value;
    }

    public function charts(Request $request, string $kode_saham)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = StockData::query()
            ->where('kode_saham', $kode_saham);

        if ($dateFrom) {
            $query->whereDate('tanggal_perdagangan_terakhir', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('tanggal_perdagangan_terakhir', '<=', $dateTo);
        }

        // Default window if no dates provided: last 30 days
        if (!$dateFrom && !$dateTo) {
            $query->whereDate('tanggal_perdagangan_terakhir', '>=', date('Y-m-d', strtotime('-30 days')));
        }

        $rows = $query
            ->orderBy('tanggal_perdagangan_terakhir', 'asc')
            ->get([
                'tanggal_perdagangan_terakhir',
                'foreign_sell', 'foreign_buy',
                'offer', 'offer_volume',
                'bid', 'bid_volume',
                'volume', 'frekuensi', 'nilai'
            ]);

        $labels = $rows->map(function ($r) {
            $d = $r->tanggal_perdagangan_terakhir;
            if ($d instanceof \Carbon\Carbon) {
                return $d->format('Y-m-d');
            }
            return (string) $d;
        });

        $data = [
            'labels' => $labels->values(),
            'foreign_sell' => $rows->pluck('foreign_sell')->values(),
            'foreign_buy' => $rows->pluck('foreign_buy')->values(),
            'offer' => $rows->pluck('offer')->values(),
            'offer_volume' => $rows->pluck('offer_volume')->values(),
            'bid' => $rows->pluck('bid')->values(),
            'bid_volume' => $rows->pluck('bid_volume')->values(),
            'volume' => $rows->pluck('volume')->values(),
            'frekuensi' => $rows->pluck('frekuensi')->values(),
            'nilai' => $rows->pluck('nilai')->values(),
        ];

        return view('stock-data.charts', [
            'kode_saham' => $kode_saham,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'chart' => $data,
        ]);
    }
}
