<x-app-layout>
    <div class="py-3">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Upload File Excel Data Saham</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <a href="{{ route('stock-data.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Data
                        </a>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('stock-data.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="files" class="form-label fw-bold">
                                <i class="bi bi-file-earmark-excel me-1"></i> Pilih File Excel (.xlsx, .xls, .csv) — bisa pilih banyak file
                            </label>
                            <input type="file" 
                                   name="files[]" 
                                   id="files" 
                                   accept=".xlsx,.xls,.csv"
                                   multiple
                                   class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror"
                                   required>
                            @error('files')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Upload bisa beberapa file sekaligus. Maksimal ukuran tiap file 10MB.
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="bi bi-table me-2"></i>Format File:</h6>
                            <p class="mb-2">File harus memiliki kolom-kolom berikut (sesuai urutan):</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <ol class="mb-0 small" start="0">
                                        <li><strong>No</strong> (tidak akan diimport)</li>
                                        <li>Kode Saham</li>
                                        <li>Nama Perusahaan</li>
                                        <li>Remarks</li>
                                        <li>Sebelumnya</li>
                                        <li>Open Price</li>
                                        <li>Tanggal Perdagangan Terakhir</li>
                                        <li>First Trade</li>
                                        <li>Tertinggi</li>
                                        <li>Terendah</li>
                                        <li>Penutupan</li>
                                        <li>Selisih</li>
                                        <li>Volume</li>
                                        <li>Nilai</li>
                                    </ol>
                                </div>
                                <div class="col-md-6">
                                    <ol start="14" class="mb-0 small">
                                        <li>Frekuensi</li>
                                        <li>Index Individual</li>
                                        <li>Offer</li>
                                        <li>Offer Volume</li>
                                        <li>Bid</li>
                                        <li>Bid Volume</li>
                                        <li>Listed Shares</li>
                                        <li>Tradable Shares</li>
                                        <li>Weight for Index</li>
                                        <li>Foreign Sell</li>
                                        <li>Foreign Buy</li>
                                        <li>Non Regular Volume</li>
                                        <li>Non Regular Value</li>
                                        <li>Non Regular Frequency</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('stock-data.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-upload me-2"></i> Upload & Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
