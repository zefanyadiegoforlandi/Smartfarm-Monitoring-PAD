@extends('layouts.user-layout')

@section('content')
<div class="container mx-auto px-4 md:px-8 lg:px-16 xl:px-20">
    <div class="mt-5 flex flex-col md:flex-row md:items-center justify-between">
        <div class="flex items-center justify-start">
            <span class="toggle-button text-white text-4xl top-5 left-4 cursor-pointer xl:hidden">
                <img src="{{ asset('images/tonggle_sidebar.svg') }}">
            </span>
            <p class="font-semibold text-3xl md:text-4xl text-[#416D14]">Ketinggian</p>
        </div>
        <div class="flex items-center justify-end">
            <div class="relative w-[124px] h-[25px] lg:w-[160px] lg:h-[30px]">
                <select id="filter" name="filter" onchange="saveAndSelectFilter(this.value)" class="block appearance-none w-full bg-[#416D14] border border-gray-300 text-white py-1 px-1 rounded-lg leading-tight text-center text-xs lg:text-sm font-semibold">
                    @foreach ($sensors as $item)
                        <option value="{{ $item['id_sensor'] }}" {{ session('id_sensor') == $item['id_sensor'] ? 'selected' : '' }}>
                            {{ $item['nama_sensor'] }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M5 8l5 5 5-5z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="bg-white border-transparent rounded-lg shadow-xl">
            <div class="bg-[#ECF0E8] rounded-tl-lg rounded-tr-lg">
                <h2 class="text-lg text-[#416D14] font-semibold p-2">Grafik</h2>
            </div>
            <canvas id="approxAltitudeChart" class="w-full"></canvas>
        </div>
    </div>

    <div class="flex items-center justify-end group mt-5">
        <a href="{{ route('history.ApproxAltitude') }}" class="font-medium text-[16px] md:text-[20px] text-[#416D14] me-1 group-hover:brightness-125 group-active:brightness-150">lihat selengkapnya</a>
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right-short text-[#416D14] transition duration-300 group-hover:brightness-125 group-active:brightness-150" viewBox="0 0 16 16">
            <path fill="currentColor" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
        </svg>
    </div>
    
    <div class="overflow-x-auto mt-2">
        <table id="approxAltitude-table" class="w-full">
            <thead class="bg-[#ECF0E8]">
                <tr>
                    <th class="p-2 text-[#416D14] uppercase">Time</th>
                    <th class="p-2 text-[#416D14] uppercase">Date</th>
                    <th class="p-2 text-[#416D14] uppercase">ID Sensor</th>
                    <th class="p-2 text-[#416D14] uppercase">Ketinggian</th>
                </tr>
            </thead>
            
            <tbody id="data-container">
                @foreach ($dataTabel as $ds)
                    <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-[#ecf0e82e]' : 'bg-white' }}">
                        <td class="py-2 px-4 border-b text-center">{{ date('H:i:s', strtotime($ds['TimeAdded'])) }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ date('Y-m-d', strtotime($ds['TimeAdded'])) }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $ds['id_sensor'] }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $ds['ApproxAltitude'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function saveAndSelectFilter(id_sensor) {
        fetch('{{ route('set-sensor') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id_sensor: id_sensor })
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error('Failed to update session.');
            }
        })
        .then(data => {
            console.log('Success:', data);
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }

    function reloadData() {
        $.get('/update-data-table-ApproxAltitude', function(response) {
            console.log(response);
            let newDataHtml = '';
            for (let i = response.dataSensor.length - 1; i >= 0; i--) {
                const ds = response.dataSensor[i];
                const rowClass = (response.dataSensor.length - i) % 2 === 0 ? 'bg-[#ecf0e82e]' : 'bg-white';

                // Memisahkan waktu dan tanggal dari TimeAdded
                const timeAdded = ds['TimeAdded'];
                const [date, time] = timeAdded.split(' ');

                newDataHtml += `
                    <tr class="${rowClass}">
                        <td class="py-2 px-4 border-b text-center">${time}</td>
                        <td class="py-2 px-4 border-b text-center">${date}</td>
                        <td class="py-2 px-4 border-b text-center">${ds['id_sensor']}</td>
                        <td class="py-2 px-4 border-b text-center">${ds['ApproxAltitude']}</td>
                    </tr>
                `;
            }
            $('#data-container').html(newDataHtml);
        });
    }

    setInterval(reloadData, 2000);

   // Mengambil data sensor dari PHP ke JavaScript
var tableData = {!! json_encode($dataSensor) !!};

// Memisahkan data label dan ApproxAltitude dari data sensor
var labels = tableData.map(entry => entry.TimeAdded);
var approxAltitude = tableData.map(entry => entry.ApproxAltitude);

// Menentukan batas atas dan bawah untuk sumbu y
var maxDataValue = Math.max(...approxAltitude);
var minDataValue = Math.min(...approxAltitude);
var upperBound = maxDataValue + 10;
var lowerBound = minDataValue - 10;

// Membuat grafik dengan Chart.js
var ctx = document.getElementById('approxAltitudeChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Approx Altitude',
            data: approxAltitude,
            borderColor: '#416D14',
            borderWidth: 1,
            pointBackgroundColor: '#416D14',
            pointBorderColor: '#fff',
            pointBorderWidth: 1,
            pointRadius: 0,
            pointHoverRadius: 1,
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#416D14',
            pointHoverBorderWidth: 2,
            fill: false
        }]
    },
    options: {
        scales: {
            y: {
                title: {
                    display: true,
                    text: 'Altitude',
                    font: {
                        size: 14 
                    },
                    color: '#416D14'
                },
                min: lowerBound, 
                max: upperBound, 
                ticks: {
                    color: '#000000',
                    font: {
                        size: 12
                    },
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Time',
                    font: {
                        size: 14 
                    },
                    color: '#416D14'
                },
                ticks: {
                    color: '#000000',
                    font: {
                        size: 10 
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            }
        },
        animation: {
            duration: 1000,
            easing: 'easeInOutQuart'
        }
    }
});

// Fungsi untuk memperbarui grafik dengan data baru
function fetchDataAndUpdateChart() {
    fetch('/update-data-grafik-ApproxAltitude')
        .then(response => response.json())
        .then(data => {
            var newData = data.ApproxAltitude;
            var newLabels = data.TimeAdded;

            myChart.data.datasets[0].data = newData;
            myChart.data.labels = newLabels;

            var newMaxDataValue = Math.max(...newData);
            var newMinDataValue = Math.min(...newData);
            myChart.options.scales.y.min = newMinDataValue - 10;
            myChart.options.scales.y.max = newMaxDataValue + 10;

            myChart.update();
        })
        .catch(error => console.error('Error:', error));
}

// Memperbarui data grafik setiap 2 detik
setInterval(fetchDataAndUpdateChart, 2000);

    function filterChanged() {
        console.log('Filter changed');
    }

    document.addEventListener('DOMContentLoaded', filterChanged);
</script>
@endsection
