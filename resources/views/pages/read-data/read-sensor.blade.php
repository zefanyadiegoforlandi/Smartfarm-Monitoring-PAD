@extends('layouts.admin-layout')

@section('content')
    <div class="flex items-center justify-center py-8">
        <div class="modal-content bg-white mx-4 md:mx-auto w-full max-w-lg rounded p-8 shadow-lg">
            <div class="modal-header text-black text-center py-2 rounded-t" style="background-color: #C6D2B9;">
                <h2 class="text-xl md:text-2xl font-bold">Informasi Sensor</h2>
            </div>
            <div class="modal-body mt-3">
                <div class="mb-4">
                    <label for="id_sensor" class="block text-gray-700 font-bold">ID Sensor</label>
                    <div id="id_sensor" class="rounded px-3 py-2 w-full">
                        {{ $sensor->id_sensor }}
                    </div>
                </div>
                <div class="mb-4">
                    <label for="id_lahan" class="block text-gray-700 font-bold">ID Lahan</label>
                    <div id="id_lahan" class="rounded px-3 py-2 w-full">
                        {{ $sensor->id_lahan }}
                    </div>
                </div>
                <div class="mb-4">
                    <label for="alamat_lahan" class="block text-gray-700 font-bold">Letak Sensor</label>
                    <div id="alamat_lahan" class="rounded px-3 py-2 w-full">
                        {{ $sensor->alamat_lahan }}
                    </div>
                </div>
                <div class="mb-4">
                    <label for="tanggal_aktivasi" class="block text-gray-700 font-bold">Tanggal Aktivasi</label>
                    <div id="tanggal_aktivasi" class="rounded px-3 py-2 w-full">
                        {{ $sensor->tanggal_aktivasi }}
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-4">
                    <button id="delete-button" class="px-4 py-2 text-white rounded" style="background-color: #C63838;">Delete</button>
                    <form id="delete-form" action="{{ route('read-sensor.destroy', ['id' => $sensor->id_sensor]) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <form action="{{ route('form-sensor.edit', $sensor->id_sensor) }}" method="GET">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-white rounded" style="background-color: #416D14;">Edit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                background: '#ffffff',
                confirmButtonColor: '#416D14',
                confirmButtonText: 'Coba Lagi',
            });
        });
    </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('delete-button').addEventListener('click', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah yakin menghapus sensor ini?',
                text: "Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#416D14',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        });
    </script>
@endsection
