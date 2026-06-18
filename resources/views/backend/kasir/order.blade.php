@extends('backend.kasir.layout')

@section('title', 'Pesanan')
@section('page_title', 'Pesanan')

@section('styles')
<style>
    .order-layout {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 24px;
        height: calc(100vh - 140px); /* Adjust based on header */
    }

    .left-panel {
        background: #faecd8;
        border-radius: 24px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .cat-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }
    
    .cat-btn {
        padding: 8px 20px;
        border-radius: 20px;
        background: #efe0cd;
        border: 1px solid #354024;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .cat-btn.active {
        background: #354024;
        color: #fff;
    }

    .menu-buttons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .menu-btn-item {
        background: #354024;
        color: #fff;
        border-radius: 16px;
        padding: 16px;
        text-align: center;
        cursor: pointer;
        border: none;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 4px;
        min-height: 80px;
    }
    .menu-btn-item:hover {
        opacity: 0.9;
    }
    
    .menu-item-name { font-size: 14px; font-weight: 700; }
    .menu-item-price { font-size: 11px; font-weight: 400; opacity: 0.9; }

    /* Right Panel */
    .right-panel {
        background: #faecd8;
        border-radius: 24px;
        padding: 24px;
        display: flex;
        flex-direction: column;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .order-title { font-size: 20px; font-weight: 700; }
    .btn-reset {
        background: #354024;
        color: #fff;
        border: none;
        padding: 6px 16px;
        border-radius: 12px;
        font-size: 12px;
        cursor: pointer;
    }

    .order-items {
        flex: 1;
        overflow-y: auto;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .order-card {
        background: #efe0cd;
        border: 1px solid #c6c7bc;
        border-radius: 12px;
        padding: 12px;
        position: relative;
    }
    
    .oc-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }
    .oc-name { font-weight: 700; font-size: 14px; }
    .oc-qty-control {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .qty-btn {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 1px solid #354024;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        color: #354024;
    }

    .oc-price { font-size: 11px; color: #555; }
    .oc-subtotal { font-size: 12px; font-weight: 600; margin-top: 4px; }
    
    .btn-trash {
        position: absolute;
        bottom: 12px;
        right: 12px;
        color: #354024;
        cursor: pointer;
        font-size: 14px;
    }

    .summary-section {
        border-top: 1px solid #c6c7bc;
        padding-top: 16px;
    }
    
    .discount-tax-row {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }
    .dt-group { flex: 1; }
    .dt-label { font-size: 12px; font-weight: 700; margin-bottom: 4px; }
    .dt-input {
        width: 100%;
        background: #efe0cd;
        border: 1px solid #354024;
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 12px;
        outline: none;
    }

    .sum-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        margin-bottom: 6px;
    }
    .sum-total {
        font-size: 16px;
        font-weight: 700;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #ccc;
    }

    .btn-save {
        width: 100%;
        background: #354024;
        color: #fff;
        border: none;
        padding: 14px;
        border-radius: 12px;
        font-weight: 600;
        margin-top: 16px;
        cursor: pointer;
    }

    .search-menu-bar {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border-radius: 20px;
        border: 1px solid #354024;
        background: transparent;
        outline: none;
        font-size: 13px;
    }
    .modal-overlay{
        position:fixed;
        inset:0;
        background:rgba(0,0,0,0.35);
        display:none;
        align-items:center;
        justify-content:center;
        z-index:50;
    }
    .modal-card{
        width: 560px;
        max-width: 92vw;
        background:#fff;
        border-radius:16px;
        padding:24px;
        box-shadow:0 18px 42px rgba(0,0,0,0.22);
    }
    .modal-title{
        font-size:14px;
        font-weight:700;
        color:#111;
        margin-bottom:6px;
    }
    .modal-total{
        font-size:36px;
        font-weight:800;
        color:#000;
        margin-bottom:14px;
    }
    .separator{
        height:1px;
        background:#ddd;
        margin:10px 0 18px;
    }
    .method-row{
        display:flex;
        gap:12px;
        margin-bottom:18px;
    }
    .pill{
        padding:10px 22px;
        border-radius:18px;
        border:1px solid #354024;
        background:#efe0cd;
        font-size:14px;
        cursor:pointer;
    }
    .pill.active{
        background:#354024;
        color:#fff;
        border-color:#354024;
    }
    .amount-grid{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        margin-top:8px;
    }
    .chip{
        padding:8px 16px;
        border-radius:18px;
        border:1px solid #354024;
        color:#354024;
        background:#fff;
        font-size:13px;
        cursor:pointer;
    }
    .modal-footer{
        display:flex;
        justify-content:space-between;
        gap:12px;
        margin-top:18px;
    }
    .btn-cancel, .btn-process{
        flex:1;
        padding:12px 16px;
        border-radius:12px;
        border:none;
        font-weight:700;
        cursor:pointer;
    }
    .btn-cancel{
        background:#354024;
        color:#fff;
    }
    .btn-process{
        background:#efe0cd;
        color:#354024;
        border:1px solid #354024;
    }


    /* Receipt (Struk) Modal */
    .struk-card {
        width: 380px;
        background: #fff;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        font-family: 'Courier New', Courier, monospace; /* Receipt font style */
        color: #000;
        position: relative;
    }
    .struk-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }
    .struk-brand { font-size: 24px; font-weight: 800; margin-bottom: 4px; text-transform: uppercase;}
    .struk-addr { font-size: 12px; margin-bottom: 2px; }
    
    .struk-meta {
        font-size: 12px;
        margin-bottom: 15px;
        border-bottom: 1px solid #000;
        padding-bottom: 10px;
    }
    .meta-row { display: flex; justify-content: space-between; margin-bottom: 2px; }
    
    .struk-items {
        margin-bottom: 15px;
        border-bottom: 1px solid #000;
        padding-bottom: 10px;
        min-height: 100px;
    }
    .struk-item-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        margin-bottom: 6px;
    }
    .item-name { flex: 1; }
    .item-price { text-align: right; }

    .struk-totals {
        font-size: 12px;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }
    .grand-total {
        font-size: 16px;
        font-weight: 700;
        margin-top: 8px;
    }

    .struk-footer {
        text-align: center;
        font-size: 12px;
        margin-top: 20px;
    }
    
    .btn-close-struk {
        display: block;
        width: 100%;
        margin-top: 10px;
        padding: 10px;
        background: #354024;
        color: #fff;
        text-align: center;
        border: none;
        border-radius: 20px; /* To match UI style */
        cursor: pointer;
        font-family: 'Poppins', sans-serif; /* Reset font for button */
        font-weight: 600;
    }

    .btn-print-struk {
        display: block;
        width: 100%;
        margin-top: 20px;
        padding: 10px;
        background: #333;
        color: #fff;
        text-align: center;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
    }

    /* Print media query to hide button when printing */
    @media print {
        .btn-close-struk, .btn-print-struk { display: none; }
        body * { visibility: hidden; }
        .struk-card, .struk-card * { visibility: visible; }
        .struk-card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; }
    }
