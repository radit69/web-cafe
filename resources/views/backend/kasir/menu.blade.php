@extends('backend.kasir.layout')

@section('title', 'Menu Kasir')
@section('page_title', 'Semua Menu')

@section('header_right')
<div class="search-box">
    <span class="search-icon">🔍</span>
    <input type="text" class="search-input" placeholder="Cari menu...">
</div>
@endsection

@section('styles')
<style>
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 24px;
        padding-top: 10px;
    }

    .menu-card {
        background: transparent;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        text-align: center;
    }

    .menu-img {
        width: 100%;
        aspect-ratio: 1/1;
        border-radius: 20px;
        object-fit: cover;
        background: #eee;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .menu-name {
        font-size: 16px;
        font-weight: 700;
        color: #354024;
    }

    .filter-btn {
        background: #efe0cd; /* Default inactive background */
        border: 1px solid #354024;
    }
    .filter-btn.active {
        background: #354024 !important;
        color: #fff !important;
    }
</style>
@endsection

@section('content')
    @php
        $categories = collect($menus)->pluck('category')->unique()->filter()->values();
    @endphp
    <div class="filter-row">
        <button class="filter-btn active" data-cat="Semua">Semua</button>
        @foreach($categories as $cat)
            <button class="filter-btn" data-cat="{{ $cat }}">{{ $cat }}</button>
        @endforeach
    </div>

    <div style="background: #faecd8; padding: 24px; border-radius: 24px;">
        <div class="menu-grid">
            @foreach($menus as $m)
            <div class="menu-card" data-name="{{ strtolower($m->name) }}" data-cat="{{ $m->category }}">
                <img src="{{ $m->image_url }}" alt="{{ $m->name }}" class="menu-img">
                <div class="menu-name">{{ $m->name }}</div>
            </div>
            @endforeach
        </div>
    </div>
    <script>
        const cards = Array.from(document.querySelectorAll('.menu-card'));
        const filterButtons = Array.from(document.querySelectorAll('.filter-btn'));
        const searchInput = document.querySelector('.search-input');
        let currentCat = 'Semua';
        function apply(){
            const q = (searchInput?.value || '').toLowerCase();
            cards.forEach(c=>{
                const matchCat = currentCat==='Semua' || c.dataset.cat===currentCat;
                const matchName = !q || c.dataset.name.includes(q);
                c.style.display = matchCat && matchName ? '' : 'none';
            });
        }
        filterButtons.forEach(b=>b.addEventListener('click',()=>{
            filterButtons.forEach(x=>x.classList.remove('active'));
            b.classList.add('active');
            currentCat = b.dataset.cat;
            apply();
        }));
        if(searchInput){searchInput.addEventListener('input',apply);}
        apply();
    </script>
@endsection