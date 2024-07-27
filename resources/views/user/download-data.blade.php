@extends('layouts.user-layout')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<span class="toggle-button text-white text-4xl top-5 left-4 cursor-pointer xl:hidden">
    <img src="{{ asset('images/tonggle_sidebar.svg') }}">
</span>
<div class="container mx-auto py-8">
    <div class="flex items-center justify-end">
        <div class="relative w-[124px] h-[25px] lg:w-[160px] lg:h-[30px] group">
            <select id="filter" name="filter" onchange="saveAndSelectFilter(this.value)" class="block appearance-none w-full bg-[#416D14] border border-gray-300 text-white py-1 px-1 rounded-lg leading-tight text-center text-xs lg:text-sm font-semibold group-hover:brightness-125 group-active:brightness-150">
                @foreach ($sensors as $item)
                    <option value="{{ $item['id_sensor'] }}" {{ session('id_sensor') == $item['id_sensor'] ? 'selected' : '' }}>
                        {{ $item['nama_sensor'] }}
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                <svg class="fill-current h-4 w-4 transition duration-300 group-hover:brightness-125 group-active:brightness-150" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M5 8l5 5 5-5z" />
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
        <h2 class="text-2xl font-bold mb-4 text-black">Unduh Data</h2>
        <form id="downloadForm">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="start_date">Tanggal Mulai:</label>
                <input type="date" id="start_date" name="start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="end_date">Tanggal Selesai:</label>
                <input type="date" id="end_date" name="end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="hover:bg-green-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline hover:brightness-125 active:brightness-150" style="background-color: #416D14;">Unduh Data</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
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
                alert('Filter berhasil disimpan');
                return response.json();
            } else {
                throw new Error('Gagal memperbarui sesi.');
            }
        })
        .then(data => {
            console.log('Success:', data);
            location.reload(); // Reload the page after successfully setting the sensor
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Kesalahan: ' + error.message);
        });
    }

    document.getElementById('downloadForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah pengiriman formulir secara default

        var startDate = document.getElementById('start_date').value;
        var endDate = document.getElementById('end_date').value;
        var id_sensor = '{{ session('id_sensor') }}'; 

        alert("Start Date: " + startDate + "\nEnd Date: " + endDate + "\nID Sensor: " + id_sensor);

        if (!startDate || !endDate) {
            alert("Silakan pilih tanggal mulai dan tanggal selesai.");
            return;
        }

        if (!id_sensor) {
            alert("ID Sensor tidak ditemukan.");
            return;
        }

        console.log("Mengambil data dari API...");
        alert("Mengambil data dari API...");

        fetch(`http://localhost/smartfarm_jwt/data_sensor/${id_sensor}`)
        .then(response => {
            console.log('Respon diterima:', response);
            alert('Menerima respon dari API');
            if (!response.ok) {
                alert('Respon jaringan tidak OK');
                throw new Error("Respon jaringan tidak OK");
            }
            return response.json();
        })
        .then(data => {
            console.log("Data Diterima:", data);
            alert("Data diterima dari API");
            if (!Array.isArray(data) || data.length === 0) {
                alert("Tidak ada data yang ditemukan atau data tidak dalam format yang diharapkan.");
            }

            var filteredData = data.filter(item => {
                var timestamp = new Date(item.TimeAdded).getTime();
                var startTimestamp = new Date(startDate).getTime();
                var endTimestamp = new Date(endDate).getTime();

                return timestamp >= startTimestamp && timestamp <= endTimestamp;
            });

            console.log("Data yang Difilter:", filteredData);
            alert("Data berhasil difilter");

            if (filteredData.length === 0) {
                alert("Tidak ada data yang ditemukan untuk rentang tanggal yang dipilih.");
                return;
            }

            var csv = Papa.unparse(filteredData);
            var blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
            saveAs(blob, 'data_sensor.csv');
            alert("Data berhasil diunduh sebagai CSV");
        })
        .catch(error => {
            console.error('Kesalahan:', error);
            alert("Terjadi kesalahan saat mengambil data: " + error.message);
        });
    });
</script>
@endsection