</style>
@endsection

@section('content')
<div class="order-layout">
    <!-- Left: Menu Selection -->
    <div class="left-panel">
        @php
            $orderCategories = collect($menus)->pluck('category')->unique()->filter()->values();
        @endphp
        <div class="cat-row">
            <span style="font-weight:700; font-size:16px;">Kategori</span>
            <button class="cat-btn active" onclick="filterOrderCategory('Semua', this)">Semua</button>
            @foreach($orderCategories as $cat)
            <button class="cat-btn" onclick="filterOrderCategory('{{ $cat }}', this)">{{ $cat }}</button>
            @endforeach
        </div>

        <div style="position:relative; margin-bottom:20px;">
            <span style="position:absolute; left:14px; top:50%; transform:translateY(-50%);">🔍</span>
            <input type="text" id="orderSearchInput" class="search-menu-bar" placeholder="Cari menu..." onkeyup="filterOrderSearch()">
        </div>

        <div class="menu-buttons-grid" id="menuGrid">
            @foreach($menus as $m)
            @if(($m->status ?? 'tersedia') === 'tersedia' && ($m->stock ?? 0) > 0)
            @php $priceStr = 'Rp' . number_format($m->price ?? 0, 0, ',', '.'); @endphp
            <button class="menu-btn-item" 
                data-name="{{ strtolower($m->name) }}" 
                data-category="{{ $m->category }}"
                onclick="addToCart('{{ $m->name }}', '{{ $priceStr }}')">
                <span class="menu-item-name">{{ $m->name }}</span>
                <span class="menu-item-price">{{ $priceStr }}</span>
            </button>
            @endif
            @endforeach
        </div>
    </div>

    <!-- Right: Order Summary -->
    <div class="right-panel">
        <div class="order-header">
            <div>
                <div class="order-title">Pesanan</div>
                <div style="font-size:12px; color:#555;">Produk yang dipilih</div>
            </div>
            <button class="btn-reset" onclick="resetOrder()">Reset</button>
        </div>

        <div class="order-items" id="orderItemsContainer">
            <!-- Items rendered via JS -->
        </div>

        <div class="summary-section">
            <div class="discount-tax-row">
                <div class="dt-group">
                    <div class="dt-label">Diskon</div>
                    <input type="text" id="discountInput" class="dt-input" value="0" oninput="calculateTotals()">
                </div>
                <div class="dt-group">
                    <div class="dt-label">Pajak</div>
                    <input type="text" id="taxInput" class="dt-input" value="10%" oninput="calculateTotals()">
                </div>
            </div>

            <div style="border:1px solid #354024; border-radius:12px; padding:12px;">
                <div class="sum-row">
                    <span>Subtotal</span>
                    <span id="subtotalVal">Rp0</span>
                </div>
                <div class="sum-row">
                    <span>Diskon</span>
                    <span id="discountVal">-</span>
                </div>
                <div class="sum-row">
                    <span>Pajak</span>
                    <span id="taxVal">Rp0</span>
                </div>
                <div class="sum-row">
                    <span>Service</span>
                    <span id="serviceVal">Rp5.000</span>
                </div>
                <div class="sum-row sum-total">
                    <span>Total</span>
                    <span id="totalVal">Rp5.000</span>
                </div>
            </div>

            <button class="btn-save" onclick="openPaymentModal()">Simpan Pesanan</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="paymentModal">
    <div class="modal-card">
        <div class="modal-title">TOTAL PEMBAYARAN</div>
        <div class="modal-total" id="modalTotal">Rp0</div>
        <div class="separator"></div>
        <div class="modal-title">Metode Pembayaran</div>
        <div class="method-row" id="methodRow">
            <button class="pill active" data-method="Cash">Cash</button>
            <button class="pill" data-method="E-Wallet">E-Wallet</button>
        </div>
        <div class="separator"></div>

        <!-- Customer Name -->
        <div class="modal-title">Nama Pelanggan</div>
        <input type="text" id="customerNameInput" class="dt-input" style="margin-bottom:14px;" placeholder="Masukkan nama pelanggan" value="Chesi">

        <!-- Cash Section -->
        <div id="cashPaymentSection">
            <div class="modal-title">Jumlah Uang</div>
            <input type="text" id="customAmountInput" class="dt-input" style="margin-bottom:8px;" placeholder="Ketik jumlah uang..." oninput="onCustomAmountInput()">
            <div style="font-size:14px;font-weight:700;color:#111;margin-bottom:8px;" id="enteredAmountLabel">Rp0</div>
            <div class="amount-grid" id="amountGrid">
                <button class="chip" data-amount="10000">Rp10.000</button>
                <button class="chip" data-amount="20000">Rp20.000</button>
                <button class="chip" data-amount="50000">Rp50.000</button>
                <button class="chip" data-amount="100000">Rp100.000</button>
                <button class="chip" data-amount="200000">Rp200.000</button>
            </div>
            <div class="separator"></div>
            <div class="modal-title">Kembalian</div>
            <div style="font-size:14px;font-weight:700;color:#111;" id="changeLabel">Rp0</div>
            <div class="separator"></div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="btnCancelPay">Batal</button>
            <button class="btn-process" id="btnProcessPay">Proses Pembayaran</button>
        </div>
    </div>
