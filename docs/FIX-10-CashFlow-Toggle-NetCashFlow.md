# FIX-10: Cash Flow Account Mode - Toggle & Net Cash Flow

**Date:** 2025-12-08  
**Commit:** b2695a1  
**Files Modified:** 1 file

---

## Summary
Added collapsible row functionality and Net Cash Flow per account for the "Per Account Kas/Bank" display mode.

## Changes

### Report View Enhancement
**File:** `resources/views/arus_kas/report_arus_kas.blade.php`

---

### Feature 1: Global Toggle Button
Added "Expand All / Collapse All" button above the table.

```blade
<div class="mb-2 flex gap-2" x-data="{ allExpanded: false }">
    <button type="button" 
        @click="allExpanded = !allExpanded; 
                document.querySelectorAll('[data-detail-rows]').forEach(el => 
                    el.style.display = allExpanded ? 'table-row' : 'none')"
        class="px-3 py-1 text-[10px] bg-blue-600 text-white rounded">
        <span x-text="allExpanded ? 'Collapse All' : 'Expand All'">Expand All</span>
    </button>
</div>
```

---

### Feature 2: Collapsible Account Rows
- Default state: **Collapsed** (only header visible)
- Click header row to expand/collapse detail rows
- Icon changes: `fa-chevron-right` ↔ `fa-chevron-down`

```blade
{{-- Header Account (Clickable) --}}
<tr class="bg-blue-100 font-semibold cursor-pointer hover:bg-blue-200" 
    onclick="toggleAccountRows({{ $accountIndex }})">
    <td colspan="4" class="px-2 py-1 text-left">
        <i class="fas fa-chevron-right mr-1" data-toggle-icon data-account="{{ $accountIndex }}"></i>
        <i class="fas fa-wallet mr-1"></i>{{ $akunKas }}
    </td>
    <td class="border px-2 py-1 text-right">{{ number_format($subtotalIn, 2) }}</td>
    <td class="border px-2 py-1 text-right">{{ number_format($subtotalOut, 2) }}</td>
    <td class="border px-2 py-1 text-right">{{ number_format($netCashFlow, 2) }}</td>
</tr>

{{-- Detail rows hidden by default --}}
<tr data-detail-rows data-account="{{ $accountIndex }}" style="display: none;">
    ...
</tr>
```

---

### Feature 3: Net Cash Flow Per Account
Added "Net" column showing `Cash In - Cash Out` for each account.

```php
$netCashFlow = $subtotalIn - $subtotalOut;
```

Color coding:
- **Green** for positive net
- **Red** for negative net

---

### JavaScript Toggle Function
```javascript
function toggleAccountRows(accountId) {
    const rows = document.querySelectorAll(`[data-detail-rows][data-account="${accountId}"]`);
    const icon = document.querySelector(`[data-toggle-icon][data-account="${accountId}"]`);
    const isHidden = rows[0]?.style.display === 'none';
    
    rows.forEach(row => row.style.display = isHidden ? 'table-row' : 'none');
    icon?.classList.toggle('fa-chevron-right', !isHidden);
    icon?.classList.toggle('fa-chevron-down', isHidden);
}
```

---

## Performance
- Pure client-side toggle (no server requests)
- O(1) complexity per click
- No additional database queries

---

## Testing
1. Navigate to Cash Flow report with "Per Account Kas/Bank" mode
2. Verify default state is collapsed
3. Click account header to expand
4. Click "Expand All" to show all details
5. Verify Net values are correct per account
