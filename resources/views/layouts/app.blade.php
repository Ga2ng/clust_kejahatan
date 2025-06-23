<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Clustering Kejahatan') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <!-- AJAX -->
        {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.ajax.min.js"></script> --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


        <!-- Alpine JS for dropdown functionality -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <div class="hidden md:flex md:flex-shrink-0">
                <div class="flex flex-col w-64 bg-blue-50 border-r border-blue-100">
                    <div class="flex items-center justify-center h-16 px-4 bg-blue-600">
                        <span class="text-white font-semibold text-lg">Clustering Kejahatan</span>
                    </div>
                    <div class="flex flex-col flex-grow overflow-y-auto">
                        <nav class="flex-1 px-2 py-4 space-y-1">
                            <!-- Dashboard Link -->
                            <a href="{{ route('dashboard.index') }}" class="flex items-center px-4 py-2 text-sm font-medium text-blue-800 rounded-md bg-blue-100 group">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
                            </a>

                            <!-- Regular Link -->
                            <a href="{{ route('clustering.index') }}" class="flex items-center px-4 py-2 text-sm font-medium text-blue-700 rounded-md hover:bg-blue-100 group">
                                <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Clustering
                            </a>
                            
                            <!-- Dropdown Example 1 -->
                            {{-- <div x-data="{ open: false }" class="space-y-1">
                                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-blue-700 rounded-md hover:bg-blue-100 focus:outline-none focus:bg-blue-100 transition duration-150 ease-in-out">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        Users
                                    </span>
                                    <svg :class="{'transform rotate-180': open}" class="w-4 h-4 text-blue-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="pl-8 space-y-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-blue-600 rounded-md hover:bg-blue-100">All Users</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-blue-600 rounded-md hover:bg-blue-100">Create User</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-blue-600 rounded-md hover:bg-blue-100">Roles</a>
                                </div>
                            </div>
                             --}}
                            <!-- Dropdown Example 2 -->
                            {{-- <div x-data="{ open: false }" class="space-y-1">
                                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-blue-700 rounded-md hover:bg-blue-100 focus:outline-none focus:bg-blue-100 transition duration-150 ease-in-out">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        Products
                                    </span>
                                    <svg :class="{'transform rotate-180': open}" class="w-4 h-4 text-blue-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="pl-8 space-y-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-blue-600 rounded-md hover:bg-blue-100">All Products</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-blue-600 rounded-md hover:bg-blue-100">Add New</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-blue-600 rounded-md hover:bg-blue-100">Categories</a>
                                </div>
                            </div> --}}
                            
                            <!-- Regular Link -->
                            {{-- <a href="#" class="flex items-center px-4 py-2 text-sm font-medium text-blue-700 rounded-md hover:bg-blue-100 group">
                                <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Reports
                            </a> --}}
                        </nav>
                        
                        <!-- Bottom Section -->
                        <div class="p-4 border-t border-blue-100">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs font-medium text-blue-500">{{ ucfirst(Auth::user()->role ?? 'User') }}</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('profile.edit') }}" class="text-xs font-medium text-blue-500 hover:text-blue-700 transition-colors duration-200">
                                    <i class="fas fa-user-edit mr-1"></i>Edit Profile
                                </a>
                            </div>
                            <!-- Logout Button -->
                            <div class="mt-4">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-blue-500 hover:text-blue-700">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile sidebar -->
            <div x-data="{ sidebarOpen: false }" class="md:hidden">
                <!-- Mobile sidebar overlay -->
                <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
                
                <!-- Mobile sidebar -->
                <div x-show="sidebarOpen" class="fixed inset-y-0 left-0 z-50 flex-shrink-0 w-64 bg-blue-50 transform transition-transform duration-300 ease-in-out" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
                    <!-- Mobile sidebar content (similar to desktop sidebar) -->
                    <div class="flex flex-col h-full">
                        <div class="flex items-center justify-center h-16 px-4 bg-blue-600">
                            <span class="text-white font-semibold text-lg">{{ config('app.name', 'Clustering Kejahatan') }}</span>
                        </div>
                        <div class="flex flex-col flex-grow overflow-y-auto">
                            <nav class="flex-1 px-2 py-4 space-y-1">
                                <!-- Mobile navigation items (same as desktop) -->
                                <!-- ... -->
                            </nav>
                            <!-- Bottom Section -->
                            <div class="p-4 border-t border-blue-100">
                                <!-- ... -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex flex-col flex-1 overflow-hidden">
                <!-- Top Navigation -->
                <div class="flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200 md:hidden">
                    <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="text-lg font-semibold text-blue-600">{{ config('app.name', 'Laravel') }}</div>
                    <div class="w-6"></div> <!-- Spacer for alignment -->
                </div>

                <!-- Header & Content -->
                <div class="flex flex-col flex-1 overflow-auto">
                    <!-- Page Heading -->
                    @if (isset($header))
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    <!-- Page Content -->
                    <main class="flex-1 overflow-y-auto p-4 bg-gray-50">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });
        </script>
    </body>
</html>