</div>
<div class="modal-overlay" id="strukModal" style="z-index: 60;">
    <div class="struk-card">
        <div class="struk-header">
            <div class="struk-brand">coffith Coffee &amp; Kitchen</div>
            <div class="struk-addr">Jl. Melati No. 88, Depok</div>
            <div class="struk-addr">Telp: 082214567</div>
        </div>
        
        <div class="struk-meta">
            <div class="meta-row">
                <span>No. Faktur</span>
                <span id="strukInv">INV-001</span>
            </div>
            <div class="meta-row">
                <span>Nama Pelanggan</span>
                <span id="strukCust">Chesi</span>
            </div>
            <div class="meta-row">
                <span>Tanggal</span>
                <span id="strukDate">01-01-25</span>
            </div>
            <div class="meta-row">
                <span>Kasir</span>
                <span id="strukCashier">Miranda</span>
            </div>
        </div>

        <div class="struk-items" id="strukItems">
            <!-- Items rendered here -->
        </div>

        <div class="struk-totals">
            <div class="total-row">
                <span>Subtotal :</span>
                <span id="strukSubtotal">Rp0</span>
            </div>
            <div class="total-row">
                <span>Service :</span>
                <span id="strukService">Rp0</span>
            </div>
            <div class="total-row">
                <span>Pajak :</span>
                <span id="strukTax">Rp0</span>
            </div>
            <div class="grand-total total-row">
                <span>Grand Total :</span>
                <span id="strukGrandTotal">Rp0</span>
            </div>
        </div>

        <div class="struk-footer">
            ---Terimakasih---<br>
            RESTORAN XYZ
        </div>

        <button class="btn-print-struk" onclick="window.print()">Cetak Struk</button>
        <button class="btn-close-struk" onclick="closeStruk()">Kembali</button>
    </div>
