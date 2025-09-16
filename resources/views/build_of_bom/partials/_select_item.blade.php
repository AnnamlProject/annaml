<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
    <div>
        <label for="item" class="block text-sm font-medium text-gray-700">Item</label>
        <select name="item_id"
            class="item-select
            w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Item --</option>
            @foreach ($item as $g)
                <option value="{{ $g->id }}">{{ $g->item_description }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <input type="text" class="item-description w-full border rounded px-4 py-2 bg-gray-50" readonly>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Unit</label>
        <input type="text" class="item-unit w-full border rounded px-4 py-2 bg-gray-50" readonly>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Current Stock</label>
        <input type="text" class="item-stock w-full border rounded px-4 py-2 bg-gray-50" readonly>
    </div>
</div>

<div class="mt-6 hidden bom-section">
    <h3 class="text-lg font-semibold mb-2">Bill Of Materials Formula</h3>
    <table class="w-full border border-gray-300 text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Component</th>
                <th class="border px-2 py-1">Unit</th>
                <th class="border px-2 py-1">Qty Per</th>
                <th class="border px-2 py-1">Unit Cost</th>
                <th class="border px-2 py-1">Amount</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody class="bom-body"></tbody>
    </table>
</div>
<script>
    document.querySelectorAll('.item-select').forEach(select => {
        select.addEventListener('change', function() {
            const wrapper = this.closest('.tab-content'); // ambil section/tab terdekat
            let itemId = this.value;

            // target input di tab yang sama
            let descInput = wrapper.querySelector('.item-description');
            let unitInput = wrapper.querySelector('.item-unit');
            let stockInput = wrapper.querySelector('.item-stock');
            let bomSection = wrapper.querySelector('.bom-section');
            let tbody = wrapper.querySelector('.bom-body');

            if (!itemId) {
                descInput.value = '';
                unitInput.value = '';
                stockInput.value = '';
                bomSection.classList.add('hidden');
                return;
            }

            // Ambil info item
            fetch(`/items/${itemId}/info`)
                .then(res => res.json())
                .then(data => {
                    descInput.value = data.description;
                    unitInput.value = data.unit;
                    stockInput.value = data.current_stock;
                });

            // Ambil BOM detail
            fetch(`/items/${itemId}/bom`)
                .then(res => res.json())
                .then(data => {
                    tbody.innerHTML = '';

                    if (data.details.length > 0) {
                        data.details.forEach(d => {
                            tbody.innerHTML += `
                            <tr>
                                <td class="border px-2 py-1">
                                    <input type="hidden" name="component_id[]" value="${d.component_id}">
                                    <input type="text" value="${d.description}" 
                                        class="w-full border rounded px-2 py-1 bg-gray-50" readonly>
                                </td>
                                <td class="border px-2 py-1">
                                    <input type="text" value="${d.unit}" name="unit[]" 
                                        class="w-full border rounded px-2 py-1 bg-gray-50" readonly>
                                </td>
                                <td class="border px-2 py-1">
                                    <input type="number" value="${d.quantity}" name="qty_per[]" 
                                        class="w-full border rounded px-2 py-1 bg-gray-50" readonly>
                                </td>
                                <td class="border px-2 py-1">
                                    <input type="hidden" name="unit_cost[]" value="${d.unit_cost}">
                                    <input type="text" value="${Number(d.unit_cost).toLocaleString('id-ID', { minimumFractionDigits: 2 })}" 
                                        class="w-full border rounded px-2 py-1 bg-gray-50 text-right" readonly>
                                </td>
                                <td class="border px-2 py-1">
                                    <input type="hidden" name="amount[]" value="${d.amount}">
                                    <input type="text" value="${Number(d.amount).toLocaleString('id-ID', { minimumFractionDigits: 2 })}" 
                                        class="w-full border rounded px-2 py-1 bg-gray-50 text-right" readonly>
                                </td>
                                <td class="border px-2 py-1 text-center">
                                    <button type="button" onclick="hapusBaris(this)" class="text-red-500">🗑️</button>
                                </td>
                            </tr>`;
                        });

                        bomSection.classList.remove('hidden');
                    } else {
                        bomSection.classList.add('hidden');
                    }
                })
                .catch(err => console.error(err));
        });
    });
</script>
