<x-app-layout>
    <div class="py-3">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Sukses!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Data Perdagangan Saham</h5>
                    <div>
                        <a href="{{ route('stock-data.create') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-cloud-upload me-1"></i> Upload File
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <div class="card mb-3 bg-light">
                        <div class="card-body">
                            <form method="GET" action="{{ route('stock-data.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="kode_saham" class="form-label"><i class="bi bi-search me-1"></i>Kode Saham</label>
                                    <input type="text" class="form-control" id="kode_saham" name="kode_saham" value="{{ request('kode_saham') }}" placeholder="Cari kode saham...">
                                </div>
                                <div class="col-md-3">
                                    <label for="date_from" class="form-label"><i class="bi bi-calendar me-1"></i>Tanggal Perdagangan Dari</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from', date('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="date_to" class="form-label"><i class="bi bi-calendar me-1"></i>Tanggal Perdagangan Sampai</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('stock-data.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i> Reset
                                    </a>
                                </div>
                                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                                <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
                            </form>
                        </div>
                    </div>

                    @if($stockData->total() > 0)
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-database me-1"></i>Total: {{ $stockData->total() }} data
                            </span>
                            <form method="GET" action="{{ route('stock-data.index') }}" class="d-flex align-items-center gap-2">
                                <label class="mb-0 text-muted small">Tampilkan:</label>
                                <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                    <option value="1000" {{ request('per_page') == 1000 ? 'selected' : '' }}>1000</option>
                                </select>
                                <label class="mb-0 text-muted small">baris</label>
                                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                                <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
                                <input type="hidden" name="kode_saham" value="{{ request('kode_saham') }}">
                                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'kode_saham', 'sort_order' => request('sort_by') == 'kode_saham' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Kode Saham
                                                @if(request('sort_by') == 'kode_saham')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'nama_perusahaan', 'sort_order' => request('sort_by') == 'nama_perusahaan' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Nama Perusahaan
                                                @if(request('sort_by') == 'nama_perusahaan')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-end">
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'volume', 'sort_order' => request('sort_by') == 'volume' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Volume
                                                @if(request('sort_by') == 'volume')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-end">
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'nilai', 'sort_order' => request('sort_by') == 'nilai' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Nilai
                                                @if(request('sort_by') == 'nilai')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-end">
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'frekuensi', 'sort_order' => request('sort_by') == 'frekuensi' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Frekuensi
                                                @if(request('sort_by') == 'frekuensi')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-end">
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'offer', 'sort_order' => request('sort_by') == 'offer' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Offer
                                                @if(request('sort_by') == 'offer')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-end">
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'offer_volume', 'sort_order' => request('sort_by') == 'offer_volume' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Offer Volume
                                                @if(request('sort_by') == 'offer_volume')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-end">
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'bid', 'sort_order' => request('sort_by') == 'bid' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Bid
                                                @if(request('sort_by') == 'bid')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-end">
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'bid_volume', 'sort_order' => request('sort_by') == 'bid_volume' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Bid Volume
                                                @if(request('sort_by') == 'bid_volume')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-end">
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'foreign_sell', 'sort_order' => request('sort_by') == 'foreign_sell' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Foreign Sell
                                                @if(request('sort_by') == 'foreign_sell')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-end">
                                            <a href="{{ route('stock-data.index', array_merge(request()->all(), ['sort_by' => 'foreign_buy', 'sort_order' => request('sort_by') == 'foreign_buy' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-white text-decoration-none">
                                                Foreign Buy
                                                @if(request('sort_by') == 'foreign_buy')
                                                    <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-center" width="80">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockData as $index => $data)
                                        <tr>
                                            <td>{{ $stockData->firstItem() + $index }}</td>
                                            <td><strong class="text-primary">{{ $data->kode_saham }}</strong></td>
                                            <td>{{ $data->nama_perusahaan }}</td>
                                            <td class="text-end">{{ number_format($data->volume, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($data->nilai, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($data->frekuensi, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($data->offer, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($data->offer_volume, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($data->bid, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($data->bid_volume, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($data->foreign_sell, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($data->foreign_buy, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="collapse" data-bs-target="#detail-{{ $data->id }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="detail-{{ $data->id }}">
                                            <td colspan="13" class="bg-light">
                                                <div class="p-3">
                                                    <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Detail Data {{ $data->kode_saham }}</h6>
                                                    <div class="row g-2">
                                                        <div class="col-md-3">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Kode Saham</small>
                                                                    <strong>{{ $data->kode_saham }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Nama Perusahaan</small>
                                                                    <strong>{{ $data->nama_perusahaan }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Sektor</small>
                                                                    <strong>{{ $data->sektor_industri }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Remarks</small>
                                                                    <strong>{{ $data->remarks ?? '-' }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Sebelumnya</small>
                                                                    <strong>{{ number_format($data->sebelumnya, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Open</small>
                                                                    <strong>{{ number_format($data->open_price, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">First Trade</small>
                                                                    <strong>{{ number_format($data->first_trade, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100 bg-success bg-opacity-10">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Tertinggi</small>
                                                                    <strong class="text-success">{{ number_format($data->tertinggi, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100 bg-danger bg-opacity-10">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Terendah</small>
                                                                    <strong class="text-danger">{{ number_format($data->terendah, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Tanggal</small>
                                                                    <strong>{{ $data->tanggal_perdagangan_terakhir ? $data->tanggal_perdagangan_terakhir->format('d/m/Y') : '-' }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100 bg-primary bg-opacity-10">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Volume</small>
                                                                    <strong>{{ number_format($data->volume, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100 bg-primary bg-opacity-10">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Nilai</small>
                                                                    <strong>{{ number_format($data->nilai, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100 bg-primary bg-opacity-10">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Frekuensi</small>
                                                                    <strong>{{ number_format($data->frekuensi, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Penutupan</small>
                                                                    <strong>{{ number_format($data->penutupan, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Selisih</small>
                                                                    <strong>{{ number_format($data->selisih, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Index Individual</small>
                                                                    <strong>{{ number_format($data->index_individual, 6, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Offer</small>
                                                                    <strong>{{ number_format($data->offer, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Offer Vol</small>
                                                                    <strong>{{ number_format($data->offer_volume, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Bid</small>
                                                                    <strong>{{ number_format($data->bid, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Bid Vol</small>
                                                                    <strong>{{ number_format($data->bid_volume, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Listed Shares</small>
                                                                    <strong>{{ number_format($data->listed_shares, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Tradable Shares</small>
                                                                    <strong>{{ number_format($data->tradable_shares, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Weight Index</small>
                                                                    <strong>{{ number_format($data->weight_for_index, 6, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100 bg-warning bg-opacity-10">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Foreign Sell</small>
                                                                    <strong>{{ number_format($data->foreign_sell, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card h-100 bg-warning bg-opacity-10">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Foreign Buy</small>
                                                                    <strong>{{ number_format($data->foreign_buy, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Non Reg Volume</small>
                                                                    <strong>{{ number_format($data->non_regular_volume, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Non Reg Value</small>
                                                                    <strong>{{ number_format($data->non_regular_value, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card h-100">
                                                                <div class="card-body p-2">
                                                                    <small class="text-muted d-block">Non Reg Freq</small>
                                                                    <strong>{{ number_format($data->non_regular_frequency, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $stockData->appends(['per_page' => request('per_page', 10), 'sort_by' => request('sort_by'), 'sort_order' => request('sort_order'), 'kode_saham' => request('kode_saham'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">Belum ada data</h4>
                            <p class="text-muted">Mulai dengan upload file Excel</p>
                            <a href="{{ route('stock-data.create') }}" class="btn btn-primary mt-3">
                                <i class="bi bi-cloud-upload me-2"></i> Upload File
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
