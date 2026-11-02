# Hiragana Footwear - Official Website & Catalog Showcase

Website resmi Company Profile & Katalog Penjualan Produk untuk **Hiragana Footwear** (Alas Kaki Pria Kasual & Distro Asal Kab. Bekasi).

---

## 🚀 Akses di Komputer Anda (Tanpa Domain Online)

Karena folder ini berada di direktori Laravel Herd (`/Users/admin/Herd/hiraganafootwear`), Anda **TIDAK PERLU** memiliki domain internet terlebih dahulu. Laravel Herd otomatis melayani website dan panel admin secara lokal:

* 👉 **Website Utama:** [http://hiraganafootwear.test](http://hiraganafootwear.test)
* 👉 **Panel Pengelola / Admin:** [http://hiraganafootwear.test/admin](http://hiraganafootwear.test/admin)

> 🔑 **Kata Sandi Admin Default:** `admin123` *(Bisa langsung diubah di menu Pengaturan Keamanan).*

---

## 🛠️ Fitur Panel Admin (`/admin`)

Melalui halaman `/admin`, Anda tidak perlu lagi mengedit kode PHP manual:
1. **Informasi Toko & Kontak:** Ubah nama brand, slogan/tagline, nomor WhatsApp penerima pesanan, jam operasional CS, dan alamat workshop.
2. **E-Commerce & Medsos:** Hubungkan link Shopee Official, Tokopedia Store, Instagram, dan TikTok.
3. **Kelola Harga Produk (15 Seri):** Ubah harga promo jual tiap produk langsung dari tabel dan simpan seketika.
4. **Keamanan Sandi:** Fitur ganti kata sandi pengelola.

---

## 📁 Struktur Direktori & File

```
hiraganafootwear/
├── index.php                # Halaman utama Company Profile & Katalog Penjualan
├── katalog.html             # Master data katalog standalone siap cetak / PDF
├── admin/
│   ├── index.php            # Dashboard Panel Pengelola Toko
│   ├── login.php            # Halaman Login Admin
│   └── logout.php           # Logout Session
├── data/
│   ├── settings.json        # Database pengaturan info toko, kontak & hash password
│   └── products.json        # Database 15 produk, varian, dan harga
└── README.md                # Dokumentasi panduan penggunaan
```
