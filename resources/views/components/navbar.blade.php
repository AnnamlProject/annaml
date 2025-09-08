    <nav x-data="{ menuOpen: false }" x-cloak class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-full mx-auto px-3 sm:px-6 lg:px-8">
            <!-- Header: Logo + Toggle -->
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('storage/' . \App\Setting::get('logo', 'logo.jpg')) }}" alt="Logo"
                                class="h-10 w-10">
                            <span class="font-bold text-lg text-gray-700"></span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex md:items-center md:space-x-8">
                    <!-- Setup Dropdown -->
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-2 py-2 text-gray-700 font-medium text-sm hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Setup
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>


                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            @click.outside="open = false"
                            class="absolute left-2 mt-2 w-60 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">

                            <!-- Company Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Company</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('setting.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Settings</a>
                                    <a href="{{ $company ? route('company_profile.show', $company->id) : route('company_profile.create') }}"
                                        @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Company
                                        Profile</a>
                                    <a href="{{ $taxpayers ? route('taxpayers_company.show', $taxpayers->id) : route('taxpayers_company.create') }}"
                                        @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Taxpayer
                                        Profile</a>
                                </div>
                            </div>

                            <!-- General Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">General</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('start_new_year.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Year
                                        Book</a>
                                    <a href="{{ route('numbering_account.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Numbering</a>
                                    <a href="{{ route('klasifikasiAkun.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Account
                                        Classification</a>
                                    <a href="{{ route('chartOfAccount.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Chart
                                        of Accounts</a>
                                    @if ($currentDept === 'Accounting')
                                        <a href="{{ route('departemen.index') }}" @click="open = false"
                                            class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                            Departments
                                        </a>
                                    @elseif ($currentDept === '-' || is_null($currentDept))
                                        <a href="{{ route('setting_departement.edit') }}" @click="open = false"
                                            class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                            Departments
                                        </a>
                                    @else
                                        <a href="#"
                                            onclick="alert('Menu ini hanya aktif saat mode departemen Accounting')"
                                            @click="open = false"
                                            class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                            Departments
                                        </a>
                                    @endif
                                    <a href="{{ route('linkedAccount.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Linked
                                        Account</a>
                                    <a href="{{ route('sales_taxes.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Sales
                                        - Taxes
                                    </a>
                                </div>
                            </div>

                            <!-- Reports Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Reports</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('report.account') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Account
                                        List</a>
                                    <a href="{{ route('report.klasifikasi') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Account
                                        Classification List</a>
                                    <a href="{{ route('report.departemen-akun') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Department
                                        Accounts List</a>
                                </div>

                                <a href="{{ route('users.index') }}" @click="open = false"
                                    class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                    Users & Roles
                                </a>
                                <a href="{{ route('permissions.index') }}" @click="open = false"
                                    class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                    Permissions
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Dropdown -->
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-2 py-2 text-gray-700 font-medium text-sm hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Sales
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute left-2 mt-2 w-64 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">

                            <!-- Company Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Setup</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('linkedAccountSales.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Linked
                                        Accounts</a>
                                    <a href="{{ route('sales_option.create') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Options
                                    </a>
                                    <a href="{{ route('sales_discount.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Sales Discount
                                    </a>
                                </div>
                            </div>

                            <!-- General Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Data</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    {{-- <a href="{{ route('item_category.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Item
                                        Category</a>
                                    <a href="{{ route('items.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Items
                                    </a> --}}
                                    <a href="{{ route('PaymentMethod.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Payment
                                        Method</a>
                                    <a href="{{ route('customers.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Customers</a>
                                </div>
                            </div>
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Sales</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('sales_order.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Sales
                                        Orders</a>
                                    <a href="{{ route('sales_invoice.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Sales
                                        Invoices
                                    </a>
                                    <a href="{{ route('employee.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Sales
                                        Person
                                    </a>
                                    <a href="{{ route('sales_deposits.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Deposits</a>
                                    <a href="{{ route('receipts.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Receipts</a>
                                </div>
                            </div>

                            <!-- Reports Submenu -->
                            {{-- <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-4 py-3 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Reports</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('report.account') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Account
                                        List</a>
                                    <a href="{{ route('report.klasifikasi') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Account
                                        Classification List</a>
                                    <a href="{{ route('report.departemen-akun') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Department
                                        Accounts List</a>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    {{-- Purchases dropdown --}}
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-2 py-2 text-gray-700 font-medium text-sm text-sm hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Purchases
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute left-0 mt-2 w-64 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">

                            <!-- Company Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Setup</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('purchases_options.create') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Options
                                    </a>
                                    <a href="{{ route('linkedAccountPurchases.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Linked
                                        Account
                                    </a>
                                </div>
                            </div>
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Purchases</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('purchase_order.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Purchases
                                        Orders
                                    </a>
                                    <a href="{{ route('purchase_invoice.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Purchase
                                        Invoice
                                    </a>
                                    <a href="{{ route('LevelKaryawan.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Prepayments
                                    </a>
                                    <a href="{{ route('payment.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Payments
                                    </a>
                                </div>
                            </div>
                            <a href="{{ route('vendors.index') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Vendors

                            </a>


                        </div>
                    </div>

                    {{-- inventory dropdown --}}
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-2 py-2 text-gray-700 font-medium text-sm text-sm hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Inventory
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>


                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute left-0 mt-2 w-64 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">

                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Settings</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    {{-- <a href="{{ route('item_category.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Item
                                        Category</a>
                                    <a href="{{ route('items.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Items
                                    </a> --}}
                                    <a href="{{ $options_inventory ? route('options_inventory.edit', $options_inventory->id) : route('options_inventory.create') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Options</a>
                                    <a href="{{ route('price_list_inventory.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Price List</a>
                                    <a href="{{ route('lokasi_inventory.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Locations</a>
                                    <a href="{{ route('item_category.index') }}"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Categories</a>
                                </div>
                            </div>

                            <a href="{{ route('inventory.index') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Inventory
                                & Service

                            </a>
                        </div>
                    </div>

                    {{-- budgeting dropdown --}}
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-2 py-2 text-gray-700 font-medium text-sm text-sm hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Budgeting
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute left-0 mt-2 w-64 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">


                            <a href="{{ route('intangible_asset.index') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Create
                                Budget

                            </a>
                            <a href="{{ route('intangible_asset.index') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Budget
                                Submission

                            </a>
                            <a href="{{ route('intangible_asset.index') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Budget
                                Disbursement

                            </a>
                            <a href="{{ route('intangible_asset.index') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Budget
                                Realization

                            </a>
                        </div>
                    </div>

                    {{-- payroll dropdown --}}
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-2 py-2 text-gray-700 font-medium text-sm text-sm hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Payroll
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute left-2 mt-2 w-64 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">

                            <!-- Company Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Setup</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('LevelKaryawan.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Employee
                                        Level</a>
                                    <a href="{{ route('unit_kerja.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Units/Departemens
                                    </a>
                                    <a href="{{ route('target_unit.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Target
                                        Unit
                                    </a>
                                    <a href="{{ route('wahana.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Wahana
                                    </a>
                                    <a href="{{ route('target_wahana.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Target
                                        Wahana
                                    </a>
                                    <a href="{{ route('jenis_hari.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Jenis
                                        Hari
                                    </a>
                                    <a href="{{ route('shift_karyawan.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Scheduling
                                        Employee
                                    </a>
                                    <a href="{{ route('transaksi_wahana.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Transaksi
                                    </a>
                                    <a href="{{ route('bonus_karyawan.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Bonus
                                        Employee
                                    </a>
                                    <a href="{{ route('jam_kerja.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Jam
                                        Kerja
                                    </a>
                                    <a href="{{ route('jabatan.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Position
                                    </a>
                                    <a href="{{ route('komponen_penghasilan.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Income
                                        Components
                                    </a>
                                    <a href="{{ route('employee.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Employee
                                    </a>
                                    <a href="{{ route('komposisi_gaji.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Salary Components
                                    </a>
                                    <a href="{{ route('ptkp.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">PTKP

                                    </a>
                                    <a href="{{ route('tax_rates.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">TER
                                        (Tax Rates)
                                    </a>

                                </div>
                            </div>

                            <!-- Reports Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Process</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('pembayaran_gaji.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Create
                                        Salary Calculations Staff
                                    </a>
                                    <a href="{{ route('pembayaran_gaji_nonstaff.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Create
                                        Salary Calculations Non Staff
                                    </a>
                                    <a href="{{ route('slip.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Salary Slip Staff</a>
                                    <a href="{{ route('slip.nonStaff.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Salary Slip Non Staff</a>
                                    <a href="{{ route('absensi.form') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Absensi
                                        Pegawai
                                    </a>
                                </div>
                            </div>
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Report</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('report.absensi.filter') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Rekap Absensi Pegawai
                                    </a>
                                    <a href="{{ route('report.target_wahana.filter') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Rekap Target Wahana
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- asset --}}
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-2 py-2 text-gray-700 font-medium text-sm text-sm hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Asset
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute left-2 mt-2 w-64 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">

                            <!-- Company Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Setup</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('kategori_asset.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Category
                                        Asset
                                    </a>
                                    <a href="{{ route('lokasi.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Location
                                        Asset
                                    </a>
                                    <a href="{{ route('masa_manfaat.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Masa
                                        Manfaat

                                    </a>
                                    <a href="{{ route('metode_penyusutan.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Metode
                                        Penyusutan
                                    </a>
                                </div>
                            </div>

                            <!-- Tangible Asset -->
                            <a href="{{ route('tangible_asset.index') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Tangible
                                Asset
                            </a>
                            <a href="{{ route('intangible_asset.index') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Intangible
                                Asset
                            </a>
                            <a href=""
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Monthly
                                Process
                            </a>

                            {{-- <!-- Reports Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Reports</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href=""
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Assets
                                        List</a>
                                    <a href=""
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                        Depreciation
                                        List</a>

                                </div>
                            </div> --}}
                        </div>
                    </div>

                    {{-- project dropdown --}}
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-2 py-2 text-gray-700 font-medium text-sm text-sm hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Specpose
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute left-0 mt-2 w-64 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">

                            <!-- Project  Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Setup</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('project.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Data
                                        Specpose</a>
                                    <a href="{{ route('project.create') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Create
                                        Specpose</a>
                                    <a href="{{ route('project.edit_project') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Edit
                                        Specpose</a>
                                    <a href="{{ route('project.view_project') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">View
                                        Specpose</a>
                                </div>
                                <a href="#"
                                    class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                    Reports</a>
                            </div>

                            <!-- Divider -->
                            <div class="my-1 border-t border-gray-100"></div>
                        </div>
                    </div>

                    <!-- Journal Entry Dropdown -->
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-2 py-2 text-gray-700 font-medium text-sm text-sm hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Journal Entry
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute left-0 mt-2 w-64 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">

                            <!-- General Journal Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">General Journal</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-1 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('journal_entry.index') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Data
                                        Journal Entry</a>
                                    <a href="{{ route('journal_entry.create') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Create
                                        Journal Entry</a>
                                    <a href="{{ route('journal_entry.filter_journal_entry') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Edit
                                        Journal Entry</a>
                                    <a href="{{ route('journal_entry.view_journal_entry') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">View
                                        Journal Entry</a>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="my-1 border-t border-gray-100"></div>
                        </div>
                    </div>

                    <!-- Report Dropdown -->
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-3 py-2 text-gray-700 font-medium hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Report
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute right-0 mt-2 w-64 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">
                            <a href="{{ route('buku_besar.filter_buku_besar') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Buku
                                Besar</a>
                            <a href="{{ route('trial_balance.filter_trial_balance') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Trial
                                Balance</a>
                            <a href="{{ route('income_statement.filter_income_statement') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Income
                                Statement</a>
                            <a href="#"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Income
                                Statement Department</a>
                        </div>
                    </div>
                    {{-- document menu  --}}
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-3 py-2 text-gray-700 font-medium hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Documents
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute left-2 mt-2 w-60 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">

                            <!-- Company Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Sales</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-0 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('sales_orders.documents.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Sales
                                        Order</a>
                                    <a href="{{ route('sales_invoice.documents.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Sales
                                        Invoice</a>
                                </div>
                            </div>

                            <!-- General Submenu -->
                            <div class="relative" x-data="{ subOpen: false }">
                                <button @mouseenter="subOpen = true" @mouseleave="subOpen = false"
                                    class="w-full text-left px-2 py-1 hover:bg-blue-50 flex justify-between items-center group transition-colors duration-150">
                                    <span class="text-gray-700 group-hover:text-blue-600">Purchases</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-1"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-x-0"
                                    x-transition:leave-end="opacity-0 translate-x-1"
                                    class="absolute left-full top-0 ml-0 w-56 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50"
                                    @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('purchase_order.documents.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Purchase
                                        Order</a>
                                    <a href="{{ route('purchase_invoice.documents.index') }}" @click="open = false"
                                        class="block px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Purchase
                                        Invoice
                                    </a>
                                </div>
                            </div>

                            <a href="{{ route('taxes.index') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                Taxes
                            </a>
                            {{-- end document menu  --}}
                        </div>
                    </div>

                    <!-- Maintenance Dropdown -->
                    <div class="relative ml-1" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center px-3 py-2 text-gray-700 font-medium hover:text-blue-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md"
                            :class="{ 'text-blue-600 bg-blue-50': open }">
                            Maintenance
                            <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute right-0 mt-2 w-64 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">

                            <a href="{{ route('accounting.start_new_year') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Start
                                New Year
                            </a>

                            <a href="{{ route('activity_log.index') }}"
                                class="block px-2 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Log
                                Activity
                            </a>
                        </div>
                    </div>



                </div>

                <!-- Profile Section -->
                <div class="hidden md:flex md:items-center">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="text-gray-700 font-medium">Hi, Admin</span>
                            <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-gray-600 text-sm font-medium">A</span>
                            </div>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                            class="absolute right-0 mt-2 w-48 bg-white shadow-xl rounded-lg border border-gray-200 py-2 z-50">
                            <a href="#"
                                class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">Profile</a>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 hover:bg-blue-100 rounded">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="menuOpen = !menuOpen"
                        class="p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="menuOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                class="md:hidden border-t border-gray-200 bg-white">
                <div class="px-2 pt-2 pb-3 space-y-1">

                    <!-- Setup with Submenu -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex justify-between items-center px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md focus:outline-none">
                            Setup
                            <svg class="w-4 h-4 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Level 1: Setup Submenu -->
                        <div x-show="open" x-collapse class="pl-4 space-y-1">

                            <!-- Company with Sub-submenu -->
                            <div x-data="{ subOpen: false }">
                                <button @click="subOpen = !subOpen"
                                    class="w-full flex justify-between items-center px-3 py-2 text-sm text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-md">
                                    Company
                                    <svg class="w-4 h-4 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': subOpen }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Level 2: Company Sub-submenu -->
                                <div x-show="subOpen" x-collapse class="pl-4 space-y-1">
                                    <a href="{{ route('setting.index') }}"
                                        class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-100 rounded-md">Settings</a>
                                    <a href="{{ $company ? route('company_profile.show', $company->id) : route('company_profile.create') }}"
                                        class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-100 rounded-md">Company
                                        Profile</a>
                                    <a href="{{ $taxpayers ? route('taxpayers_company.show', $taxpayers->id) : route('taxpayers_company.create') }}"
                                        class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-100 rounded-md">Taxpayer
                                        Profile</a>
                                </div>
                            </div>

                            <!-- Other Setup Items -->
                            <a href="#"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 rounded-md">General</a>
                            <a href="#"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 rounded-md">Reports</a>
                        </div>
                    </div>

                    <!-- Journal Entry with Submenu -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex justify-between items-center px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md focus:outline-none">
                            Journal Entry
                            <svg class="w-4 h-4 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="pl-4 space-y-1">
                            <a href="#"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 rounded-md">General
                                Journal</a>
                            <a href="#"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 rounded-md">Create
                                Entry</a>
                            <a href="#"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50 rounded-md">Edit
                                Entry</a>
                        </div>
                    </div>

                    <!-- Report -->
                    <a href="#"
                        class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Report</a>

                    <!-- Profile & Logout -->
                    <div class="border-t border-gray-200 pt-2">
                        <a href="#"
                            class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Profile</a>
                        <a href="#" class="block px-3 py-2 text-red-600 hover:bg-red-50 rounded-md">Logout</a>
                    </div>
                </div>
            </div>

        </div>
    </nav>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
