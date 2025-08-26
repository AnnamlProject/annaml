@extends('layouts.app')
@section('content')
    <div class="max-w-full border rounded text-sm mx-auto py-10 px-6">
        <h2 class="text-2xl font-semibold mb-6">Inventory</h2>

        <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="bg-white p-6 rounded-lg shadow space-y-6">
                <div>
                    <label for="" class="font-semibold text-gray-700 block mb-2">Item</label>
                    <label for="" class="mr-4">Number</label>
                    <input type="text" name="item_number" class="form-input w-1/8 border rounded px-2 py-1 text-sm">
                    <label for="" class="mr-4">Description</label>
                    <input type="text" name="item_name" class="form-input w-1/3 border rounded px-2 py-1 text-sm">
                </div>
                <div class="mb-4">
                    <label class="font-semibold text-gray-700 block mb-2">Type</label>
                    <label class="mr-4">
                        <input type="radio" name="type" value="inventory" checked>
                        Inventory
                    </label>
                    <label>
                        <input type="radio" name="type" value="service">
                        Service
                    </label>
                </div>


                {{-- Tab Navigation --}}
                <div id="inventory-tabs" class="type-section">
                    <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600" role="tablist">
                        <li><a href="#quantities" class="tab-link">Quantities</a></li>
                        <li><a href="#units" class="tab-link">Units</a></li>
                        <li><a href="#pricing" class="tab-link">Pricing</a></li>
                        <li><a href="#vendors" class="tab-link">Vendors</a></li>
                        <li><a href="#linked" class="tab-link">Linked COA</a></li>
                        <li><a href="#build" class="tab-link">Build</a></li>
                        <li><a href="#statistics" class="tab-link">Statistics</a></li>
                        <li><a href="#taxes" class="tab-link">Taxes</a></li>
                        <li><a href="#description" class="tab-link">Description</a></li>
                        <li><a href="#picture" class="tab-link">Picture</a></li>
                    </ul>
                </div>
                <div id="service-tabs" class="type-section hidden">
                    <ul class="flex border-b mb-4 space-x-4 text-sm font-medium text-gray-600" role="tablist">
                        <li><a href="#unitsService" class="tab-link">Units</a></li>
                        <li><a href="#pricingService" class="tab-link">Pricing</a></li>
                        <li><a href="#vendorsService" class="tab-link">Vendors</a></li>
                        <li><a href="#linkedService" class="tab-link">Linked COA</a></li>
                        <li><a href="#statisticsService" class="tab-link">Statistics</a></li>
                        <li><a href="#taxesService" class="tab-link">Taxes</a></li>
                        <li><a href="#descriptionService" class="tab-link">Description</a></li>
                        <li><a href="#pictureService" class="tab-link">Picture</a></li>
                    </ul>
                </div>

                {{-- Quantities Tab --}}
                <div id="quantities" class="tab-content">
                    <h3 class="font-semibold text-lg mb-2">Quantities</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block">Location</label>
                            <select name="location_id" id="location_id"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Location --</option>
                                @foreach ($lokasiInventory as $g)
                                    <option value="{{ $g->id }}">
                                        {{ $g->kode_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block">On Hand Quantity</label>
                            <input type="number" name="on_hand_qty"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="block">On Hand Value</label>
                            <input type="number" step="0.01" name="on_hand_value"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="block">Pending Orders Quantity</label>
                            <input type="number" name="pending_orders_qty"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="block">Pending Orders Value</label>
                            <input type="number" step="0.01" name="pending_orders_value"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="block">Purchase Order Quantity</label>
                            <input type="number" name="purchase_order_qty"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="block">Sales Order Quantity</label>
                            <input type="number" name="sales_order_qty"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="block">Minimum Level</label>
                            <input type="number" name="reorder_minimum"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="block">To Order</label>
                            <input type="number" name="reorder_to_order"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                    </div>
                </div>

                {{-- Units Tab --}}
                <div id="units" class="tab-content hidden">
                    <h3 class="font-semibold text-lg mb-2">Units</h3>
                    <div class="mb-4">
                        <label class="block">Stocking Unit of Measure</label>
                        <input type="text" name="stocking_unit"
                            class="form-input w-full border rounded px-2 py-1 text-sm" />
                    </div>
                    <div class="mb-4">
                        <label>
                            <input type="checkbox" name="selling_same_as_stocking" value="1" checked />
                            Selling unit same as stocking unit
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block">Selling Unit (if different)</label>
                            <input type="text" name="selling_unit"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="block">Selling Relationship</label>
                            <input type="number" name="selling_relationship"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                    </div>
                    <div class="mb-4 mt-4">
                        <label>
                            <input type="checkbox" name="buying_same_as_stocking" value="1" checked />
                            Buying unit same as stocking unit
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block">Buying Unit (if different)</label>
                            <input type="text" name="buying_unit"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                        <div>
                            <label class="block">Buying Relationship</label>
                            <input type="number" name="buying_relationship"
                                class="form-input w-full border rounded px-2 py-1 text-sm" />
                        </div>
                    </div>
                </div>

                {{-- Pricing Tab --}}
                <div id="pricing" class="tab-content hidden">
                    <table class="table-auto w-full border-collapse border border-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th>Price List</th>
                                <th>Price Per Selling Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" name="price_list_name"
                                        class="form-input w-full border rounded px-2 py-1 text-sm">
                                </td>
                                <td>
                                    <input type="text" name="price" class="w-full border rounded px-2 py-1 text-sm">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Vendors Tab --}}
                <div id="vendors" class="tab-content hidden"></div>

                {{-- Description Tab --}}
                <div id="description" class="tab-content hidden">
                    <h3 class="font-semibold text-lg mb-2">Description</h3>
                    <textarea name="long_description" class="form-textarea w-full border rounded px-2 py-1 text-sm" rows="5"></textarea>
                </div>

                {{-- Picture Tab --}}
                <div id="picture" class="tab-content hidden">
                    <h3 class="font-semibold text-lg mb-2">Picture</h3>
                    <div class="mb-4">
                        <label class="block">Main Picture</label>
                        <input type="file" name="picture"
                            class="form-input w-full border rounded px-2 py-1 text-sm" />
                    </div>
                    <div class="mb-4">
                        <label class="block">Thumbnail</label>
                        <input type="file" name="thumbnail"
                            class="form-input w-full border rounded px-2 py-1 text-sm" />
                    </div>
                </div>

                {{-- Tab Linked (Chart of Accounts) --}}
                <div id="linked" class="tab-content hidden">
                    <h3 class="font-semibold text-lg mb-2">Account Linkings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Asset Account --}}
                        <div>
                            <label for="asset_account_id" class="block text-sm font-medium text-gray-700">Asset
                                Account</label>
                            <select name="asset_account_id" class="form-select w-full border rounded px-2 py-1 text-sm"
                                required>
                                <option value="">-- Pilih Akun Aset --</option>
                                @foreach ($accounts->where('tipe_akun', 'Aset') as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->kode_akun }} - {{ $account->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Revenue Account --}}
                        <div>
                            <label for="revenue_account_id" class="block text-sm font-medium text-gray-700">Revenue
                                Account</label>
                            <select name="revenue_account_id" class="form-select w-full border rounded px-2 py-1 text-sm"
                                required>
                                <option value="">-- Pilih Akun Pendapatan --</option>
                                @foreach ($accounts->where('tipe_akun', 'Pendapatan') as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->kode_akun }} - {{ $account->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- COGS Account --}}
                        <div>
                            <label for="cogs_account_id" class="block text-sm font-medium text-gray-700">COGS
                                Account</label>
                            <select name="cogs_account_id" class="form-select w-full border rounded px-2 py-1 text-sm"
                                required>
                                <option value="">-- Pilih Akun Beban Pokok Penjualan --</option>
                                @foreach ($accounts->where('tipe_akun', 'Beban') as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->kode_akun }} - {{ $account->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Variance Account --}}
                        <div>
                            <label for="variance_account_id" class="block text-sm font-medium text-gray-700">Variance
                                Account</label>
                            <select name="variance_account_id" class="form-select w-full border rounded px-2 py-1 text-sm"
                                required>
                                <option value="">-- Pilih Akun Selisih Harga --</option>
                                @foreach ($accounts->where('tipe_akun', 'Beban') as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->kode_akun }} - {{ $account->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div id="build" class="tab-content hidden">
                    <table class="table-auto w-full border-collapse border border-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>Description</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- Pilih Item --}}
                                <td>
                                    <select name="item" id="item-select"
                                        class="form-select w-full border rounded px-2 py-1 text-sm">
                                        <option value="">-- Pilih Item --</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" data-unit="{{ $item->unit }}"
                                                data-description="{{ $item->item_description }}">
                                                {{ $item->item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- Unit --}}
                                <td>
                                    <input type="text" id="unit" name="unit"
                                        class="form-input w-full border rounded px-2 py-1 text-sm" readonly>
                                </td>

                                {{-- Description --}}
                                <td>
                                    <input type="text" id="description" name="description"
                                        class="form-input w-full border rounded px-2 py-1 text-sm" readonly>
                                </td>

                                {{-- Quantity --}}
                                <td>
                                    <input type="number" name="quantity"
                                        class="form-input w-full border rounded px-2 py-1 text-sm">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Taxes Tab --}}
                <div id="taxes" class="tab-content hidden">
                    <h3 class="font-semibold text-lg mb-2">Tax Configuration</h3>
                    <table class="table-auto w-full border-collapse border border-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2">Tax</th>
                                <th class="px-4 py-2">Tax Exempt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($taxes as $tax)
                                <tr class="border-b">
                                    <td class="px-4 py-2">
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="taxes[]" value="{{ $tax->id }}"
                                                class="form-checkbox">
                                            <span>{{ $tax->name }} ({{ $tax->rate }}%)</span>
                                        </label>
                                    </td>
                                    <td class="px-4 py-2">
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="tax_exempt[]" value="{{ $tax->id }}"
                                                class="form-checkbox">
                                            <span>Exempt {{ $tax->name }}</span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                {{-- service area --}}

                {{-- Units Tab --}}
                <div id="unitsService" class="tab-content hidden">
                    <h3 class="font-semibold text-lg mb-2">Units</h3>
                    <div class="mb-4">
                        <label class="block">Unit of Measure</label>
                        <input type="text" name="unit_of_measure"
                            class="form-input w-full border rounded px-2 py-1 text-sm" />
                    </div>
                </div>

                {{-- Pricing Tab --}}
                <div id="pricingService" class="tab-content hidden">
                    <table class="table-auto w-full border-collapse border border-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th>Price List</th>
                                <th>Price Per Selling Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" name="price_list_name"
                                        class="form-input w-full border rounded px-2 py-1 text-sm">
                                </td>
                                <td>
                                    <input type="text" name="price" class="w-full border rounded px-2 py-1 text-sm">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Tab Linked (Chart of Accounts) --}}
                <div id="linkedService" class="tab-content hidden">
                    <h3 class="font-semibold text-lg mb-2">Account Linkings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Revenue Account --}}
                        <div>
                            <label for="revenue_account_id" class="block text-sm font-medium text-gray-700">Revenue
                                Account</label>
                            <select name="revenue_account_id" class="form-select w-full border rounded px-2 py-1 text-sm"
                                required>
                                <option value="">-- Pilih Account --</option>
                                @foreach ($accounts->where('tipe_akun', 'Pendapatan') as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->kode_akun }} - {{ $account->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        {{-- expense Account --}}
                        <div>
                            <label for="expense_account_id" class="block text-sm font-medium text-gray-700">Expense
                                Account</label>
                            <select name="expense_account_id" class="form-select w-full border rounded px-2 py-1 text-sm"
                                required>
                                <option value="">-- Pilih Account --</option>
                                @foreach ($accounts->where('tipe_akun', 'Beban') as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->kode_akun }} - {{ $account->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                {{-- Taxes Tab --}}
                <div id="taxesService" class="tab-content hidden">
                    <h3 class="font-semibold text-lg mb-2">Tax Configuration</h3>
                    <table class="table-auto w-full border-collapse border border-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2">Tax</th>
                                <th class="px-4 py-2">Tax Exempt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($taxes as $tax)
                                <tr class="border-b">
                                    <td class="px-4 py-2">
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="taxes[]" value="{{ $tax->id }}"
                                                class="form-checkbox">
                                            <span>{{ $tax->name }} ({{ $tax->rate }}%)</span>
                                        </label>
                                    </td>
                                    <td class="px-4 py-2">
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="tax_exempt[]" value="{{ $tax->id }}"
                                                class="form-checkbox">
                                            <span>Exempt {{ $tax->name }}</span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Description Tab --}}
                <div id="descriptionService" class="tab-content hidden">
                    <h3 class="font-semibold text-lg mb-2">Description</h3>
                    <textarea name="long_description" class="form-textarea w-full border rounded px-2 py-1 text-sm" rows="5"></textarea>
                </div>

                {{-- Picture Tab --}}
                <div id="pictureService" class="tab-content hidden">
                    <h3 class="font-semibold text-lg mb-2">Picture</h3>
                    <div class="mb-4">
                        <label class="block">Main Picture</label>
                        <input type="file" name="picture"
                            class="form-input w-full border rounded px-2 py-1 text-sm" />
                    </div>
                    <div class="mb-4">
                        <label class="block">Thumbnail</label>
                        <input type="file" name="thumbnail"
                            class="form-input w-full border rounded px-2 py-1 text-sm" />
                    </div>
                </div>

                {{-- end service --}}


                {{-- Tombol Submit --}}
                <div class="mt-6 flex space-x-4">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                        Simpan
                    </button>
                    <a href="{{ route('inventory.index') }}"
                        class="px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-400 transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>


    {{-- Script Tab Navigation --}}
    <script>
        const typeRadios = document.querySelectorAll('input[name="type"]');
        const inventoryTabs = document.getElementById('inventory-tabs');
        const serviceTabs = document.getElementById('service-tabs');

        typeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'inventory') {
                    inventoryTabs.classList.remove('hidden');
                    serviceTabs.classList.add('hidden');
                } else {
                    inventoryTabs.classList.add('hidden');
                    serviceTabs.classList.remove('hidden');
                }
            });
        });

        // Tab switching untuk Inventory
        const invLinks = document.querySelectorAll('.tab-link');
        const invTabs = document.querySelectorAll('.tab-content');
        invLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                invTabs.forEach(tab => tab.classList.add('hidden'));
                const id = this.getAttribute('href');
                document.querySelector(id).classList.remove('hidden');
            });
        });

        // Tab switching untuk Service
        const srvLinks = document.querySelectorAll('.tab-link-service');
        const srvTabs = document.querySelectorAll('.tab-content-service');
        srvLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                srvTabs.forEach(tab => tab.classList.add('hidden'));
                const id = this.getAttribute('href');
                document.querySelector(id).classList.remove('hidden');
            });
        });
    </script>
@endsection
