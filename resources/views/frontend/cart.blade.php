@extends('frontend.layout')

@section('title', 'Keranjang Belanja | coffith Coffee &amp; Kitchen')

@section('content')
<main class="max-w-[1280px] mx-auto px-container-margin-mobile md:px-container-margin-desktop py-section-gap">
    @if (session('success'))
        <div class="mb-6 p-4 bg-primary/10 border border-primary/20 rounded-xl text-primary font-body-md">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-error/10 border border-error/20 rounded-xl text-error font-body-md">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-error/10 border border-error/20 rounded-xl text-error font-body-md">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center gap-4 mb-8">
        <h1 class="font-headline-lg text-headline-lg text-on-surface">Keranjang Belanja</h1>
        <span id="cart-count-header" class="bg-surface-container-highest text-on-surface-variant px-3 py-1 rounded-full font-label-sm text-label-sm">0 Item</span>
    </div>

    <div id="cart-empty" class="text-center py-20">
        <span class="material-symbols-outlined text-primary text-6xl mb-4" data-icon="shopping_cart">shopping_cart</span>
        <h2 class="font-headline-md text-headline-md text-on-surface mb-2">Keranjang Kosong</h2>
        <p class="text-on-surface-variant font-body-md mb-8">Belum ada item yang ditambahkan ke keranjang.</p>
        <a href="{{ route('frontend.menu') }}" class="inline-flex items-center gap-2 bg-primary text-on-primary px-6 py-3 rounded-full font-headline-md text-headline-md transition-all hover:opacity-90">
            <span class="material-symbols-outlined" data-icon="restaurant_menu">restaurant_menu</span>
            Lihat Menu
        </a>
    </div>

    <div id="cart-content" class="hidden">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
            <div class="md:col-span-8">
                <div id="cart-items-container" class="space-y-6"></div>
                <div class="mt-8 flex justify-start">
                    <a href="{{ route('frontend.menu') }}" class="flex items-center gap-2 text-on-secondary-container hover:text-primary transition-colors">
                        <span class="material-symbols-outlined" data-icon="arrow_back">arrow_back</span>
                        <span class="font-label-md text-label-md">Kembali ke Menu</span>
                    </a>
                </div>
            </div>

            <div class="md:col-span-4">
                <form id="checkout-form" method="POST" action="{{ route('frontend.cart.checkout') }}">
                    @csrf
                    <div id="checkout-items"></div>
                    <div class="bg-surface-container-high p-8 rounded-2xl shadow-sm border border-secondary/10 sticky top-24">
                        <h2 class="font-headline-md text-headline-md text-on-surface mb-6 border-b border-secondary/10 pb-4">Ringkasan Pesanan</h2>
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-on-surface-variant">
                                <span class="font-body-md text-body-md">Subtotal</span>
                                <span id="subtotal-amount" class="font-body-md text-body-md">Rp 0</span>
                            </div>
                        <div class="flex justify-between text-on-surface-variant">
                            <span class="font-body-md text-body-md">Pajak ({{ $pajak ?? 10 }}%)</span>
                            <span id="tax-amount" class="font-body-md text-body-md">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-on-surface-variant">
                            <span class="font-body-md text-body-md">Service ({{ $service ?? 5 }}%)</span>
                            <span id="service-amount" class="font-body-md text-body-md">Rp 0</span>
                        </div>
                            <div class="pt-4 border-t border-secondary/10 flex justify-between">
                                <span class="font-headline-md text-headline-md text-on-surface">Total</span>
                                <span id="total-amount" class="font-headline-md text-headline-md text-primary">Rp 0</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <button class="w-full bg-primary text-on-primary py-4 rounded-xl font-headline-md text-headline-md transition-all active:scale-[0.98] hover:opacity-90 mt-4 shadow-lg shadow-primary/10" type="submit">
                                Lanjut ke Pembayaran
                            </button>
                            <div class="flex items-center justify-center gap-2 text-on-surface-variant py-2">
                                <span class="material-symbols-outlined text-[16px]" data-icon="lock">lock</span>
                                <span class="font-label-sm text-label-sm uppercase tracking-wider">Transaksi Aman &amp; Terenkripsi</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
