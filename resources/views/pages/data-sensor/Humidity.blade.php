@extends('layouts.user-layout')

@section('content')
<div class="container mx-auto px-4 md:px-8 lg:px-16 xl:px-20">
    <div class="mt-5 flex flex-col md:flex-row md:items-center justify-between">
        <div class="flex items-center justify-start">
            <span class="toggle-button text-white text-4xl top-5 left-4 cursor-pointer xl:hidden">
                <img src="{{ asset('images/tonggle_sidebar.svg') }}">
            </span>
            <p class="font-semibold text-3xl md:text-4xl text-[#416D14]">Humidity</p>
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
            <canvas id="humidityChart" class="w-full"></canvas>
        </div>
    </div>

    <div class="overflow-x-auto mt-5">
        <table id="humidity-table" class="w-full">
            <thead class="bg-[#ECF0E8]">
                <tr>
                    <th class="p-2 text-[#416D14] uppercase">Time</th>
                    <th class="p-2 text-[#416D14] uppercase">Date</th>
                    <th class="p-2 text-[#416D14] uppercase">ID Sensor</th>
                    <th class="p-2 text-[#416D14] uppercase">Humidity</th>
                </tr>
            </thead>
            
            <tbody id="data-container">
                @foreach ($dataTabel as $ds)
                    <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-[#ecf0e82e]' : 'bg-white' }}">
                        <td class="py-2 px-4 border-b text-center">{{ date('H:i:s', strtotime($ds['TimeAdded'])) }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ date('Y-m-d', strtotime($ds['TimeAdded'])) }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $ds['id_sensor'] }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $ds['H'] }}</td>
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
        $.get('/update-data-table-Humidity', function(response) {
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
                        <td class="py-2 px-4 border-b text-center">${ds['H']}</td>
                    </tr>
                `;
            }
            $('#data-container').html(newDataHtml);
        });
    }

    setInterval(reloadData, 2000);

    var tableData = {!! json_encode($dataSensor) !!};
    var labels = tableData.map(entry => entry.TimeAdded);
    var humidity = tableData.map(entry => entry.H);

    var ctx = document.getElementById('humidityChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Humidity',
                data: humidity,
                borderColor: '#416D14',
                borderWidth: 1,
                pointBackgroundColor: '#416D14',
                pointBorderColor: '#fff',
                pointBorderWidth: 1,
                pointRadius: 2,
                pointHoverRadius: 4,
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
                        text: 'Humidity (%)',
                        font: {
                            size: 14 
                        },
                        color: '#416D14'
                    },
                    ticks: {
                        color: '#000000',
                        font: {
                            size: 12
                        }
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

    function fetchDataAndUpdateChart() {
        fetch('/update-data-grafik-Humidity')
            .then(response => response.json())
            .then(data => {
                var newData = data.Humidity;
                var newLabels = data.TimeAdded;

                myChart.data.datasets[0].data = newData;
                myChart.data.labels = newLabels;
                myChart.update();
            })
            .catch(error => console.error('Error:', error));
    }
    setInterval(fetchDataAndUpdateChart, 2000);

    function filterChanged() {
        console.log('Filter changed');
    }

    document.addEventListener('DOMContentLoaded', filterChanged);
</script>
@endsection
