@extends('layouts.user-layout')

@section('content')
<div class="container mx-auto px-4 md:px-8 lg:px-16 xl:px-20">
    <div class="mt-5 flex flex-col md:flex-row md:items-center justify-between">
        <div class="flex items-center justify-start">
            <span class="toggle-button text-white text-4xl top-5 left-4 cursor-pointer xl:hidden">
                <img src="{{ asset('images/tonggle_sidebar.svg') }}">
            </span>
            <p class="font-semibold text-3xl md:text-4xl text-[#416D14]">Intensitas Cahaya</p>
        </div>
        <div class="flex items-center justify-end space-x-4"> <!-- Added space-x-4 for spacing -->
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
    <div class="flex items-center justify-start group mt-5">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-arrow-left text-[#416D14] transition duration-300 group-hover:brightness-125 group-active:brightness-150" viewBox="0 0 16 16">
            <path fill="currentColor" d="M5.854 4.646a.5.5 0 0 1 0 .708L2.207 9H13.5a.5.5 0 0 1 0 1H2.207l3.647 3.646a.5.5 0 0 1-.708.708l-4.5-4.5a.5.5 0 0 1 0-.708l4.5-4.5a.5.5 0 0 1 .708 0z"/>
        </svg>
        <a href="{{ route('light') }}" class="font-medium text-[16px] md:text-[20px] text-[#416D14] ms-1 group-hover:brightness-125 group-active:brightness-150">kembali</a>
    </div>
    
    
    
    <div class="overflow-x-auto mt-2">
        <table id="airQuality-table" class="w-full">
            <thead class="bg-[#ECF0E8]">
                <tr>
                    <th class="p-2 text-[#416D14] uppercase">Time</th>
                    <th class="p-2 text-[#416D14] uppercase">Date</th>
                    <th class="p-2 text-[#416D14] uppercase">ID Sensor</th>
                    <th class="p-2 text-[#416D14] uppercase">Intensitas Cahaya</th>
                </tr>
            </thead>
            <tbody id="data-container">
                @if (count($paginator['data']) > 0)
                    @foreach ($paginator['data'] as $ds)
                        <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-[#ecf0e82e]' : 'bg-white' }}">
                            <td class="py-2 px-4 border-b text-center">{{ date('H:i:s', strtotime($ds['TimeAdded'])) }}</td>
                            <td class="py-2 px-4 border-b text-center">{{ date('Y-m-d', strtotime($ds['TimeAdded'])) }}</td>
                            <td class="py-2 px-4 border-b text-center">{{ $ds['id_sensor'] }}</td>
                            <td class="py-2 px-4 border-b text-center">{{ $ds['Light'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="py-4 text-center text-[#416D14] text-2xl font-semibold">Sensor tidak memiliki data</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <nav class="w-full flex justify-center mt-5" aria-label="Page navigation example">
        <ul class="list-style-none flex">
            {{-- Tombol Previous --}}
            @if ($paginator['prev_page_url'])
                <li>
                    <a class="flex items-center justify-center rounded-full bg-gray-300 text-sm text-neutral-600 transition-all duration-300 hover:bg-[#CAE8AC] dark:text-white dark:hover:bg-green-700 dark:hover:text-white circle-button"
                       href="{{ $paginator['prev_page_url'] }}"
                       style="width: 19px; height: 19px; line-height: 19px;">
                        &lt;
                    </a>
                </li>
            @else
                <li>
                    <a class="pointer-events-none flex items-center justify-center rounded-full hover:bg-[#CAE8AC] bg-gray-300 text-sm text-neutral-500 transition-all duration-300 dark:text-neutral-400 circle-button"
                       style="width: 19px; height: 19px; line-height: 19px;">
                        &lt;
                    </a>
                </li>
            @endif

            {{-- Paginator Halaman --}}
            @foreach (range(1, $paginator['last_page']) as $page)
                @if ($page == 1 || $page == $paginator['last_page'] || ($page >= $paginator['current_page'] - 2 && $page <= $paginator['current_page'] + 2))
                    <li aria-current="{{ $page == $paginator['current_page'] ? 'page' : '' }}">
                        <a class="relative block flex items-center justify-center
                                @if ($page == $paginator['current_page'])
                                bg-[#CAE8AC] text-black
                                @else
                                bg-transparent text-neutral-600 hover:bg-[#CAE8AC] dark:text-white dark:hover:bg-[#CAE8AC] dark:hover:text-white
                                @endif
                                mx-1 text-sm font-medium transition-all duration-300"
                           href="{{ $page == $paginator['current_page'] ? '#' : $paginator['path'] . '?page=' . $page }}"
                           style="width: 19px; height: 19px;">
                            {{ $page }}
                        </a>
                    </li>
                @elseif ($page == $paginator['current_page'] - 3 || $page == $paginator['current_page'] + 3)
                    <li>
                        <span class="relative block text-sm text-neutral-600 transition-all duration-300 dark:text-white dark:hover:bg-[#CAE8AC] dark:hover:text-white">
                            ...
                        </span>
                    </li>
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator['next_page_url'])
                <li>
                    <a class="flex items-center justify-center relative block rounded-full bg-gray-300 text-sm text-neutral-600 transition-all duration-300 hover:bg-[#CAE8AC] dark:text-white dark:hover:bg-green-700 dark:hover:text-white"
                       style="width: 19px; height: 19px; line-height: 19px;"
                       href="{{ $paginator['next_page_url'] }}">
                        &gt;
                    </a>
                </li>
            @else
                <li>
                    <a class="flex items-center justify-center relative block rounded-full bg-gray-300 text-sm text-neutral-500 transition-all duration-300 hover:bg-[#CAE8AC] dark:hover:bg-gray-700 dark:hover:text-white"
                       href="#!"
                       style="width: 19px; height: 19px; line-height: 19px;">
                        &gt;
                    </a>
                </li>
            @endif
        </ul>
    </nav>
</div>

<script>
    function openModal() {
        document.getElementById('downloadModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('downloadModal').classList.add('hidden');
    }

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

    document.addEventListener('DOMContentLoaded', function() {
        
    });
</script>
@endsection
