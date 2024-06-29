<aside class="bg-[#ECF0E8] text-white w-72 p-4 sidebar xl:border xl:border-r-3 xl:bg-white ">
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
                    @foreach ([
                        ['route' => 'temperature', 'icon' => 'images/farmer-s/suhu.svg', 'label' => 'Suhu'],
                        ['route' => 'humidity', 'icon' => 'images/farmer-s/kelembapan.svg', 'label' => 'Kelembapan'],
                        ['route' => 'airquality', 'icon' => 'images/farmer-s/angin.svg', 'label' => 'Kualitas Udara'],
                        ['route' => 'raindrop', 'icon' => 'images/farmer-s/curah-hujan.svg', 'label' => 'Curah Hujan'],
                        ['route' => 'light', 'icon' => 'images/farmer-s/intensitasC.svg', 'label' => 'Intensitas Cahaya'],
                        ['route' => 'persentasekelembapantanah', 'icon' => 'images/farmer-s/tanah.svg', 'label' => 'Kelembapan Tanah'],
                        ['route' => 'approxaltitude', 'icon' => 'images/farmer-s/ketinggian.svg', 'label' => 'Ketinggian'],
                        ['route' => 'pressure', 'icon' => 'images/farmer-s/tekanan-udara.svg', 'label' => 'Tekanan Udara'],
                    ] as $item)
                    <li class="menu-item">
                        <a href="{{ route($item['route']) }}" class="flex group" onclick="changeColor(this, '{{ $item['route'] }}')">
                            <img src="{{ asset($item['icon']) }}" class="w-6 h-6" alt="{{ $item['label'] }} Icon">
                            <span class="menu-text ms-6 text-[#818280]">{{ $item['label'] }}</span>
                        </a>
                    </li>
                    @endforeach
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
