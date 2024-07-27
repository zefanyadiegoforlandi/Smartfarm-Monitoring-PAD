<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Smart Farm') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body>
    <div class="font-sans text-gray-900 antialiased">

        <div>
            <div class="bg-no-repeat bg-cover right-0" style="background-image: url('{{ asset('images/background.svg') }}');">
                <div class="min-h-screen lg:mx-[114px] md:mx-14">
                    <div class="absolute hidden md:block top-1">
                        <img src="{{ asset('images/smartfarm_logo 2.svg') }}">
                    </div>

                    <div class=" flex items-center justify-center lg:justify-start min-h-screen">
                        <main class="w-[272px] h-[261px]  lg:w-[402px] lg:h-[294px]">
                            <section>
                                <h3 class="font-semibold text-4xl md:text-[40px] text-center text-[#416D14]">Login</h3>
                            </section>
                            <section class="mt-8 ">
                                @if ($errors->any())
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        Swal.fire({
                                            icon: 'error',
                                            text: '{{ $errors->first() }}',
                                            confirmButtonColor: '#416D14',
                                        });
                                    });
                                </script>
                                @endif
                                <form id="loginForm" class="flex flex-col items-center" method="POST" action="{{ route('login') }}">
                                    @csrf
                            
                                    <div class="mb-6 p-1 rounded bg-white relative w-full h-11 lg:h-[52px]">
                                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                            class="bg-white rounded w-full h-full border-none focus:border-[#416D14] outline-none focus:border-b-2 focus:transition duration-500 focus:px-3 focus:pb-1">
                                        <img src="{{ asset('images/email.svg') }}" class="absolute top-1/2 right-2 -translate-y-1/2">
                                    </div>
                            
                                    <div class="mb-6 p-1 rounded bg-white relative w-full h-11 lg:h-[52px]">
                                        <input type="password" id="password" name="password" required
                                            class="bg-white rounded w-full h-full border-none focus:border-[#416D14] outline-none focus:border-b-2 focus:transition duration-500 focus:px-3 focus:pb-1">
                                        <img id="togglePassword" src="{{ asset('images/close_eye.svg') }}" class="absolute top-1/2 right-2 -translate-y-1/2 cursor-pointer">
                                    </div>
                            
                                    <button class="bg-[#416D14] h-10 w-28 lg:h-[54px] lg:w-[166px] hover:bg-[#3a5a1a] text-white py-2 rounded-[50px] shadow-lg hover:shadow-xl transition duration-200"
                                        type="submit">Submit</button>
                                </form>                            
                            </section>
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @livewireScripts

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            var passwordInput = document.getElementById('password');
            var icon = document.getElementById('togglePassword');

            // Toggle password visibility
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.src = "{{ asset('images/open_eye.svg') }}";
            } else {
                passwordInput.type = 'password';
                icon.src = "{{ asset('images/close_eye.svg') }}";
            }
        });
    </script>
</body>

</html>