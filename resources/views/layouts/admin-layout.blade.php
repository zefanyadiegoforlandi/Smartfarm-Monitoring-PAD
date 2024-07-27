<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Smartfarm Admin Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @livewireStyles

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
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
                overflow: hidden; 
                display: none; 
            }

            .sidebar.active {
                display: block; 
            }

            main {
                margin-left: 0; 
            }
        }

        @media (min-width: 1024px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh; 
                overflow: hidden; 
                width: 18rem; 
                border-right: 3px solid #ccc; 
            }

            main {
                margin-left: 18rem; 
            }
        }

        .menu-item:hover .menu-text,
        .menu-item.active .menu-text {
            color: #416D14;
        }

        .menu-item:hover svg path,
        .menu-item.active svg path {
            fill: #416D14;
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

        <div class="overlay"></div>
            <aside class="bg-[#ECF0E8] text-white w-72 p-4 sidebar xl:border xl:border-r-3 xl:bg-white ">
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
                                <a href="{{ route('admin-dashboard') }}" class="flex dashboard" onclick="changeColor(this, 'dashboard')">
                                    <svg width="25" height="25" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill="#818280" d="M11.5 0.766717L11.9983 0.18405C11.8595 0.0652713 11.6827 0 11.5 0C11.3173 0 11.1405 0.0652713 11.0017 0.18405L11.5 0.766717ZM0.766667 9.96672L0.268333 9.38405L0 9.61405V9.96672H0.766667ZM8.43333 22.2334V23C8.63667 23 8.83167 22.9193 8.97545 22.7755C9.11923 22.6317 9.2 22.4367 9.2 22.2334H8.43333ZM14.5667 22.2334H13.8C13.8 22.4367 13.8808 22.6317 14.0246 22.7755C14.1683 22.9193 14.3633 23 14.5667 23V22.2334ZM22.2333 9.96672H23V9.61405L22.7317 9.38405L22.2333 9.96672ZM2.3 23H8.43333V21.4667H2.3V23ZM22.7317 9.38405L11.9983 0.18405L11.0017 1.34938L21.735 10.5494L22.7317 9.38405ZM11.0017 0.18405L0.268333 9.38405L1.265 10.5494L11.9983 1.34938L11.0017 0.18405ZM9.2 22.2334V17.6334H7.66667V22.2334H9.2ZM13.8 17.6334V22.2334H15.3333V17.6334H13.8ZM14.5667 23H20.7V21.4667H14.5667V23ZM23 20.7V9.96672H21.4667V20.7H23ZM0 9.96672V20.7H1.53333V9.96672H0ZM11.5 15.3334C12.11 15.3334 12.695 15.5757 13.1263 16.007C13.5577 16.4384 13.8 17.0234 13.8 17.6334H15.3333C15.3333 16.6167 14.9295 15.6417 14.2106 14.9228C13.4917 14.2039 12.5167 13.8 11.5 13.8V15.3334ZM11.5 13.8C10.4833 13.8 9.50831 14.2039 8.78942 14.9228C8.07053 15.6417 7.66667 16.6167 7.66667 17.6334H9.2C9.2 17.0234 9.44232 16.4384 9.87365 16.007C10.305 15.5757 10.89 15.3334 11.5 15.3334V13.8ZM20.7 23C21.31 23 21.895 22.7577 22.3263 22.3264C22.7577 21.8951 23 21.31 23 20.7H21.4667C21.4667 20.9034 21.3859 21.0984 21.2421 21.2422C21.0983 21.3859 20.9033 21.4667 20.7 21.4667V23ZM2.3 21.4667C2.09667 21.4667 1.90166 21.3859 1.75788 21.2422C1.61411 21.0984 1.53333 20.9034 1.53333 20.7H0C0 21.31 0.242321 21.8951 0.673654 22.3264C1.10499 22.7577 1.69 23 2.3 23V21.4667Z"/>
                                    </svg>                                    
                                    <span class="menu-text ms-6 text-[#818280] font-semibold">Dashboard</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('daftar-farmer') }}" class="flex daftar-farmer" onclick="changeColor(this, 'daftar-farmer')">
                                    <svg width="25" height="25" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill="#818280" d="M7.625 1.875C8.33578 1.875 9.03059 2.08577 9.62158 2.48066C10.2126 2.87554 10.6732 3.43681 10.9452 4.09348C11.2172 4.75015 11.2884 5.47274 11.1497 6.16986C11.011 6.86697 10.6688 7.50732 10.1662 8.00991C9.66357 8.51251 9.02322 8.85478 8.32611 8.99345C7.62899 9.13211 6.9064 9.06094 6.24973 8.78894C5.59306 8.51694 5.03179 8.05632 4.63691 7.46533C4.24202 6.87434 4.03125 6.17953 4.03125 5.46875C4.03125 4.51563 4.40988 3.60154 5.08384 2.92759C5.75779 2.25363 6.67188 1.875 7.625 1.875ZM7.625 0.4375C6.62991 0.4375 5.65717 0.732578 4.82979 1.28542C4.0024 1.83826 3.35753 2.62403 2.97673 3.54337C2.59593 4.46271 2.49629 5.47433 2.69042 6.4503C2.88456 7.42626 3.36374 8.32275 4.06737 9.02638C4.771 9.73001 5.66749 10.2092 6.64345 10.4033C7.61942 10.5975 8.63104 10.4978 9.55038 10.117C10.4697 9.73622 11.2555 9.09135 11.8083 8.26396C12.3612 7.43658 12.6562 6.46384 12.6562 5.46875C12.6562 4.13438 12.1262 2.85466 11.1826 1.91112C10.2391 0.967577 8.95937 0.4375 7.625 0.4375ZM14.8125 20.5625H13.375V16.9688C13.375 16.0156 12.9964 15.1015 12.3224 14.4276C11.6485 13.7536 10.7344 13.375 9.78125 13.375H5.46875C4.51563 13.375 3.60154 13.7536 2.92759 14.4276C2.25363 15.1015 1.875 16.0156 1.875 16.9688V20.5625H0.4375V16.9688C0.4375 15.6344 0.967577 14.3547 1.91112 13.4111C2.85466 12.4676 4.13438 11.9375 5.46875 11.9375H9.78125C11.1156 11.9375 12.3953 12.4676 13.3389 13.4111C14.2824 14.3547 14.8125 15.6344 14.8125 16.9688V20.5625ZM14.8125 1.875H22V3.3125H14.8125V1.875ZM14.8125 5.46875H22V6.90625H14.8125V5.46875ZM14.8125 9.0625H19.8438V10.5H14.8125V9.0625Z"/>
                                    </svg>                                    
                                    <span class="menu-text ms-6 text-[#818280] font-semibold">Daftar Farmer</span>
                                </a>
                            </li>

                            <li class="menu-item">
                                <a href="{{ route('daftar-lahan') }}" class="flex daftar-lahan" onclick="changeColor(this, 'daftar-lahan')">
                                    <svg width="25" height="25" viewBox="0 0 40 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="#818280" d="M14.5 8.5C16.914 8.5 18.885 10.401 18.995 12.788L19 13V17.698L24.405 13.696C24.733 13.453 25.172 13.437 25.514 13.642L25.625 13.719L31 18.02V14.5H33V19.62L35.625 21.719L34.375 23.281L33 22.18L32.999 26.5H35C35.513 26.5 35.936 26.886 35.993 27.383L36 27.5V35.5C36 36.013 35.614 36.436 35.117 36.493L35 36.5H8V34.5H15.545L17.295 32.5H8V30.5H19V30.552L20.795 28.5H8V26.5H10V13C10 10.515 12.015 8.5 14.5 8.5ZM26.795 28.5H23.453L18.203 34.5H21.545L26.795 28.5ZM34 30.16L30.203 34.5H34V30.16ZM32.795 28.5H29.453L24.203 34.5H27.545L32.795 28.5ZM31 20.58L24.976 15.761L19 20.187V26.5H21.999L22 22.5C22 21.987 22.386 21.564 22.883 21.507L23 21.5H27C27.513 21.5 27.936 21.886 27.993 22.383L28 22.5L27.999 26.5H30.999L31 20.58ZM14.5 10.5C13.175 10.5 12.09 11.532 12.005 12.836L12 13V26.5H17V13C17 11.619 15.881 10.5 14.5 10.5ZM26 23.5H24V26.5H26V23.5Z"/>
                                    </svg>
                                                                      
                                    <span class="menu-text ms-6 text-[#818280] font-semibold">Daftar Lahan</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('daftar-sensor') }}" class="flex daftar-sensor" onclick="changeColor(this, 'daftar-sensor')">
                                    <svg width="25" height="25" viewBox="0 0 24 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="#818280" d="M4.72365 4.72364C4.9044 4.54907 5.14647 4.45248 5.39774 4.45466C5.64902 4.45685 5.88938 4.55763 6.06706 4.73532C6.24474 4.913 6.34553 5.15336 6.34771 5.40463C6.3499 5.6559 6.2533 5.89798 6.07873 6.07872C5.36564 6.78979 4.80015 7.63481 4.41481 8.5652C4.02946 9.49559 3.83186 10.493 3.83336 11.5C3.83175 12.5071 4.02931 13.5045 4.41466 14.4349C4.80001 15.3653 5.36555 16.2103 6.07873 16.9213C6.17379 17.0088 6.25018 17.1146 6.3033 17.2324C6.35643 17.3501 6.38519 17.4774 6.38787 17.6066C6.39054 17.7358 6.36708 17.8641 6.31887 17.984C6.27067 18.1038 6.19874 18.2127 6.10738 18.3041C6.01603 18.3954 5.90715 18.4674 5.78729 18.5156C5.66742 18.5638 5.53905 18.5872 5.40989 18.5846C5.28072 18.5819 5.15344 18.5531 5.03567 18.5C4.91791 18.4469 4.8121 18.3705 4.72461 18.2754C3.83346 17.3866 3.12667 16.3305 2.6448 15.1678C2.16294 14.0051 1.9155 12.7586 1.91669 11.5C1.91525 10.2413 2.16247 8.99471 2.64417 7.83181C3.12588 6.66892 3.83256 5.61264 4.72365 4.72364ZM16.9213 4.72364C17.101 4.54398 17.3447 4.44305 17.5989 4.44305C17.853 4.44305 18.0967 4.54398 18.2764 4.72364C19.1675 5.61264 19.8742 6.66892 20.3559 7.83181C20.8376 8.99471 21.0848 10.2413 21.0834 11.5C21.0848 12.7587 20.8376 14.0053 20.3559 15.1682C19.8742 16.3311 19.1675 17.3874 18.2764 18.2764C18.0957 18.451 17.8536 18.5476 17.6023 18.5454C17.351 18.5432 17.1107 18.4424 16.933 18.2647C16.7553 18.087 16.6545 17.8467 16.6523 17.5954C16.6502 17.3441 16.7467 17.1021 16.9213 16.9213C17.6344 16.2102 18.1999 15.3652 18.5852 14.4348C18.9706 13.5044 19.1682 12.507 19.1667 11.5C19.1683 10.493 18.9707 9.49554 18.5854 8.56514C18.2 7.63474 17.6345 6.78972 16.9213 6.07872C16.7417 5.89901 16.6407 5.6553 16.6407 5.40118C16.6407 5.14707 16.7417 4.90335 16.9213 4.72364ZM8.78986 7.43381C8.87896 7.52281 8.94965 7.6285 8.99788 7.74484C9.0461 7.86118 9.07093 7.98589 9.07093 8.11183C9.07093 8.23777 9.0461 8.36247 8.99788 8.47881C8.94965 8.59515 8.87896 8.70085 8.78986 8.78985C8.4332 9.14522 8.15034 9.56763 7.95758 10.0328C7.76482 10.4979 7.66596 10.9965 7.66669 11.5C7.66669 12.5829 8.11424 13.5585 8.83586 14.2571C8.93026 14.3436 9.00637 14.4482 9.0597 14.5646C9.11303 14.681 9.1425 14.8069 9.14636 14.9348C9.15022 15.0628 9.1284 15.1903 9.08218 15.3097C9.03597 15.4291 8.96629 15.538 8.87728 15.63C8.78826 15.7221 8.68171 15.7953 8.56391 15.8455C8.44611 15.8956 8.31946 15.9217 8.19142 15.9221C8.06339 15.9225 7.93658 15.8972 7.81847 15.8478C7.70036 15.7983 7.59336 15.7257 7.50378 15.6343C6.94838 15.0986 6.5068 14.4563 6.20546 13.7459C5.90411 13.0355 5.74921 12.2717 5.75003 11.5C5.75003 9.91206 6.39499 8.4736 7.43382 7.43381C7.52282 7.34471 7.62851 7.27402 7.74485 7.22579C7.86119 7.17756 7.9859 7.15274 8.11184 7.15274C8.23778 7.15274 8.36248 7.17756 8.47882 7.22579C8.59516 7.27402 8.70086 7.34471 8.78986 7.43381ZM15.6036 7.47214C16.6603 8.54633 17.2517 9.99323 17.25 11.5C17.251 12.2553 17.1027 13.0033 16.8137 13.7011C16.5248 14.3989 16.1008 15.0327 15.5662 15.5662C15.3864 15.746 15.1425 15.8471 14.8882 15.8471C14.6339 15.8471 14.39 15.746 14.2102 15.5662C14.0304 15.3864 13.9293 15.1425 13.9293 14.8882C13.9293 14.6339 14.0304 14.39 14.2102 14.2102C14.5669 13.8548 14.8497 13.4324 15.0425 12.9673C15.2352 12.5022 15.3341 12.0035 15.3334 11.5C15.3334 10.4545 14.9155 9.50668 14.2361 8.81477C14.0624 8.63256 13.9674 8.38935 13.9715 8.13766C13.9757 7.88598 14.0787 7.64604 14.2584 7.46969C14.438 7.29335 14.6798 7.19475 14.9315 7.19521C15.1832 7.19567 15.4246 7.29514 15.6036 7.47214ZM11.5 10.0625C11.8813 10.0625 12.2469 10.214 12.5165 10.4836C12.7861 10.7531 12.9375 11.1188 12.9375 11.5C12.9375 11.8813 12.7861 12.2469 12.5165 12.5165C12.2469 12.7861 11.8813 12.9375 11.5 12.9375C11.1188 12.9375 10.7531 12.7861 10.4836 12.5165C10.214 12.2469 10.0625 11.8813 10.0625 11.5C10.0625 11.1188 10.214 10.7531 10.4836 10.4836C10.7531 10.214 11.1188 10.0625 11.5 10.0625Z"/>
                                    </svg>
                                                                      
                                    <span class="menu-text ms-6 text-[#818280] font-semibold">Daftar Sensor</span>
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
                                ['route' => 'read-auth.edit', 'label' => session('user_name')],
                                ['route' => 'logout', 'label' => 'Log Out']
                            ] as $item)
                                <li class="menu-item" style="list-style-type: none; margin-bottom: 10px;">
                                    @if($item['route'] == 'read-auth.edit')
                                        <a href="{{ route('read-auth.edit') }}" class="flex group" onclick="changeColor(this, '{{ $item['route'] }}')" style="text-decoration: none; color: #818280;">
                                            <svg width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M7.625 1.875C8.33578 1.875 9.03059 2.08577 9.62158 2.48066C10.2126 2.87554 10.6732 3.43681 10.9452 4.09348C11.2172 4.75015 11.2884 5.47274 11.1497 6.16986C11.011 6.86697 10.6688 7.50732 10.1662 8.00991C9.66357 8.51251 9.02322 8.85478 8.32611 8.99345C7.62899 9.13211 6.9064 9.06094 6.24973 8.78894C5.59306 8.51694 5.03179 8.05632 4.63691 7.46533C4.24202 6.87434 4.03125 6.17953 4.03125 5.46875C4.03125 4.51563 4.40988 3.60154 5.08384 2.92759C5.75779 2.25363 6.67188 1.875 7.625 1.875ZM7.625 0.4375C6.62991 0.4375 5.65717 0.732578 4.82979 1.28542C4.0024 1.83826 3.35753 2.62403 2.97673 3.54337C2.59593 4.46271 2.49629 5.47433 2.69042 6.4503C2.88456 7.42626 3.36374 8.32275 4.06737 9.02638C4.771 9.73001 5.66749 10.2092 6.64345 10.4033C7.61942 10.5975 8.63104 10.4978 9.55038 10.117C10.4697 9.73622 11.2555 9.09135 11.8083 8.26396C12.3612 7.43658 12.6562 6.46384 12.6562 5.46875C12.6562 4.13438 12.1262 2.85466 11.1826 1.91112C10.2391 0.967577 8.95937 0.4375 7.625 0.4375ZM14.8125 20.5625H13.375V16.9688C13.375 16.0156 12.9964 15.1015 12.3224 14.4276C11.6485 13.7536 10.7344 13.375 9.78125 13.375H5.46875C4.51563 13.375 3.60154 13.7536 2.92759 14.4276C2.25363 15.1015 1.875 16.0156 1.875 16.9688V20.5625H0.4375V16.9688C0.4375 15.6344 0.967577 14.3547 1.91112 13.4111C2.85466 12.4676 4.13438 11.9375 5.46875 11.9375H9.78125C11.1156 11.9375 12.3953 12.4676 13.3389 13.4111C14.2824 14.3547 14.8125 15.6344 14.8125 16.9688V20.5625ZM14.8125 1.875H22V3.3125H14.8125V1.875ZM14.8125 5.46875H22V6.90625H14.8125V5.46875ZM14.8125 9.0625H19.8438V10.5H14.8125V9.0625Z" fill="#818280"/>
                                            </svg>
                                            <span class="menu-text ms-6" style="margin-left: 10px;">{{ $item['label'] }}</span>
                                        </a>
                                    @elseif($item['route'] == 'logout')
                                        <a href="{{ route('logout') }}" class="flex" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="text-decoration: none; color: #818280;">
                                            <svg width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14.875 6.125L13.6413 7.35875L15.8988 9.625H7V11.375H15.8988L13.6413 13.6325L14.875 14.875L19.25 10.5L14.875 6.125ZM3.5 4.375H10.5V2.625H3.5C2.5375 2.625 1.75 3.4125 1.75 4.375V16.625C1.75 17.5875 2.5375 18.375 3.5 18.375H10.5V16.625H3.5V4.375Z" fill="#818280"/>
                                            </svg>
                                            <span class="menu-text ms-6" style="margin-left: 10px;">{{ $item['label'] }}</span>
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Show Rectangle Button -->
                    <button id="showRectangle" class="w-[247px] h-[84px] rounded-[24px] px-2 mb-2 bg-[#416D14] border-none flex items-center text-white font-sans text-left hover:brightness-125 active:brightness-150">
                        <div class="flex items-center ml-4">
                            <div class="img w-14 h-14 overflow-hidden rounded-full">
                                <img src="{{ asset('images/user_besar_icon.svg') }}" alt="User Image" class="w-full h-full object-cover">
                            </div>
                            <div class="text ml-4">
                                <div class="text-xl text-[18px]">{{ session('user_name') }}</div>
                                <div class="text-xl text-[14px]">{{ session('user_level') }}</div>
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
                  
                    // Prevent closing sensor items on sub-item click
                    function preventClose(event) {
                        event.stopPropagation();
                    }

                    // Change color function
                    function changeColor(element, linkId) {
                        const allLinks = document.querySelectorAll('.menu-text');
                        const allImages = document.querySelectorAll('.menu-item img');
                        const allSvgs = document.querySelectorAll('.menu-item svg path');

                        // Reset semua tautan ke warna default dan gambar ke non-selected
                        allLinks.forEach(link => {
                            link.style.color = '#818280';
                        });
                        allImages.forEach(img => {
                            img.style.filter = 'none';
                        });
                        allSvgs.forEach(svg => {
                            svg.style.fill = '#818280';
                        });

                        // Ubah warna tautan yang dipilih dan tampilkan ikon yang sesuai
                        element.querySelector('.menu-text').style.color = '#416D14';
                        const svgPaths = element.querySelectorAll('svg path');
                        svgPaths.forEach(path => {
                            path.style.fill = '#416D14';
                        });

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
                            const svgPaths = selectedLink.querySelectorAll('svg path');
                            svgPaths.forEach(path => {
                                path.style.fill = '#416D14';
                            });
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

    <script>
        // Toggle Sidebar functionality
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.querySelector('.toggle-button');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            const closeSidebarButton = document.querySelector('.close-sidebar-button');

            toggleButton.addEventListener('click', function () {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            });

            closeSidebarButton.addEventListener('click', function () {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });

            overlay.addEventListener('click', function () {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        });
    </script>
</body>

</html>
