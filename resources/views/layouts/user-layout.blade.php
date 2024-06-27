<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>

    <!-- Styles -->
    @livewireStyles

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Add some styles to overlay the sidebar on top of the main content on small screens */
        @media (max-width: 1024px) {
            .overlay {
                position: fixed;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .overlay.active {
                display: block;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                z-index: 1000;
                overflow: hidden; /* Prevent scrolling */
            }
        }

        @media (min-width: 1024px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh; /* Ensure it takes the full viewport height */
                overflow: hidden; /* Prevent scrolling */
                width: 18rem; /* Same as w-72 */
                border-right: 3px solid #ccc; /* Same as xl:border xl:border-r-3 */
            }

            main {
                margin-left: 18rem; /* Adjust main content margin to make space for the sidebar */
            }
        }

        /* Add hover and active effect on menu text and images */
        .menu-item:hover .menu-text,
        .menu-item.active .menu-text {
            color: #416D14;
        }

        .menu-item:hover img,
        .menu-item.active img {
            filter: brightness(0) saturate(100%) invert(22%) sepia(73%) saturate(1395%) hue-rotate(68deg) brightness(90%) contrast(88%);
        }

        .selected-icon {
            display: inline;
        }

        .non-selected-icon {
            display: none;
        }
    </style>
</head>

