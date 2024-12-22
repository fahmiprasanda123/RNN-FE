<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
    <title>Aplikasi Harga Pangan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Aplikasi Prediksi Harga Pangan</h1>
            <p>Aplikasi ini menggunakan data latih dan data uji harga pangan dari Januari 2021 - Desember 2023 bersumber dari PIHPS</p>
        </header>
        <main>
            <form id="priceForm">
                <table>
                    <thead>
                        <tr>
                            <th>Komoditas</th>
                            <th>Provinsi</th>
                            <th>Tanggal Awal</th>
                            <th>Tanggal Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select id="commodity">
                                    <option value="beras">Beras</option>
                                    <option value="daging_ayam">Daging Ayam</option>
                                    <option value="daging_sapi">Daging Sapi</option>
                                    <option value="telur_ayam">Telur Ayam</option>
                                    <option value="bawang_merah">Bawang Merah</option>
                                    <option value="bawang_putih">Bawang Putih</option>
                                    <option value="cabai_merah">Cabai Merah</option>
                                    <option value="cabai_rawit">Cabai Rawit</option>
                                    <option value="gula_pasir">Gula Pasir</option>
                                    <option value="minyak_goreng">Minyak Goreng</option>
                                </select>
                            </td>
                            <td>
                                <select id="province">
                                    <option value="Semua Provinsi">Semua Provinsi</option>
                                    <option value="Aceh">Aceh</option>
                                    <option value="Sumatera Utara">Sumatera Utara</option>
                                    <option value="Sumatera Barat">Sumatera Barat</option>
                                    <option value="Riau">Riau</option>
                                    <option value="Kepulauan Riau">Kepulauan Riau</option>
                                    <option value="Jambi">Jambi</option>
                                    <option value="Bengkulu">Bengkulu</option>
                                    <option value="Sumatera Selatan">Sumatera Selatan</option>
                                    <option value="Kepulauan Bangka Belitung">Kepulauan Bangka Belitung</option>
                                    <option value="Lampung">Lampung</option>
                                    <option value="Banten">Banten</option>
                                    <option value="Jawa Barat">Jawa Barat</option>
                                    <option value="DKI Jakarta">DKI Jakarta</option>
                                    <option value="Jawa Tengah">Jawa Tengah</option>
                                    <option value="DI Yogyakarta">DI Yogyakarta</option>
                                    <option value="Jawa Timur">Jawa Timur</option>
                                    <option value="Bali">Bali</option>
                                    <option value="Nusa Tenggara Barat">Nusa Tenggara Barat</option>
                                    <option value="Nusa Tenggara Timur">Nusa Tenggara Timur</option>
                                    <option value="Kalimantan Barat">Kalimantan Barat</option>
                                    <option value="Kalimantan Selatan">Kalimantan Selatan</option>
                                    <option value="Kalimantan Tengah">Kalimantan Tengah</option>
                                    <option value="Kalimantan Timur">Kalimantan Timur</option>
                                    <option value="Kalimantan Utara">Kalimantan Utara</option>
                                    <option value="Gorontalo">Gorontalo</option>
                                    <option value="Sulawesi Selatan">Sulawesi Selatan</option>
                                    <option value="Sulawesi Tenggara">Sulawesi Tenggara</option>
                                    <option value="Sulawesi Tengah">Sulawesi Tengah</option>
                                    <option value="Sulawesi Utara">Sulawesi Utara</option>
                                    <option value="Sulawesi Barat">Sulawesi Barat</option>
                                    <option value="Maluku">Maluku</option>
                                    <option value="Maluku Utara">Maluku Utara</option>
                                    <option value="Papua">Papua</option>
                                    <option value="Papua Barat">Papua Barat</option>
                                </select>
                            </td>
                            <td><input type="date" id="startDate"></td>
                            <td><input type="date" id="endDate"></td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit">Cari Harga</button>
            </form>

            <div class="price-table" id="priceTable" style="display:none;">
                <h2>Harga (Rp)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody id="priceData">
                        <!-- Data harga akan ditampilkan di sini -->
                    </tbody>
                </table>
            </div>

            <canvas id="myLineChart" width="400" height="200" style="display:none;"></canvas>
            <canvas id="myBarChart" width="400" height="200" style="display:none;"></canvas>
            <canvas id="myPieChart" width="400" height="200" style="display:none;"></canvas>
        </main>
    </div>

    <script>
        let myLineChart; // Variabel global untuk grafik garis
        let myBarChart; // Variabel global untuk grafik batang
        let myPieChart; // Variabel global untuk diagram lingkaran

        document.getElementById('priceForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const commodity = document.getElementById('commodity').value;
            const province = document.getElementById('province').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            // Mengubah format tanggal dari YYYY-MM-DD ke DD/MM/YYYY
            const formattedStartDate = startDate.split('-').reverse().join('/');
            const formattedEndDate = endDate.split('-').reverse().join('/');

            const url = 'https://pangan.prasanda.com/flask/predict'; // Ganti dengan URL API Anda

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    start_date: formattedStartDate,
                    end_date: formattedEndDate,
                    sheet_name: commodity,
                    province: province,
                }),
            })
            .then(response => response.json())
            .then(data => {
                // Mengosongkan data sebelumnya
                const priceData = document.getElementById('priceData');
                priceData.innerHTML = '';

                // Menyimpan data untuk diagram
                const labels = [];
                const prices = [];

                // Memastikan data diolah dengan benar
                Object.keys(data).forEach(date => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${date}</td>
                        <td>${data[date]}</td>
                    `;
                    priceData.appendChild(row);

                    // Menyimpan data untuk diagram
                    labels.push(date);
                    prices.push(data[date]);
                });

                // Menampilkan tabel harga
                document.getElementById('priceTable').style.display = 'block';

                // Menghapus grafik sebelumnya jika ada
                if (myLineChart) {
                    myLineChart.destroy();
                }
                if (myBarChart) {
                    myBarChart.destroy();
                }
                if (myPieChart) {
                    myPieChart.destroy();
                }

                // Menampilkan grafik garis
                const ctxLine = document.getElementById('myLineChart').getContext('2d');
                myLineChart = new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Harga (Garis)',
                            data: prices,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            fill: false
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Menampilkan grafik batang
                const ctxBar = document.getElementById('myBarChart').getContext('2d');
                myBarChart = new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Harga (Batang)',
                            data: prices,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Menampilkan diagram lingkaran
                const totalPrices = prices.reduce((a, b) => a + b, 0);
                const pieData = prices.map(price => (price / totalPrices) * 100); // Persentase

                const ctxPie = document.getElementById('myPieChart').getContext('2d');
                myPieChart = new Chart(ctxPie, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Proporsi Harga',
                            data: pieData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Proporsi Harga'
                            }
                        }
                    }
                });

                // Menampilkan canvas
                document.getElementById('myLineChart').style.display = 'block';
                document.getElementById('myBarChart').style.display = 'block';
                document.getElementById('myPieChart').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