</div>
    <script>
        // --- Order Logic ---
        let cart = [];
        const serviceFee = 5000;
        let currentGrandTotal = 0;

        // Initialize
        renderCart();

        function parsePrice(priceStr) {
            return parseInt(priceStr.replace(/[^0-9]/g, '')) || 0;
        }

        function formatIDR(n) {
            return 'Rp' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function addToCart(name, priceStr) {
            const price = parsePrice(priceStr);
            const existing = cart.find(item => item.name === name);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({ name: name, price: price, qty: 1 });
            }
            renderCart();
        }

        function updateQty(index, change) {
            if (cart[index]) {
                cart[index].qty += change;
                if (cart[index].qty <= 0) {
                    cart.splice(index, 1);
                }
                renderCart();
            }
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function resetOrder() {
            if(confirm('Reset pesanan?')) {
                cart = [];
                document.getElementById('discountInput').value = '0';
                document.getElementById('taxInput').value = '10%';
                renderCart();
            }
        }

        function renderCart() {
            const container = document.getElementById('orderItemsContainer');
            container.innerHTML = '';

            if (cart.length === 0) {
                container.innerHTML = '<div style="text-align:center; color:#777; margin-top:20px;">Belum ada pesanan</div>';
            } else {
                cart.forEach((item, index) => {
                    const itemTotal = item.price * item.qty;
                    const html = `
                        <div class="order-card">
                            <div class="oc-header">
                                <span class="oc-name">${item.name}</span>
                                <div class="oc-qty-control">
                                    <button class="qty-btn" onclick="updateQty(${index}, -1)">-</button>
                                    <span style="font-size:13px; font-weight:600;">${item.qty}</span>
                                    <button class="qty-btn" onclick="updateQty(${index}, 1)">+</button>
                                </div>
                            </div>
                            <div class="oc-price">${formatIDR(item.price)}</div>
                            <div class="oc-subtotal">Subtotal: ${formatIDR(itemTotal)}</div>
                            <span class="btn-trash" onclick="removeItem(${index})">🗑️</span>
                        </div>
                    `;
                    container.innerHTML += html;
                });
            }

            calculateTotals();
        }

        function parsePercentageOrValue(inputStr, baseValue) {
            let valStr = inputStr.replace(/[^0-9.,%]/g, '');
            if (valStr.includes('%')) {
                const pct = parseFloat(valStr.replace('%', '')) || 0;
                return (baseValue * pct) / 100;
            }
            return parseFloat(valStr.replace(',', '.')) || 0;
        }

        function calculateTotals() {
            let subtotal = 0;
            cart.forEach(item => subtotal += (item.price * item.qty));

            const discountInput = document.getElementById('discountInput').value;
            const taxInput = document.getElementById('taxInput').value;

            const discount = parsePercentageOrValue(discountInput, subtotal);
            const taxableAmount = subtotal - discount;
            const tax = parsePercentageOrValue(taxInput, taxableAmount);
            
            const total = taxableAmount + tax + serviceFee;
            currentGrandTotal = total;

            document.getElementById('subtotalVal').textContent = formatIDR(subtotal);
            document.getElementById('discountVal').textContent = discount > 0 ? '-' + formatIDR(discount) : '-';
            document.getElementById('taxVal').textContent = formatIDR(tax);
            document.getElementById('serviceVal').textContent = formatIDR(serviceFee);
            document.getElementById('totalVal').textContent = formatIDR(total);
        }


        // --- Payment Modal Logic ---
        const modal = document.getElementById('paymentModal');
        const strukModal = document.getElementById('strukModal');
        const btnCancelPay = document.getElementById('btnCancelPay');
        const btnProcessPay = document.getElementById('btnProcessPay');
        const enteredAmountLabel = document.getElementById('enteredAmountLabel');
        const changeLabel = document.getElementById('changeLabel');
        const chips = Array.from(document.querySelectorAll('#amountGrid .chip'));
        const methodButtons = Array.from(document.querySelectorAll('#methodRow .pill'));
        const cashSection = document.getElementById('cashPaymentSection');
        
        let payAmount = 0;
        let selectedMethod = 'Cash';

        function updateChange(){
            const c = Math.max(payAmount - currentGrandTotal, 0);
            enteredAmountLabel.textContent = formatIDR(payAmount);
            changeLabel.textContent = formatIDR(c);
        }

        function openPaymentModal() {
            if (cart.length === 0) {
                alert('Keranjang kosong!');
                return;
            }
            modal.style.display = 'flex';
            document.getElementById('modalTotal').textContent = formatIDR(currentGrandTotal);
            
            // Reset to Cash by default
            selectPaymentMethod('Cash');
            
            // Default pay amount to total
            payAmount = currentGrandTotal;
            document.getElementById('customAmountInput').value = formatIDR(payAmount).replace('Rp', '');
            updateChange();
        }
        
        function selectPaymentMethod(method) {
            selectedMethod = method;
            
            // Update buttons UI
            methodButtons.forEach(b => {
                if(b.dataset.method === method) b.classList.add('active');
                else b.classList.remove('active');
            });

            // Toggle Sections
            if (method === 'Cash') {
                cashSection.style.display = 'block';
            } else {
                cashSection.style.display = 'none';
            }
        }

        btnCancelPay.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        btnProcessPay.addEventListener('click', () => {
            // Validate only if Cash
            if (selectedMethod === 'Cash' && payAmount < currentGrandTotal) {
                alert('Uang pembayaran kurang!');
                return;
            }
            
            const customerName = document.getElementById('customerNameInput').value.trim() || 'Umum';
            const changeAmount = selectedMethod === 'Cash' ? Math.max(payAmount - currentGrandTotal, 0) : 0;
            
            // Prepare payload
            const payload = {
                items: cart.map(it => ({ name: it.name, qty: it.qty, price: it.price })),
                total: currentGrandTotal,
                payment_method: selectedMethod,
                customer_name: customerName,
                amount_paid: selectedMethod === 'Cash' ? payAmount : 0,
                change: changeAmount
            };

            // Close payment modal
            modal.style.display = 'none';

            if (selectedMethod === 'Cash') {
                // Cash flow: save and show receipt directly
                fetch('{{ route("kasir.order.checkout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(() => {
                    showStruk();
                })
                .catch(() => {
                    alert('Gagal menyimpan transaksi, namun struk tetap ditampilkan.');
                    showStruk();
                });
            } else {
                // Non-cash flow: save sale first, then Midtrans Snap
                fetch('{{ route("kasir.order.checkout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(data => {
                    const saleId = data.sale_id;
                    // Get Midtrans snap token
                    return fetch('{{ route("kasir.order.midtrans_pay") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ sale_id: saleId })
                    })
                    .then(r => r.ok ? r.json() : Promise.reject())
                    .then(mtData => {
                        if (mtData.snap_token) {
                            snap.pay(mtData.snap_token, {
                                onSuccess: function() {
                                    confirmMidtransAndShowStruk(saleId);
                                },
                                onPending: function() {
                                    alert('Pembayaran masih pending. Silakan tunggu konfirmasi.');
                                    confirmMidtransAndShowStruk(saleId);
                                },
                                onError: function() {
                                    alert('Pembayaran gagal!');
                                    resetAfterPayment();
                                },
                                onClose: function() {
                                    resetAfterPayment();
                                }
                            });
                        } else {
                            alert('Gagal mendapatkan token pembayaran.');
                            resetAfterPayment();
                        }
                    })
                    .catch(() => {
                        alert('Gagal memproses pembayaran Midtrans.');
                        resetAfterPayment();
                    });
                })
                .catch(() => {
                    alert('Gagal menyimpan transaksi.');
                    resetAfterPayment();
                });
            }
        });

        function confirmMidtransAndShowStruk(saleId) {
            fetch('{{ route("kasir.order.confirm_midtrans") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ sale_id: saleId })
            })
            .then(r => r.json())
            .then(data => {
                showStruk();
            })
            .catch(() => {
                showStruk();
            });
        }

        function resetAfterPayment() {
            cart = [];
            resetOrderInputs();
            renderCart();
        }

        function showStruk() {
            // Generate Invoice No
            const date = new Date();
            const invNo = 'INV-' + date.getFullYear() + '-' + (date.getMonth()+1).toString().padStart(2,'0') + date.getDate().toString().padStart(2,'0') + '-' + Math.floor(Math.random()*1000).toString().padStart(4,'0');
            
            const customerName = document.getElementById('customerNameInput').value.trim() || 'Umum';
            
            // Populate Data
            document.getElementById('strukInv').textContent = invNo;
            document.getElementById('strukCust').textContent = customerName;
            document.getElementById('strukDate').textContent = date.toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' });
            
            // Items
            const itemsContainer = document.getElementById('strukItems');
            itemsContainer.innerHTML = '';
            cart.forEach(item => {
                const html = `
                    <div class="struk-item-row">
                        <div style="width:20px;">${item.qty}</div>
                        <div class="item-name">${item.name}</div>
                        <div class="item-price">${formatIDR(item.price * item.qty)}</div>
                    </div>
                `;
                itemsContainer.innerHTML += html;
            });
            
            // Totals
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const discountInput = document.getElementById('discountInput').value;
            const taxInput = document.getElementById('taxInput').value;
            const discount = parsePercentageOrValue(discountInput, subtotal);
            const taxableAmount = subtotal - discount;
            const tax = parsePercentageOrValue(taxInput, taxableAmount);
            const total = taxableAmount + tax + serviceFee;

            document.getElementById('strukSubtotal').textContent = formatIDR(subtotal);
            document.getElementById('strukService').textContent = formatIDR(serviceFee);
            document.getElementById('strukTax').textContent = formatIDR(tax);
            document.getElementById('strukGrandTotal').textContent = formatIDR(total);

            strukModal.style.display = 'flex';
        }
        
        function closeStruk() {
            strukModal.style.display = 'none';
            // Clear cart and reset
            cart = [];
            resetOrderInputs();
            renderCart();
        }

        function resetOrderInputs() {
            document.getElementById('discountInput').value = '0';
            document.getElementById('taxInput').value = '10%';
        }

        function onCustomAmountInput() {
            const input = document.getElementById('customAmountInput');
            const raw = input.value.replace(/[^0-9]/g, '');
            payAmount = parseInt(raw, 10) || 0;
            updateChange();
        }

        chips.forEach(ch => ch.addEventListener('click', () => {
            payAmount = parseInt(ch.dataset.amount, 10);
            document.getElementById('customAmountInput').value = formatIDR(payAmount).replace('Rp', '');
            updateChange();
        }));

        methodButtons.forEach(b => b.addEventListener('click', () => {
            selectPaymentMethod(b.dataset.method);
        }));

        // --- Menu Filtering Logic ---
        let currentOrderCategory = 'Semua';

        function filterOrderCategory(category, btn) {
            currentOrderCategory = category;
            document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
            if(btn) btn.classList.add('active');
            applyOrderFilters();
        }

        function filterOrderSearch() {
            applyOrderFilters();
        }

        function applyOrderFilters() {
            const searchValue = document.getElementById('orderSearchInput').value.toLowerCase();
            const items = document.querySelectorAll('.menu-btn-item');

            items.forEach(item => {
                const name = item.getAttribute('data-name');
                const category = item.getAttribute('data-category');
                
                const matchesCategory = (currentOrderCategory === 'Semua') || (category === currentOrderCategory);
                const matchesSearch = name.includes(searchValue);

                item.style.display = matchesCategory && matchesSearch ? 'flex' : 'none';
            });
        }
    </script>
@endsection