<body class="font-sans">
    <div class="min-h-screen flex">

        <!-- Overlay for small screens -->
        <div class="overlay"></div>
        <!-- Sidebar -->
        <aside class="bg-[#ECF0E8] text-white w-72 p-4 hidden xl:block sidebar xl:border xl:border-r-3 xl:bg-white ">
            <!-- Close Sidebar Button -->
            <button class="close-sidebar-button text-white mb-4 xl:hidden absolute top-6 right-2">&#10006;</button>

            <!-- Sidebar Menu -->
            <nav class="mt-9 flex flex-col">
                <div class="flex text-center justify-center">
                    <img src="{{ asset('images/smartfarm_logo 2.svg') }}">
                </div>

                <div class="mt-12 ms-12">
                    <div>
                        <ul class="space-y-5 text-xl">
                            <!-- Dashboard -->
                            <li class="menu-item">
                                <a href="{{ route('user-dashboard') }}" class="flex dashboard" onclick="changeColor(this, 'dashboard')">
                                    <img src="{{ asset('images/farmer-s/dashboard.svg') }}" class="w-6 h-6 selected-icon" alt="Dashboard Icon">
                                    <span class="menu-text ms-6 text-[#818280]">Dashboard</span>
                                </a>
                            </li>

                            <!-- Pertinjau -->
                            <li class="menu-item">
                                <a href="{{ route('pertinjau.lihat') }}" class="flex pertinjau" onclick="changeColor(this, 'pertinjau')">
                                    <img src="{{ asset('images/farmer-s/pertinjau.svg') }}" class="w-6 h-6 selected-icon" alt="Lahan Icon">
                                    <span class="menu-text ms-6 text-[#818280]">Pertinjau</span>
                                </a>
                            </li>

                            <!-- Sensor -->
                            <li class="menu-item sensor-link">
                                <a href="#" class="flex" id="sensorLink" onclick="toggleSensorItems(event, this)">
                                    <img src="{{ asset('images/farmer-s/sensor.svg') }}" class="w-6 h-6 selected-icon" alt="Sensor Icon">
                                    <span class="menu-text ms-6 text-[#818280]">Sensor</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Div for 8 items (initially hidden) -->
                    <div id="sensorItems" class="hidden text-[#818280] ms-2 mt-4 space-y-2">
                        <ul class="space-y-2 text-lg">
                            <!-- Suhu -->
                            <li class="menu-item">
                                <a href="{{ route('temperature') }}" class="flex group" onclick="changeColor(this, 'suhu')">
                                    <img src="{{ asset('images/farmer-s/suhu.svg') }}" class="w-6 h-6" alt="">
                                    <span class="menu-text ms-6 text-[#818280]">Suhu</span>
                                </a>
                            </li>

                            <!-- Kelembapan -->
                            <li class="menu-item">
                                <a href="{{ route('humidity') }}" class="flex group" onclick="changeColor(this, 'kelembapan')">
                                    <img src="{{ asset('images/farmer-s/kelembapan.svg') }}" class="w-6 h-6" alt="">
                                    <span class="menu-text ms-6 text-[#818280]">Kelembapan</span>
                                </a>
                            </li>

                            <!-- Kualitas Udara -->
                            <li class="menu-item">
                                <a href="{{ route('airquality') }}" class="flex group" onclick="changeColor(this, 'kualitas-udara')">
                                    <img src="{{ asset('images/farmer-s/angin.svg') }}" class="w-6 h-6" alt="">
                                    <span class="menu-text ms-6 text-[#818280]">Kualitas Udara</span>
                                </a>
                            </li>

                            <!-- Curah Hujan -->
                            <li class="menu-item">
                                <a href="{{ route('raindrop') }}" class="flex group" onclick="changeColor(this, 'curah-hujan')">
                                    <img src="{{ asset('images/farmer-s/curah-hujan.svg') }}" class="w-6 h-6" alt="">
                                    <span class="menu-text ms-6 text-[#818280]">Curah Hujan</span>
                                </a>
                            </li>

                            <!-- Intensitas Cahaya -->
                            <li class="menu-item">
                                <a href="{{ route('light') }}" class="flex group" onclick="changeColor(this, 'intensitas-cahaya')">
                                    <img src="{{ asset('images/farmer-s/intensitasC.svg') }}" class="w-6 h-6" alt="">
                                    <span class="menu-text ms-6 text-[#818280]">Intensitas Cahaya</span>
                                </a>
                            </li>

                            <!-- Kelembapan Tanah -->
                            <li class="menu-item">
                                <a href="{{ route('persentasekelembapantanah') }}" class="flex group" onclick="changeColor(this, 'kelembapan-tanah')">
                                    <img src="{{ asset('images/farmer-s/tanah.svg') }}" class="w-6 h-6" alt="">
                                    <span class="menu-text ms-6 text-[#818280]">Kelembapan Tanah</span>
                                </a>
                            </li>

                            <!-- Ketinggian -->
                            <li class="menu-item">
                                <a href="{{ route('approxaltitude') }}" class="flex group" onclick="changeColor(this, 'ketinggian')">
                                    <img src="{{ asset('images/farmer-s/ketinggian.svg') }}" class="w-6 h-6" alt="">
                                    <span class="menu-text ms-6 text-[#818280]">Ketinggian</span>
                                </a>
                            </li>

                            <!-- Tekanan Udara -->
                            <li class="menu-item">
                                <a href="{{ route('pressure') }}" class="flex group" onclick="changeColor(this, 'tekanan-udara')">
                                    <img src="{{ asset('images/farmer-s/tekanan-udara.svg') }}" class="w-6 h-6" alt="">
                                    <span class="menu-text ms-6 text-[#818280]">Tekanan Udara</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; align-items: center; height: 100vh; margin-top: 10px;">
                    <!-- Animated Rectangle -->
                    <div id="animatedRectangle" style="width: 206px; height: 86px; background-color: #ffffff; display: none; margin-bottom: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); border-radius: 5px;">
                        <ul class=" ml-7 mt-4">
                            @foreach ([
                                ['route' => 'read-user.information', 'icon' => 'images/user_button_icon.svg', 'label' => session('user_name'), 'logout' => false],
                                ['route' => 'logout', 'icon' => 'images/logout_icon.svg', 'label' => 'Log Out', 'logout' => true]
                            ] as $item)
                                <li class="menu-item" style="list-style-type: none; margin-bottom: 10px;">
                                    @if($item['logout'])
                                        <a href="{{ route($item['route']) }}" class="flex" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="text-decoration: none; color: #818280;">
                                            <img src="{{ asset($item['icon']) }}" class="w-6 h-6 selected-icon" alt="{{ $item['label'] }} Icon" style="width: 24px; height: 24px;">
                                            <span class="menu-text ms-6" style="margin-left: 10px;">{{ $item['label'] }}</span>
                                        </a>
                                        <form id="logout-form" action="{{ route($item['route']) }}" method="POST" class="d-none" style="display: none;">
                                            @csrf
                                        </form>
                                    @else
                                        <a href="{{ route($item['route']) }}" class="flex" style="text-decoration: none; color: #818280;">
                                            <img src="{{ asset($item['icon']) }}" class="w-6 h-6 selected-icon" alt="{{ $item['label'] }} Icon" style="width: 24px; height: 24px;">
                                            <span class="menu-text ms-6" style="margin-left: 10px;">{{ $item['label'] }}</span>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Show Rectangle Button -->
                    <button id="showRectangle" style="width: 247px; height: 84px; border-radius: 24px; padding: 8px; margin-bottom: 10px; background-color:#416D14; border: none; display: flex; align-items: center; color: white; font-family: sans-serif; text-align: left;">
                        <div class="flex items-center ml-4" style="display: flex; align-items: center; margin-left: 16px;">
                            <div class="img w-14 h-14 overflow-hidden rounded-full" style="width: 56px; height: 56px; overflow: hidden; border-radius: 50%;">
                                <img src="{{ asset('images/user_besar_icon.svg') }}" alt="User Image" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="text ml-4" style="margin-left: 16px;">
                                <div class="text-xl" style="font-size: 20px;">{{ session('user_level') }}</div>
                                <!-- Menampilkan level pengguna dari sesi -->
                            </div>
                        </div>
                    </button>
                </div>

                <script>
                    // Script untuk menampilkan/menyembunyikan animatedRectangle saat tombol showRectangle diklik
                    document.getElementById('showRectangle').addEventListener('click', function() {
                        var container = document.getElementById('animatedRectangle');
                        var sensorItems = document.getElementById('sensorItems');
                        container.style.display = (container.style.display === 'none') ? 'block' : 'none';
                        // Menyembunyikan sensorItems saat animatedRectangle ditampilkan
                        if (container.style.display === 'block') {
                            sensorItems.classList.add('hidden');
                        }
                    });

                    // Toggle active class
                    function toggleSensorItems(event, element) {
                        event.preventDefault();
                        const sensorItems = document.getElementById('sensorItems');
                        const menuItems = document.querySelectorAll('.menu-item');
                        const sensorLink = document.getElementById('sensorLink');
                        const animatedRectangle = document.getElementById('animatedRectangle');

                        // Toggle the hidden class for sensor items
                        sensorItems.classList.toggle('hidden');

                        // Hide the animated rectangle if it is open
                        animatedRectangle.style.display = 'none';

                        // Remove active class from all menu items
                        menuItems.forEach(item => item.classList.remove('active'));

                        // If sensor items are visible, add active class to sensor link and change color
                        if (!sensorItems.classList.contains('hidden')) {
                            sensorLink.parentElement.classList.add('active');
                            sensorLink.querySelector('.menu-text').style.color = '#416D14';
                            sensorLink.querySelector('img').style.filter = 'brightness(0) saturate(100%) invert(22%) sepia(73%) saturate(1395%) hue-rotate(68deg) brightness(90%) contrast(88%)';
                        } else {
                            sensorLink.querySelector('.menu-text').style.color = '#818280';
                            sensorLink.querySelector('img').style.filter = 'none';
                        }
                    }

                    // Prevent closing sensor items on sub-item click
                    function preventClose(event) {
                        event.stopPropagation();
                    }

                    // Change color function
                    function changeColor(element, linkId) {
                        const allLinks = document.querySelectorAll('.menu-text');
                        const allImages = document.querySelectorAll('.menu-item img');

                        // Reset semua tautan ke warna default dan gambar ke non-selected
                        allLinks.forEach(link => {
                            link.style.color = '#818280';
                        });
                        allImages.forEach(img => {
                            img.style.filter = 'none';
                        });

                        // Ubah warna tautan yang dipilih dan tampilkan ikon yang sesuai
                        element.querySelector('.menu-text').style.color = '#416D14';
                        element.querySelector('img').style.filter = 'brightness(0) saturate(100%) invert(22%) sepia(73%) saturate(1395%) hue-rotate(68deg) brightness(90%) contrast(88%)';

                        // Simpan status ke localStorage
                        localStorage.setItem('selectedColor', '#416D14');
                        localStorage.setItem('selectedId', linkId);
                    }

                    // Periksa localStorage saat halaman dimuat
                    document.addEventListener('DOMContentLoaded', () => {
                        const selectedId = localStorage.getItem('selectedId');
                        if (selectedId) {
                            const selectedLink = document.querySelector(`a.${selectedId}`);
                            selectedLink.querySelector('.menu-text').style.color = '#416D14';
                            selectedLink.querySelector('img').style.filter = 'brightness(0) saturate(100%) invert(22%) sepia(73%) saturate(1395%) hue-rotate(68deg) brightness(90%) contrast(88%)';
                        }
                    });
                </script>

            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 xl:overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <!-- Sidebar Toggle Button -->
    <button class="toggle-button text-white xl:hidden fixed top-6 left-6">&#9776;</button>

    @livewireScripts
</body>

</html>