function formatPrice(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function renderCart() {
    var cart = getCart();
    var container = document.getElementById('cart-items-container');
    var emptyState = document.getElementById('cart-empty');
    var cartContent = document.getElementById('cart-content');
    var headerCount = document.getElementById('cart-count-header');

    var totalItems = cart.reduce(function(sum, item) { return sum + item.qty; }, 0);
    headerCount.textContent = totalItems + ' Item';

    if (cart.length === 0) {
        emptyState.classList.remove('hidden');
        cartContent.classList.add('hidden');
        return;
    }

    emptyState.classList.add('hidden');
    cartContent.classList.remove('hidden');

    var itemsHtml = '';
    var subtotal = 0;

    for (var i = 0; i < cart.length; i++) {
        var item = cart[i];
        var itemTotal = item.price * item.qty;
        subtotal += itemTotal;
        var imageHtml = item.image
            ? '<img class="w-full h-full object-cover" src="' + item.image + '" alt="' + item.name + '">'
            : '<div class="w-full h-full flex items-center justify-center bg-secondary/10 text-primary"><span class="material-symbols-outlined text-4xl" data-icon="restaurant">restaurant</span></div>';

        itemsHtml += '<div class="flex flex-col md:flex-row gap-6 p-6 rounded-xl bg-surface-container-low border border-secondary/20 transition-all hover:bg-surface-container">'
            + '<div class="w-full md:w-32 h-32 rounded-lg overflow-hidden flex-shrink-0">' + imageHtml + '</div>'
            + '<div class="flex flex-col flex-grow justify-between">'
            + '<div class="flex justify-between items-start">'
            + '<div><h3 class="font-headline-md text-headline-md text-on-surface">' + item.name + '</h3></div>'
            + '<p class="font-headline-md text-headline-md text-primary">Rp ' + formatPrice(itemTotal) + '</p>'
            + '</div>'
            + '<div class="flex justify-between items-center mt-4">'
            + '<div class="flex items-center gap-4 bg-surface-container-highest rounded-full px-4 py-2">'
            + '<button class="text-primary hover:opacity-70 transition-opacity" onclick="updateQty(' + item.id + ', -1)"><span class="material-symbols-outlined" data-icon="remove">remove</span></button>'
            + '<span class="font-label-md text-label-md w-6 text-center">' + item.qty + '</span>'
            + '<button class="text-primary hover:opacity-70 transition-opacity" onclick="updateQty(' + item.id + ', 1)"><span class="material-symbols-outlined" data-icon="add">add</span></button>'
            + '</div>'
            + '<button class="text-error font-label-sm text-label-sm flex items-center gap-1 hover:underline transition-all" onclick="removeFromCart(' + item.id + ')">'
            + '<span class="material-symbols-outlined text-[18px]" data-icon="delete">delete</span> Hapus'
            + '</button>'
            + '</div>'
            + '<div class="mt-3">'
            + '<textarea class="w-full bg-surface-container-highest rounded-xl px-4 py-3 font-body-md text-body-md text-on-surface placeholder-on-surface-variant border border-secondary/20 focus:outline-none focus:border-primary resize-none" rows="2" placeholder="Catatan (opsional)..." oninput="updateNotes(' + item.id + ', this.value)">' + (item.notes || '') + '</textarea>'
            + '</div>'
            + '</div>'
            + '</div>';
    }

    container.innerHTML = itemsHtml;

    var taxRate = {{ $pajak ?? 10 }};
    var serviceRate = {{ $service ?? 5 }};
    var tax = Math.round(subtotal * taxRate / 100);
    var serviceFee = Math.round(subtotal * serviceRate / 100);
    var total = subtotal + tax + serviceFee;

    document.getElementById('subtotal-amount').textContent = 'Rp ' + formatPrice(subtotal);
    document.getElementById('tax-amount').textContent = 'Rp ' + formatPrice(tax);
    document.getElementById('service-amount').textContent = 'Rp ' + formatPrice(serviceFee);
    document.getElementById('total-amount').textContent = 'Rp ' + formatPrice(total);

    var checkoutContainer = document.getElementById('checkout-items');
    checkoutContainer.innerHTML = cart.map(function(item, idx) {
        return '<input type="hidden" name="items[' + idx + '][id]" value="' + item.id + '">'
            + '<input type="hidden" name="items[' + idx + '][name]" value="' + item.name + '">'
            + '<input type="hidden" name="items[' + idx + '][qty]" value="' + item.qty + '">'
            + '<input type="hidden" name="items[' + idx + '][price]" value="' + item.price + '">'
            + '<input type="hidden" name="items[' + idx + '][notes]" value="' + (item.notes || '') + '">';
    }).join('');
}

function removeFromCart(id) {
    var cart = getCart();
    cart = cart.filter(function(item) { return item.id !== id; });
    saveCart(cart);
    renderCart();
}

function updateNotes(id, value) {
    var cart = getCart();
    for (var i = 0; i < cart.length; i++) {
        if (cart[i].id === id) {
            cart[i].notes = value;
            break;
        }
    }
    saveCart(cart);
}

function updateQty(id, delta) {
    var cart = getCart();
    for (var i = 0; i < cart.length; i++) {
        if (cart[i].id === id) {
            cart[i].qty = Math.max(1, cart[i].qty + delta);
            break;
        }
    }
    saveCart(cart);
    renderCart();
}

document.addEventListener('DOMContentLoaded', function() {
    renderCart();
    @if (session('checkout_success'))
        localStorage.removeItem('pos_cart');
        renderCart();
    @endif
});
</script>
@endsection
