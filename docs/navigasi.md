# Struktur Navigasi - Sistem POS

## 1. Public (Belum Login)

```
┌─────────────────────────────────────────────────┐
│                    NAVBAR                        │
├──────────┬──────────┬──────────┬────────────────┤
│  Beranda │   Menu   │ Keranjang│  Masuk / Daftar│
└──────────┴──────────┴──────────┴────────────────┘
```

| Halaman | URL | Keterangan |
|---------|-----|------------|
| Beranda | `/` | Landing page, promo, menu favorit |
| Menu | `/menu` | Daftar semua menu |
| Menu Detail | `/menu/{id}` | Detail satu menu |
| Keranjang | `/keranjang` | Checkout menu (Midtrans) |
| Masuk | `/masuk` | Login customer |
| Daftar | `/daftar` | Register customer |

---

## 2. Customer (Login sebagai Pelanggan)

```
┌──────────────────────────────────────────────────────┐
│                       NAVBAR                         │
├──────────┬──────────┬──────────┬──────────┬─────────┤
│  Beranda │   Menu   │ Reservasi│  Status  │ Profil  │
│          │          │          │          │ [Logout]│
└──────────┴──────────┴──────────┴──────────┴─────────┘
```

| Halaman | URL | Keterangan |
|---------|-----|------------|
| Beranda | `/` | Landing page |
| Menu | `/menu` | Daftar menu |
| Reservasi | `/reservasi` | Form buat reservasi |
| Status Reservasi | `/status` | Cek status reservasi, bayar DP, bayar sisa |
| Keranjang | `/keranjang` | Checkout menu |
| Profil | `/profil` | Edit nama & password |
| Logout | POST `/logout` | Keluar |

---

## 3. Admin (Login sebagai Admin)

```
┌──────────────────────────────────────────────────────────────────┐
│                         SIDEBAR                                  │
├──────────────────────────────────────────────────────────────────┤
│ 📊 Dashboard           → /admin/dashboard                       │
│ 👥 Manajemen User      → /admin/users                           │
│ 👤 Manajemen Customer  → /admin/customers                       │
│ 🍽️ Manajemen Menu      → /admin/menus                           │
│ 📦 Inventaris          → /admin/inventory                       │
│ 🪑 Reservasi           → /admin/reservations                    │
│ 💰 Penjualan           → /admin/sales                           │
│                         ├─ /admin/sales                         │
│                         ├─ /admin/sales/export                  │
│                         └─ /admin/sales/export-pdf              │
│ 🚪 Logout              → POST /logout                           │
└──────────────────────────────────────────────────────────────────┘
```

| Halaman | URL | CRUD | Keterangan |
|---------|-----|------|------------|
| Dashboard | `/admin/dashboard` | - | Statistik, grafik harian/bulanan |
| User (index) | `/admin/users` | R | List semua user (admin/kasir) |
| User (create) | `/admin/users/create` | C | Tambah user baru |
| User (edit) | `/admin/users/{id}/edit` | U | Edit user |
| User (delete) | `/admin/users/{id}` | D | Hapus user |
| Customer (index) | `/admin/customers` | R | List customer, filter, search |
| Customer (create) | `/admin/customers/create` | C | Tambah customer |
| Customer (edit) | `/admin/customers/{id}/edit` | U | Edit customer |
| Customer (delete) | `/admin/customers/{id}` | D | Hapus customer |
| Customer (toggle) | `/admin/customers/{id}/toggle` | U | Aktif/nonaktif customer |
| Customer (export) | `/admin/customers/export` | - | Export CSV |
| Menu (index) | `/admin/menus` | R | List semua menu |
| Menu (create) | `/admin/menus/create` | C | Tambah menu baru |
| Menu (edit) | `/admin/menus/{id}/edit` | U | Edit menu |
| Menu (delete) | `/admin/menus/{id}` | D | Hapus menu |
| Inventaris | `/admin/inventory` | R | Stok semua menu |
| Inventaris (restock) | `/admin/inventory/restock` | U | Tambah stok |
| Reservasi | `/admin/reservations` | R | List semua reservasi |
| Reservasi (status) | `/admin/reservations/{id}/status` | U | Update status |
| Reservasi (hapus) | `/admin/reservations/{id}` | D | Hapus reservasi |
| Penjualan | `/admin/sales` | R | List penjualan |
| Penjualan (export) | `/admin/sales/export` | - | Export CSV |
| Penjualan (PDF) | `/admin/sales/export-pdf` | - | Export PDF |

---

## 4. Kasir (Login sebagai Kasir)

```
┌──────────────────────────────────────────────────────────────────┐
│                         SIDEBAR                                  │
├──────────────────────────────────────────────────────────────────┤
│ 🍽️ Menu               → /kasir/menu                             │
│ 📦 Stok               → /kasir/stock                            │
│ 🛒 Order              → /kasir/order                            │
│ 📋 Status Order       → /kasir/order-status                     │
│ 🪑 Reservasi           → /kasir/reservations                    │
│ 📊 Laporan            → /kasir/report                           │
│                         └─ /kasir/report/export-pdf             │
│ 🚪 Logout             → POST /logout                            │
└──────────────────────────────────────────────────────────────────┘
```

| Halaman | URL | Keterangan |
|---------|-----|------------|
| Menu | `/kasir/menu` | Lihat daftar menu (read-only) |
| Stok | `/kasir/stock` | Lihat & restok stok menu |
| Order | `/kasir/order` | Buat pesanan baru (kasir input) |
| Status Order | `/kasir/order-status` | Lihat riwayat order, update status |
| Reservasi | `/kasir/reservations` | Lihat & update status reservasi |
| Laporan | `/kasir/report` | Laporan penjualan harian |
| Laporan (PDF) | `/kasir/report/export-pdf` | Export laporan ke PDF |

---

## 5. Sitemap (Semua Halaman)

```
/                           → Beranda (Public)
/menu                       → Menu (Public)
/menu/{id}                  → Menu Detail (Public)
/keranjang                  → Keranjang (Public)
/masuk                      → Login Customer
/daftar                     → Register Customer
/profil                     → Profil Customer (Auth)
/reservasi                  → Buat Reservasi (Auth)
/status                     → Status Reservasi (Auth)
/backend                    → Login Staff (Admin/Kasir)

/admin/dashboard            → Dashboard Admin
/admin/users                → Manajemen User
/admin/customers            → Manajemen Customer
/admin/menus                → Manajemen Menu
/admin/inventory            → Inventaris
/admin/reservations         → Reservasi Admin
/admin/sales                → Penjualan Admin

/kasir/menu                 → Menu Kasir
/kasir/stock                → Stok Kasir
/kasir/order                → Order Kasir
/kasir/order-status         → Status Order Kasir
/kasir/reservations         → Reservasi Kasir
/kasir/report               → Laporan Kasir
```
