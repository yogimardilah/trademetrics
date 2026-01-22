<x-app-layout>
    <div class="py-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up-arrow me-2"></i>Chart Saham: {{ $kode_saham }}
                </h5>
                <div>
                    <a href="{{ route('stock-data.index', array_filter(['kode_saham' => $kode_saham, 'date_from' => $date_from, 'date_to' => $date_to])) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('stock-data.charts', ['kode_saham' => $kode_saham]) }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-calendar me-1"></i>Tanggal Dari</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $date_from ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-calendar me-1"></i>Tanggal Sampai</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $date_to ?? '' }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light"><strong>Foreign Sell vs Foreign Buy</strong></div>
                        <div class="card-body"><canvas id="chartForeign"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light"><strong>Bid vs Offer</strong></div>
                        <div class="card-body"><canvas id="chartBidOffer"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light"><strong>Bid Volume vs Offer Volume</strong></div>
                        <div class="card-body"><canvas id="chartBidOfferVolume"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light"><strong>Volume</strong></div>
                        <div class="card-body"><canvas id="chartVolume"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light"><strong>Frekuensi</strong></div>
                        <div class="card-body"><canvas id="chartFrekuensi"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light"><strong>Nilai</strong></div>
                        <div class="card-body"><canvas id="chartNilai"></canvas></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($chart['labels']);
        const datasets = {
            foreign_sell: @json($chart['foreign_sell']),
            foreign_buy: @json($chart['foreign_buy']),
            offer: @json($chart['offer']),
            offer_volume: @json($chart['offer_volume']),
            bid: @json($chart['bid']),
            bid_volume: @json($chart['bid_volume']),
            volume: @json($chart['volume']),
            frekuensi: @json($chart['frekuensi']),
            nilai: @json($chart['nilai']),
        };

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { ticks: { autoSkip: true, maxTicksLimit: 10 } } },
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top' } }
        };

        function lineChart(ctx, series) {
            return new Chart(ctx, {
                type: 'line',
                data: { labels, datasets: series },
                options: commonOptions
            });
        }

        function barChart(ctx, series) {
            return new Chart(ctx, {
                type: 'bar',
                data: { labels, datasets: series.map(s => ({
                    ...s,
                    backgroundColor: s.backgroundColor || s.borderColor,
                    borderColor: s.borderColor || s.backgroundColor,
                    borderWidth: 1
                })) },
                options: {
                    ...commonOptions,
                    scales: {
                        x: commonOptions.scales.x,
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        function color(name) {
            const map = {
                blue: 'rgba(54, 162, 235, 1)',
                blueT: 'rgba(54, 162, 235, 0.3)',
                green: 'rgba(75, 192, 192, 1)',
                greenT: 'rgba(75, 192, 192, 0.3)',
                red: 'rgba(255, 99, 132, 1)',
                redT: 'rgba(255, 99, 132, 0.3)',
                orange: 'rgba(255, 159, 64, 1)',
                orangeT: 'rgba(255, 159, 64, 0.3)',
                purple: 'rgba(153, 102, 255, 1)',
                purpleT: 'rgba(153, 102, 255, 0.3)'
            };
            return map[name];
        }

        // 1. Foreign sell vs buy
        lineChart(document.getElementById('chartForeign').getContext('2d'), [
            { label: 'Foreign Sell', data: datasets.foreign_sell, borderColor: color('red'), backgroundColor: color('redT'), tension: 0.2 },
            { label: 'Foreign Buy', data: datasets.foreign_buy, borderColor: color('green'), backgroundColor: color('greenT'), tension: 0.2 },
        ]);

        // 2. Bid vs Offer
        lineChart(document.getElementById('chartBidOffer').getContext('2d'), [
            { label: 'Bid', data: datasets.bid, borderColor: color('purple'), backgroundColor: color('purpleT'), tension: 0.2 },
            { label: 'Offer', data: datasets.offer, borderColor: color('blue'), backgroundColor: color('blueT'), tension: 0.2 },
        ]);

        // 3. Bid Volume vs Offer Volume
        lineChart(document.getElementById('chartBidOfferVolume').getContext('2d'), [
            { label: 'Bid Volume', data: datasets.bid_volume, borderColor: color('orange'), backgroundColor: color('orangeT'), tension: 0.2 },
            { label: 'Offer Volume', data: datasets.offer_volume, borderColor: color('green'), backgroundColor: color('greenT'), tension: 0.2 },
        ]);

        // 4. Volume (bar)
        barChart(document.getElementById('chartVolume').getContext('2d'), [
            { label: 'Volume', data: datasets.volume, borderColor: color('blue'), backgroundColor: color('blueT') },
        ]);

        // 5. Frekuensi (bar)
        barChart(document.getElementById('chartFrekuensi').getContext('2d'), [
            { label: 'Frekuensi', data: datasets.frekuensi, borderColor: color('green'), backgroundColor: color('greenT') },
        ]);

        // 6. Nilai (bar)
        barChart(document.getElementById('chartNilai').getContext('2d'), [
            { label: 'Nilai', data: datasets.nilai, borderColor: color('red'), backgroundColor: color('redT') },
        ]);
    </script>
</x-app-layout>
