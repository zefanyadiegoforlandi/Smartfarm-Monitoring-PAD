<x-app-layout>
    <div class=" flex items-center justify-center py-8">

        <div class="modal-content bg-white mx-4 md:mx-auto w-full max-w-lg rounded p-8 shadow-lg">
            
            <div class="modal-header text-black text-center py-2 rounded-t" style="background-color: #C6D2B9;">
                <h2 class="text-xl md:text-2xl font-bold">Update Sensor</h2>
            </div>
            
            <div class="modal-body">
                <form action="{{ route('form-sensor.update', $sensor->id_sensor) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="mb-4">
                        <label for="id_user" class="block text-gray-700 font-bold">Id Lahan</label>
                        <select name="id_lahan" id="id_lahan" class="border border-gray-300 rounded px-3 py-2 w-full">
                            @foreach($lahan as $l)
                                <option value="{{ $l->id_lahan }}" {{ $l->id_lahan == $sensor->id_lahan ? 'selected' : '' }}>
                                    {{ $l->id_lahan }}
                                </option>
                            @endforeach    
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="nama_sensor" class="block text-gray-700 font-bold">NAMA SENSOR</label>
                        <input type="text" name="nama_sensor" id="nama_sensor" value="{{ $sensor->nama_sensor }}" class="border border-gray-300 rounded px-3 py-2 w-full">
                    </div>
                    
                    <div class="mb-4">
                        <label for="tanggal_aktivasi" class="block text-gray-700 font-bold">TANGGAL AKTIVASI</label>
                        <input type="text" name="tanggal_aktivasi" id="tanggal_aktivasi" class="border border-gray-300 rounded px-3 py-2 w-full">
                    </div>
                    <div class="flex justify-end space-x-4 mt-4">
                        <a href="/pages/add/daftar-sensor" class="px-4 py-2 text-white rounded" style="background-color: #C63838;">Batal</a>

                        <button type="submit"  class="px-4 py-2 text-white rounded" style="background-color: #416D14;" onclick="submitForm()">
                            simpan
                        </button>     
                    </div>

                </form>
            </div>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <script>
                // Inisialisasi Flatpickr pada elemen dengan ID 'tanggal_aktivasi'
                flatpickr("#tanggal_aktivasi", {
                    enableTime: true, // Aktifkan pilihan waktu
                    dateFormat: "Y-m-d H:i:s", // Format tanggal dan waktu
                    defaultDate: "{{ $sensor->tanggal_aktivasi }}", // Gunakan nilai tanggal_aktivasi dari $sensor sebagai nilai awal
                });
            </script>
    
            
    </div>
</x-app-layout>    