-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 21 Bulan Mei 2026 pada 03.38
-- Versi server: 8.0.30
-- Versi PHP: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_jamu_madura`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_trail`
--

CREATE TABLE `audit_trail` (
  `id_audit` bigint NOT NULL,
  `id_pengguna` bigint DEFAULT NULL,
  `aksi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modul` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_lama` json DEFAULT NULL,
  `data_baru` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waktu_aksi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `audit_trail`
--

INSERT INTO `audit_trail` (`id_audit`, `id_pengguna`, `aksi`, `modul`, `data_lama`, `data_baru`, `ip_address`, `waktu_aksi`) VALUES
(1, 1, 'LOGIN', 'auth', NULL, '{\"email\": \"admin@jamu.com\", \"status\": \"aktif\", \"id_role\": 1, \"username\": \"admin\", \"id_pengguna\": 1, \"nama_lengkap\": \"Admin Utama\"}', '127.0.0.1', '2026-05-21 00:47:54'),
(2, 1, 'create', 'stok_keluar', NULL, '{\"catatan\": \"Stok Keluar\", \"id_order\": 2, \"pengguna\": {\"email\": \"admin@jamu.com\", \"status\": \"aktif\", \"id_role\": 1, \"username\": \"admin\", \"created_at\": \"2026-05-21T07:45:40.000000Z\", \"updated_at\": \"2026-05-21T07:45:40.000000Z\", \"id_pengguna\": 1, \"nama_lengkap\": \"Admin Utama\"}, \"created_at\": \"2026-05-21T01:14:04.000000Z\", \"distributor\": {\"email\": \"dist2@mail.com\", \"alamat\": \"Bandung\", \"telepon\": \"082222222222\", \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"updated_at\": \"2026-05-21T07:45:41.000000Z\", \"kontak_person\": \"Rina\", \"id_distributor\": 2, \"kode_distributor\": \"DST002\", \"nama_distributor\": \"Toko Herbal Makmur\"}, \"id_pengguna\": 1, \"id_distributor\": 2, \"id_stok_keluar\": 2, \"tanggal_keluar\": \"2026-05-21T00:00:00.000000Z\", \"nomor_transaksi\": \"SK-20260521-0001\", \"order_distribusi\": {\"status\": \"disetujui\", \"catatan\": \"Order kedua\", \"id_order\": 2, \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"updated_at\": \"2026-05-21T07:45:41.000000Z\", \"id_pengguna\": 3, \"nomor_order\": \"ORD-2026-002\", \"tanggal_order\": \"2026-05-12T00:00:00.000000Z\", \"id_distributor\": 2, \"tanggal_diproses\": null}, \"detail_stok_keluar\": [{\"batch\": {\"id_batch\": 3, \"id_produk\": 3, \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"keterangan\": \"Produk premium\", \"nomor_batch\": \"BATCH-VP-001\", \"tanggal_expired\": \"2027-03-01T00:00:00.000000Z\", \"tanggal_produksi\": \"2026-03-01T00:00:00.000000Z\"}, \"jumlah\": 675, \"produk\": {\"deskripsi\": \"mengurangi lemak dalam tubuh, menambah nafsu makan dan melancarkan BAB\", \"id_produk\": 3, \"id_satuan\": 1, \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"updated_at\": \"2026-05-21T08:14:04.000000Z\", \"id_kategori\": 1, \"kode_produk\": \"PRD003\", \"nama_produk\": \"galian singset\", \"harga_satuan\": \"72000.00\", \"stok_minimum\": 56, \"stok_terkini\": 3}, \"id_batch\": 3, \"subtotal\": \"48600000.00\", \"id_produk\": 3, \"harga_jual\": \"72000.00\", \"id_stok_keluar\": 2, \"id_detail_keluar\": 2}]}', '127.0.0.1', '2026-05-21 01:14:04'),
(3, 1, 'LOGOUT', 'auth', '{\"email\": \"admin@jamu.com\", \"status\": \"aktif\", \"id_role\": 1, \"username\": \"admin\", \"id_pengguna\": 1, \"nama_lengkap\": \"Admin Utama\"}', NULL, '127.0.0.1', '2026-05-21 01:16:01'),
(4, 2, 'LOGIN', 'auth', NULL, '{\"email\": \"staf@jamu.com\", \"status\": \"aktif\", \"id_role\": 2, \"username\": \"staf\", \"id_pengguna\": 2, \"nama_lengkap\": \"Staf Gudang\"}', '127.0.0.1', '2026-05-21 01:16:10'),
(5, 2, 'SINKRONISASI_EOQ', 'Monitoring EOQ', NULL, '{\"dibuat\": 0, \"diperbarui\": 189}', '127.0.0.1', '2026-05-21 01:17:49'),
(6, 2, 'create', 'stok_keluar', NULL, '{\"catatan\": null, \"id_order\": 2, \"pengguna\": {\"email\": \"staf@jamu.com\", \"status\": \"aktif\", \"id_role\": 2, \"username\": \"staf\", \"created_at\": \"2026-05-21T07:45:40.000000Z\", \"updated_at\": \"2026-05-21T07:45:40.000000Z\", \"id_pengguna\": 2, \"nama_lengkap\": \"Staf Gudang\"}, \"created_at\": \"2026-05-21T01:19:22.000000Z\", \"distributor\": {\"email\": \"dist2@mail.com\", \"alamat\": \"Bandung\", \"telepon\": \"082222222222\", \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"updated_at\": \"2026-05-21T07:45:41.000000Z\", \"kontak_person\": \"Rina\", \"id_distributor\": 2, \"kode_distributor\": \"DST002\", \"nama_distributor\": \"Toko Herbal Makmur\"}, \"id_pengguna\": 2, \"id_distributor\": 2, \"id_stok_keluar\": 3, \"tanggal_keluar\": \"2026-05-21T00:00:00.000000Z\", \"nomor_transaksi\": \"SK-20260521-0002\", \"order_distribusi\": {\"status\": \"disetujui\", \"catatan\": \"Order kedua\", \"id_order\": 2, \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"updated_at\": \"2026-05-21T07:45:41.000000Z\", \"id_pengguna\": 3, \"nomor_order\": \"ORD-2026-002\", \"tanggal_order\": \"2026-05-12T00:00:00.000000Z\", \"id_distributor\": 2, \"tanggal_diproses\": null}, \"detail_stok_keluar\": [{\"batch\": {\"id_batch\": 1, \"id_produk\": 1, \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"keterangan\": \"Produksi awal\", \"nomor_batch\": \"BATCH-KA-001\", \"tanggal_expired\": \"2027-01-01T00:00:00.000000Z\", \"tanggal_produksi\": \"2026-01-01T00:00:00.000000Z\"}, \"jumlah\": 200, \"produk\": {\"deskripsi\": \"mengurangi bau badan, mengurangi bau tidak sedap serta membuat rapet dan kesed area kewanitaan\", \"id_produk\": 1, \"id_satuan\": 1, \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"updated_at\": \"2026-05-21T08:19:22.000000Z\", \"id_kategori\": 3, \"kode_produk\": \"PRD001\", \"nama_produk\": \"Galian Rapat Wangi\", \"harga_satuan\": \"45000.00\", \"stok_minimum\": 87, \"stok_terkini\": 113}, \"id_batch\": 1, \"subtotal\": \"9000000.00\", \"id_produk\": 1, \"harga_jual\": \"45000.00\", \"id_stok_keluar\": 3, \"id_detail_keluar\": 3}]}', '127.0.0.1', '2026-05-21 01:19:22'),
(7, 2, 'SINKRONISASI_EOQ', 'Monitoring EOQ', NULL, '{\"dibuat\": 0, \"diperbarui\": 189}', '127.0.0.1', '2026-05-21 01:19:55'),
(8, 2, 'LOGOUT', 'auth', '{\"email\": \"staf@jamu.com\", \"status\": \"aktif\", \"id_role\": 2, \"username\": \"staf\", \"id_pengguna\": 2, \"nama_lengkap\": \"Staf Gudang\"}', NULL, '127.0.0.1', '2026-05-21 01:23:48'),
(9, 1, 'LOGIN', 'auth', NULL, '{\"email\": \"admin@jamu.com\", \"status\": \"aktif\", \"id_role\": 1, \"username\": \"admin\", \"id_pengguna\": 1, \"nama_lengkap\": \"Admin Utama\"}', '127.0.0.1', '2026-05-21 01:23:52'),
(10, 1, 'LOGOUT', 'auth', '{\"email\": \"admin@jamu.com\", \"status\": \"aktif\", \"id_role\": 1, \"username\": \"admin\", \"id_pengguna\": 1, \"nama_lengkap\": \"Admin Utama\"}', NULL, '127.0.0.1', '2026-05-21 01:23:58'),
(11, 2, 'LOGIN', 'auth', NULL, '{\"email\": \"staf@jamu.com\", \"status\": \"aktif\", \"id_role\": 2, \"username\": \"staf\", \"id_pengguna\": 2, \"nama_lengkap\": \"Staf Gudang\"}', '127.0.0.1', '2026-05-21 01:24:11'),
(12, 2, 'BUKA_LAPORAN', 'Laporan', NULL, '{\"filter\": [], \"jenis_laporan\": \"stok-kedaluwarsa\"}', '127.0.0.1', '2026-05-21 01:24:25'),
(13, 2, 'BUKA_LAPORAN', 'Laporan', NULL, '{\"filter\": [], \"jenis_laporan\": \"stok-kedaluwarsa\"}', '127.0.0.1', '2026-05-21 01:24:28'),
(14, 2, 'BUKA_LAPORAN', 'Laporan', NULL, '{\"filter\": [], \"jenis_laporan\": \"stok-kedaluwarsa\"}', '127.0.0.1', '2026-05-21 01:24:36'),
(15, 2, 'LOGOUT', 'auth', '{\"email\": \"staf@jamu.com\", \"status\": \"aktif\", \"id_role\": 2, \"username\": \"staf\", \"id_pengguna\": 2, \"nama_lengkap\": \"Staf Gudang\"}', NULL, '127.0.0.1', '2026-05-21 01:32:39'),
(16, 3, 'LOGIN', 'auth', NULL, '{\"email\": \"manager@jamu.com\", \"status\": \"aktif\", \"id_role\": 3, \"username\": \"manager\", \"id_pengguna\": 3, \"nama_lengkap\": \"Manajer Operasional\"}', '127.0.0.1', '2026-05-21 01:33:02'),
(17, 3, 'LOGOUT', 'auth', '{\"email\": \"manager@jamu.com\", \"status\": \"aktif\", \"id_role\": 3, \"username\": \"manager\", \"id_pengguna\": 3, \"nama_lengkap\": \"Manajer Operasional\"}', NULL, '127.0.0.1', '2026-05-21 01:41:21'),
(18, 3, 'LOGIN', 'auth', NULL, '{\"email\": \"manager@jamu.com\", \"status\": \"aktif\", \"id_role\": 3, \"username\": \"manager\", \"id_pengguna\": 3, \"nama_lengkap\": \"Manajer Operasional\"}', '127.0.0.1', '2026-05-21 01:41:23'),
(19, 3, 'LOGOUT', 'auth', '{\"email\": \"manager@jamu.com\", \"status\": \"aktif\", \"id_role\": 3, \"username\": \"manager\", \"id_pengguna\": 3, \"nama_lengkap\": \"Manajer Operasional\"}', NULL, '127.0.0.1', '2026-05-21 01:41:54'),
(20, 2, 'LOGIN', 'auth', NULL, '{\"email\": \"staf@jamu.com\", \"status\": \"aktif\", \"id_role\": 2, \"username\": \"staf\", \"id_pengguna\": 2, \"nama_lengkap\": \"Staf Gudang\"}', '127.0.0.1', '2026-05-21 01:42:00'),
(21, 2, 'BUKA_LAPORAN', 'Laporan', NULL, '{\"filter\": [], \"jenis_laporan\": \"stok-kedaluwarsa\"}', '127.0.0.1', '2026-05-21 01:42:28'),
(22, 2, 'BUKA_LAPORAN', 'Laporan', NULL, '{\"filter\": {\"q\": null, \"status\": \"disetujui\", \"kondisi\": \"30_hari\", \"per_page\": \"25\", \"id_produk\": \"32\", \"id_kategori\": null, \"id_supplier\": \"1\", \"status_stok\": null, \"jenis_mutasi\": \"masuk\", \"tanggal_mulai\": \"2026-05-01\", \"id_distributor\": \"1\", \"tanggal_selesai\": \"2026-05-21\"}, \"jenis_laporan\": \"stok-kedaluwarsa\"}', '127.0.0.1', '2026-05-21 01:43:02'),
(23, 2, 'BUKA_LAPORAN', 'Laporan', NULL, '{\"filter\": [], \"jenis_laporan\": \"stok-kedaluwarsa\"}', '127.0.0.1', '2026-05-21 01:43:11'),
(24, 2, 'LOGOUT', 'auth', '{\"email\": \"staf@jamu.com\", \"status\": \"aktif\", \"id_role\": 2, \"username\": \"staf\", \"id_pengguna\": 2, \"nama_lengkap\": \"Staf Gudang\"}', NULL, '127.0.0.1', '2026-05-21 01:50:00'),
(25, 1, 'LOGIN', 'auth', NULL, '{\"email\": \"admin@jamu.com\", \"status\": \"aktif\", \"id_role\": 1, \"username\": \"admin\", \"id_pengguna\": 1, \"nama_lengkap\": \"Admin Utama\"}', '127.0.0.1', '2026-05-21 01:50:13'),
(26, 1, 'create', 'stok_keluar', NULL, '{\"catatan\": null, \"id_order\": 2, \"pengguna\": {\"email\": \"admin@jamu.com\", \"status\": \"aktif\", \"id_role\": 1, \"username\": \"admin\", \"created_at\": \"2026-05-21T07:45:40.000000Z\", \"updated_at\": \"2026-05-21T07:45:40.000000Z\", \"id_pengguna\": 1, \"nama_lengkap\": \"Admin Utama\"}, \"created_at\": \"2026-05-21T02:14:18.000000Z\", \"distributor\": {\"email\": \"dist1@mail.com\", \"alamat\": \"Jakarta\", \"telepon\": \"081111111111\", \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"updated_at\": \"2026-05-21T07:45:41.000000Z\", \"kontak_person\": \"Andi\", \"id_distributor\": 1, \"kode_distributor\": \"DST001\", \"nama_distributor\": \"Distributor Sehat Abadi\"}, \"id_pengguna\": 1, \"id_distributor\": 1, \"id_stok_keluar\": 4, \"tanggal_keluar\": \"2026-05-21T00:00:00.000000Z\", \"nomor_transaksi\": \"SK-20260521-0003\", \"order_distribusi\": {\"status\": \"disetujui\", \"catatan\": \"Order kedua\", \"id_order\": 2, \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"updated_at\": \"2026-05-21T07:45:41.000000Z\", \"id_pengguna\": 3, \"nomor_order\": \"ORD-2026-002\", \"tanggal_order\": \"2026-05-12T00:00:00.000000Z\", \"id_distributor\": 2, \"tanggal_diproses\": null}, \"detail_stok_keluar\": [{\"batch\": {\"id_batch\": 2, \"id_produk\": 2, \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"keterangan\": \"Produksi reguler\", \"nomor_batch\": \"BATCH-BK-001\", \"tanggal_expired\": \"2027-02-01T00:00:00.000000Z\", \"tanggal_produksi\": \"2026-02-01T00:00:00.000000Z\"}, \"jumlah\": 907, \"produk\": {\"deskripsi\": \"Mengurangi lendir berlebih, mengatasi keputihan serta menimbulkan sensasi denyut saat berhubungan\", \"id_produk\": 2, \"id_satuan\": 1, \"created_at\": \"2026-05-21T07:45:41.000000Z\", \"updated_at\": \"2026-05-21T09:14:18.000000Z\", \"id_kategori\": 2, \"kode_produk\": \"PRD002\", \"nama_produk\": \"Empot-empot legit\", \"harga_satuan\": \"93000.00\", \"stok_minimum\": 69, \"stok_terkini\": 4}, \"id_batch\": 2, \"subtotal\": \"84351000.00\", \"id_produk\": 2, \"harga_jual\": \"93000.00\", \"id_stok_keluar\": 4, \"id_detail_keluar\": 4}]}', '127.0.0.1', '2026-05-21 02:14:19'),
(27, 1, 'SINKRONISASI_EOQ', 'Monitoring EOQ', NULL, '{\"dibuat\": 0, \"diperbarui\": 189}', '127.0.0.1', '2026-05-21 02:14:29'),
(28, 1, 'LOGOUT', 'auth', '{\"email\": \"admin@jamu.com\", \"status\": \"aktif\", \"id_role\": 1, \"username\": \"admin\", \"id_pengguna\": 1, \"nama_lengkap\": \"Admin Utama\"}', NULL, '127.0.0.1', '2026-05-21 02:16:09'),
(29, 2, 'LOGIN', 'auth', NULL, '{\"email\": \"staf@jamu.com\", \"status\": \"aktif\", \"id_role\": 2, \"username\": \"staf\", \"id_pengguna\": 2, \"nama_lengkap\": \"Staf Gudang\"}', '127.0.0.1', '2026-05-21 02:16:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `batch`
--

CREATE TABLE `batch` (
  `id_batch` bigint UNSIGNED NOT NULL,
  `id_produk` bigint UNSIGNED DEFAULT NULL,
  `nomor_batch` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_produksi` date DEFAULT NULL,
  `tanggal_expired` date NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `batch`
--

INSERT INTO `batch` (`id_batch`, `id_produk`, `nomor_batch`, `tanggal_produksi`, `tanggal_expired`, `keterangan`, `created_at`) VALUES
(1, 1, 'BATCH-KA-001', '2026-01-01', '2027-01-01', 'Produksi awal', '2026-05-21 07:45:41'),
(2, 2, 'BATCH-BK-001', '2026-02-01', '2027-02-01', 'Produksi reguler', '2026-05-21 07:45:41'),
(3, 3, 'BATCH-VP-001', '2026-03-01', '2027-03-01', 'Produk premium', '2026-05-21 07:45:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-dashboard:distributor-distribution:9d7867f4177ec8c110dafc5e982f30ec', 'a:2:{s:6:\"labels\";a:2:{i:0;s:23:\"Distributor Sehat Abadi\";i:1;s:18:\"Toko Herbal Makmur\";}s:4:\"data\";a:2:{i:0;d:84351000;i:1;d:57780000;}}', 1779330622),
('laravel-cache-dashboard:kpi:9d7867f4177ec8c110dafc5e982f30ec', 'a:8:{s:12:\"total_produk\";a:3:{s:5:\"value\";i:189;s:5:\"label\";s:18:\"Total Produk Aktif\";s:6:\"change\";i:0;}s:16:\"nilai_total_stok\";a:4:{s:5:\"value\";d:4862518000;s:5:\"label\";s:16:\"Nilai Total Stok\";s:6:\"change\";i:0;s:8:\"currency\";b:1;}s:15:\"order_bulan_ini\";a:4:{s:5:\"value\";i:2;s:5:\"label\";s:17:\"Order Periode Ini\";s:6:\"change\";d:100;s:9:\"breakdown\";a:4:{s:7:\"pending\";i:1;s:9:\"disetujui\";i:1;s:7:\"selesai\";i:0;s:7:\"ditolak\";i:0;}}s:10:\"stok_masuk\";a:3:{s:5:\"value\";i:2;s:5:\"label\";s:20:\"Transaksi Stok Masuk\";s:6:\"change\";d:100;}s:11:\"stok_keluar\";a:3:{s:5:\"value\";i:4;s:5:\"label\";s:21:\"Transaksi Stok Keluar\";s:6:\"change\";d:100;}s:22:\"produk_perlu_perhatian\";a:3:{s:5:\"value\";i:16;s:5:\"label\";s:22:\"Produk Perlu Perhatian\";s:6:\"change\";i:0;}s:14:\"pengguna_aktif\";a:3:{s:5:\"value\";i:3;s:5:\"label\";s:14:\"Pengguna Aktif\";s:6:\"change\";i:0;}s:27:\"nilai_distribusi_comparison\";a:3:{s:7:\"current\";d:142131000;s:8:\"previous\";d:0;s:6:\"change\";d:100;}}', 1779330622),
('laravel-cache-dashboard:latest-activities:v3', 'a:3:{s:10:\"stok_masuk\";a:2:{i:0;a:6:{s:13:\"id_stok_masuk\";i:2;s:15:\"nomor_transaksi\";s:11:\"SM-2026-002\";s:13:\"tanggal_masuk\";s:10:\"2026-05-03\";s:13:\"nama_supplier\";s:16:\"PT Rempah Madura\";s:13:\"nama_pengguna\";s:11:\"Staf Gudang\";s:11:\"jumlah_item\";i:1;}i:1;a:6:{s:13:\"id_stok_masuk\";i:1;s:15:\"nomor_transaksi\";s:11:\"SM-2026-001\";s:13:\"tanggal_masuk\";s:10:\"2026-05-01\";s:13:\"nama_supplier\";s:19:\"CV Herbal Nusantara\";s:13:\"nama_pengguna\";s:11:\"Staf Gudang\";s:11:\"jumlah_item\";i:2;}}s:6:\"orders\";a:2:{i:0;a:6:{s:8:\"id_order\";i:2;s:11:\"nomor_order\";s:12:\"ORD-2026-002\";s:13:\"tanggal_order\";s:10:\"2026-05-12\";s:6:\"status\";s:9:\"disetujui\";s:16:\"nama_distributor\";s:18:\"Toko Herbal Makmur\";s:11:\"total_nilai\";d:180000;}i:1;a:6:{s:8:\"id_order\";i:1;s:11:\"nomor_order\";s:12:\"ORD-2026-001\";s:13:\"tanggal_order\";s:10:\"2026-05-10\";s:6:\"status\";s:7:\"pending\";s:16:\"nama_distributor\";s:23:\"Distributor Sehat Abadi\";s:11:\"total_nilai\";d:300000;}}s:5:\"audit\";a:5:{i:0;a:4:{s:4:\"aksi\";s:5:\"LOGIN\";s:5:\"modul\";s:4:\"auth\";s:10:\"waktu_aksi\";s:19:\"2026-05-21 02:16:15\";s:13:\"nama_pengguna\";s:11:\"Staf Gudang\";}i:1;a:4:{s:4:\"aksi\";s:6:\"LOGOUT\";s:5:\"modul\";s:4:\"auth\";s:10:\"waktu_aksi\";s:19:\"2026-05-21 02:16:09\";s:13:\"nama_pengguna\";s:11:\"Admin Utama\";}i:2;a:4:{s:4:\"aksi\";s:16:\"SINKRONISASI_EOQ\";s:5:\"modul\";s:14:\"Monitoring EOQ\";s:10:\"waktu_aksi\";s:19:\"2026-05-21 02:14:29\";s:13:\"nama_pengguna\";s:11:\"Admin Utama\";}i:3;a:4:{s:4:\"aksi\";s:6:\"create\";s:5:\"modul\";s:11:\"stok_keluar\";s:10:\"waktu_aksi\";s:19:\"2026-05-21 02:14:19\";s:13:\"nama_pengguna\";s:11:\"Admin Utama\";}i:4;a:4:{s:4:\"aksi\";s:5:\"LOGIN\";s:5:\"modul\";s:4:\"auth\";s:10:\"waktu_aksi\";s:19:\"2026-05-21 01:50:13\";s:13:\"nama_pengguna\";s:11:\"Admin Utama\";}}}', 1779330621),
('laravel-cache-dashboard:order-status:9d7867f4177ec8c110dafc5e982f30ec', 'a:2:{s:6:\"labels\";a:4:{i:0;s:7:\"pending\";i:1;s:9:\"disetujui\";i:2;s:7:\"selesai\";i:3;s:7:\"ditolak\";}s:4:\"data\";a:4:{i:0;i:1;i:1;i:1;i:2;i:0;i:3;i:0;}}', 1779330622),
('laravel-cache-dashboard:stock-movement:9d7867f4177ec8c110dafc5e982f30ec', 'a:3:{s:6:\"labels\";a:21:{i:0;s:6:\"01 Mei\";i:1;s:6:\"02 Mei\";i:2;s:6:\"03 Mei\";i:3;s:6:\"04 Mei\";i:4;s:6:\"05 Mei\";i:5;s:6:\"06 Mei\";i:6;s:6:\"07 Mei\";i:7;s:6:\"08 Mei\";i:8;s:6:\"09 Mei\";i:9;s:6:\"10 Mei\";i:10;s:6:\"11 Mei\";i:11;s:6:\"12 Mei\";i:12;s:6:\"13 Mei\";i:13;s:6:\"14 Mei\";i:14;s:6:\"15 Mei\";i:15;s:6:\"16 Mei\";i:16;s:6:\"17 Mei\";i:17;s:6:\"18 Mei\";i:18;s:6:\"19 Mei\";i:19;s:6:\"20 Mei\";i:20;s:6:\"21 Mei\";}s:5:\"masuk\";a:21:{i:0;i:180;i:1;i:0;i:2;i:50;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:0;i:13;i:0;i:14;i:0;i:15;i:0;i:16;i:0;i:17;i:0;i:18;i:0;i:19;i:0;i:20;i:0;}s:6:\"keluar\";a:21:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;i:12;i:15;i:13;i:0;i:14;i:0;i:15;i:0;i:16;i:0;i:17;i:0;i:18;i:0;i:19;i:0;i:20;i:1782;}}', 1779330622),
('laravel-cache-dashboard:top-products:9d7867f4177ec8c110dafc5e982f30ec', 'a:2:{s:6:\"labels\";a:3:{i:0;s:17:\"Empot-empot legit\";i:1;s:14:\"galian singset\";i:2;s:18:\"Galian Rapat Wangi\";}s:4:\"data\";a:3:{i:0;i:922;i:1;i:675;i:2;i:200;}}', 1779330622);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_eoq`
--

CREATE TABLE `data_eoq` (
  `id_eoq` bigint UNSIGNED NOT NULL,
  `id_produk` bigint UNSIGNED DEFAULT NULL,
  `permintaan_tahunan` bigint DEFAULT NULL,
  `biaya_pemesanan` bigint DEFAULT NULL,
  `biaya_penyimpanan` bigint DEFAULT NULL,
  `eoq` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `data_eoq`
--

INSERT INTO `data_eoq` (`id_eoq`, `id_produk`, `permintaan_tahunan`, `biaya_pemesanan`, `biaya_penyimpanan`, `eoq`) VALUES
(1, 1, 200, 45000, 7000, 51),
(2, 2, 922, 49000, 3000, 174),
(3, 3, 675, 89000, 6000, 142),
(4, 4, 0, 94000, 9000, 0),
(5, 5, 0, 78000, 9000, 0),
(6, 6, 0, 70000, 8000, 0),
(7, 7, 0, 84000, 2000, 0),
(8, 8, 0, 70000, 9000, 0),
(9, 9, 0, 92000, 6000, 0),
(10, 10, 0, 37000, 7000, 0),
(11, 11, 0, 26000, 4000, 0),
(12, 12, 0, 34000, 9000, 0),
(13, 13, 0, 20000, 1000, 0),
(14, 14, 0, 28000, 2000, 0),
(15, 15, 0, 59000, 9000, 0),
(16, 16, 0, 87000, 8000, 0),
(17, 17, 0, 17000, 9000, 0),
(18, 18, 0, 23000, 8000, 0),
(19, 19, 0, 81000, 9000, 0),
(20, 20, 0, 90000, 10000, 0),
(21, 21, 0, 63000, 6000, 0),
(22, 22, 0, 24000, 9000, 0),
(23, 23, 0, 31000, 4000, 0),
(24, 24, 0, 56000, 10000, 0),
(25, 25, 0, 25000, 3000, 0),
(26, 26, 0, 93000, 6000, 0),
(27, 27, 0, 56000, 8000, 0),
(28, 28, 0, 78000, 3000, 0),
(29, 29, 0, 17000, 10000, 0),
(30, 30, 0, 44000, 1000, 0),
(31, 31, 0, 63000, 8000, 0),
(32, 32, 0, 24000, 9000, 0),
(33, 33, 0, 62000, 2000, 0),
(34, 34, 0, 16000, 10000, 0),
(35, 35, 0, 94000, 6000, 0),
(36, 36, 0, 36000, 3000, 0),
(37, 37, 0, 52000, 2000, 0),
(38, 38, 0, 92000, 8000, 0),
(39, 39, 0, 21000, 6000, 0),
(40, 40, 0, 100000, 5000, 0),
(41, 41, 0, 33000, 2000, 0),
(42, 42, 0, 29000, 7000, 0),
(43, 43, 0, 78000, 9000, 0),
(44, 44, 0, 38000, 6000, 0),
(45, 45, 0, 69000, 3000, 0),
(46, 46, 0, 24000, 8000, 0),
(47, 47, 0, 20000, 1000, 0),
(48, 48, 0, 63000, 1000, 0),
(49, 49, 0, 73000, 2000, 0),
(50, 50, 0, 20000, 1000, 0),
(51, 51, 0, 99000, 8000, 0),
(52, 52, 0, 99000, 8000, 0),
(53, 53, 0, 86000, 2000, 0),
(54, 54, 0, 11000, 9000, 0),
(55, 55, 0, 55000, 2000, 0),
(56, 56, 0, 77000, 8000, 0),
(57, 57, 0, 21000, 9000, 0),
(58, 58, 0, 18000, 8000, 0),
(59, 59, 0, 49000, 7000, 0),
(60, 60, 0, 93000, 2000, 0),
(61, 61, 0, 74000, 6000, 0),
(62, 62, 0, 16000, 2000, 0),
(63, 63, 0, 92000, 9000, 0),
(64, 64, 0, 11000, 5000, 0),
(65, 65, 0, 35000, 3000, 0),
(66, 66, 0, 97000, 10000, 0),
(67, 67, 0, 21000, 2000, 0),
(68, 68, 0, 10000, 6000, 0),
(69, 69, 0, 51000, 8000, 0),
(70, 70, 0, 100000, 5000, 0),
(71, 71, 0, 86000, 10000, 0),
(72, 72, 0, 97000, 8000, 0),
(73, 73, 0, 29000, 3000, 0),
(74, 74, 0, 54000, 5000, 0),
(75, 75, 0, 40000, 3000, 0),
(76, 76, 0, 26000, 2000, 0),
(77, 77, 0, 96000, 3000, 0),
(78, 78, 0, 79000, 7000, 0),
(79, 79, 0, 72000, 10000, 0),
(80, 80, 0, 67000, 7000, 0),
(81, 81, 0, 45000, 9000, 0),
(82, 82, 0, 87000, 2000, 0),
(83, 83, 0, 60000, 8000, 0),
(84, 84, 0, 30000, 7000, 0),
(85, 85, 0, 33000, 3000, 0),
(86, 86, 0, 83000, 8000, 0),
(87, 87, 0, 95000, 8000, 0),
(88, 88, 0, 92000, 2000, 0),
(89, 89, 0, 61000, 9000, 0),
(90, 90, 0, 77000, 10000, 0),
(91, 91, 0, 18000, 9000, 0),
(92, 92, 0, 77000, 4000, 0),
(93, 93, 0, 57000, 1000, 0),
(94, 94, 0, 38000, 6000, 0),
(95, 95, 0, 34000, 5000, 0),
(96, 96, 0, 52000, 3000, 0),
(97, 97, 0, 99000, 6000, 0),
(98, 98, 0, 27000, 4000, 0),
(99, 99, 0, 66000, 10000, 0),
(100, 100, 0, 47000, 7000, 0),
(101, 101, 0, 81000, 10000, 0),
(102, 102, 0, 60000, 6000, 0),
(103, 103, 0, 31000, 7000, 0),
(104, 104, 0, 34000, 3000, 0),
(105, 105, 0, 22000, 1000, 0),
(106, 106, 0, 77000, 4000, 0),
(107, 107, 0, 83000, 4000, 0),
(108, 108, 0, 50000, 5000, 0),
(109, 109, 0, 87000, 5000, 0),
(110, 110, 0, 80000, 8000, 0),
(111, 111, 0, 73000, 9000, 0),
(112, 112, 0, 88000, 6000, 0),
(113, 113, 0, 25000, 10000, 0),
(114, 114, 0, 42000, 5000, 0),
(115, 115, 0, 21000, 3000, 0),
(116, 116, 0, 76000, 2000, 0),
(117, 117, 0, 18000, 9000, 0),
(118, 118, 0, 66000, 9000, 0),
(119, 119, 0, 97000, 4000, 0),
(120, 120, 0, 75000, 8000, 0),
(121, 121, 0, 28000, 2000, 0),
(122, 122, 0, 68000, 4000, 0),
(123, 123, 0, 11000, 3000, 0),
(124, 124, 0, 88000, 7000, 0),
(125, 125, 0, 26000, 9000, 0),
(126, 126, 0, 67000, 8000, 0),
(127, 127, 0, 27000, 1000, 0),
(128, 128, 0, 33000, 9000, 0),
(129, 129, 0, 19000, 4000, 0),
(130, 130, 0, 42000, 1000, 0),
(131, 131, 0, 16000, 5000, 0),
(132, 132, 0, 55000, 2000, 0),
(133, 133, 0, 31000, 8000, 0),
(134, 134, 0, 71000, 6000, 0),
(135, 135, 0, 73000, 2000, 0),
(136, 136, 0, 79000, 5000, 0),
(137, 137, 0, 98000, 6000, 0),
(138, 138, 0, 74000, 3000, 0),
(139, 139, 0, 59000, 5000, 0),
(140, 140, 0, 79000, 3000, 0),
(141, 141, 0, 65000, 7000, 0),
(142, 142, 0, 88000, 5000, 0),
(143, 143, 0, 47000, 10000, 0),
(144, 144, 0, 67000, 10000, 0),
(145, 145, 0, 38000, 8000, 0),
(146, 146, 0, 51000, 1000, 0),
(147, 147, 0, 28000, 7000, 0),
(148, 148, 0, 52000, 8000, 0),
(149, 149, 0, 37000, 2000, 0),
(150, 150, 0, 82000, 5000, 0),
(151, 151, 0, 24000, 4000, 0),
(152, 152, 0, 48000, 1000, 0),
(153, 153, 0, 23000, 3000, 0),
(154, 154, 0, 71000, 6000, 0),
(155, 155, 0, 28000, 8000, 0),
(156, 156, 0, 31000, 6000, 0),
(157, 157, 0, 100000, 5000, 0),
(158, 158, 0, 11000, 8000, 0),
(159, 159, 0, 63000, 7000, 0),
(160, 160, 0, 69000, 8000, 0),
(161, 161, 0, 53000, 8000, 0),
(162, 162, 0, 64000, 3000, 0),
(163, 163, 0, 30000, 7000, 0),
(164, 164, 0, 44000, 7000, 0),
(165, 165, 0, 13000, 9000, 0),
(166, 166, 0, 51000, 3000, 0),
(167, 167, 0, 69000, 7000, 0),
(168, 168, 0, 30000, 8000, 0),
(169, 169, 0, 53000, 4000, 0),
(170, 170, 0, 37000, 8000, 0),
(171, 171, 0, 81000, 3000, 0),
(172, 172, 0, 86000, 2000, 0),
(173, 173, 0, 61000, 2000, 0),
(174, 174, 0, 42000, 9000, 0),
(175, 175, 0, 83000, 5000, 0),
(176, 176, 0, 30000, 9000, 0),
(177, 177, 0, 99000, 4000, 0),
(178, 178, 0, 10000, 4000, 0),
(179, 179, 0, 32000, 8000, 0),
(180, 180, 0, 16000, 10000, 0),
(181, 181, 0, 53000, 7000, 0),
(182, 182, 0, 74000, 1000, 0),
(183, 183, 0, 65000, 8000, 0),
(184, 184, 0, 72000, 1000, 0),
(185, 185, 0, 96000, 6000, 0),
(186, 186, 0, 54000, 2000, 0),
(187, 187, 0, 75000, 1000, 0),
(188, 188, 0, 95000, 6000, 0),
(189, 189, 0, 41000, 1000, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_order`
--

CREATE TABLE `detail_order` (
  `id_detail_order` bigint UNSIGNED NOT NULL,
  `id_order` bigint UNSIGNED DEFAULT NULL,
  `id_produk` bigint UNSIGNED DEFAULT NULL,
  `jumlah_diminta` int NOT NULL,
  `jumlah_disetujui` int DEFAULT '0',
  `harga_satuan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `catatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ;

--
-- Dumping data untuk tabel `detail_order`
--

INSERT INTO `detail_order` (`id_detail_order`, `id_order`, `id_produk`, `jumlah_diminta`, `jumlah_disetujui`, `harga_satuan`, `subtotal`, `catatan`) VALUES
(1, 1, 1, 20, 0, 15000.00, 300000.00, 'Menunggu persetujuan'),
(2, 2, 2, 15, 15, 12000.00, 180000.00, 'Disetujui penuh');

--
-- Trigger `detail_order`
--
DELIMITER $$
CREATE TRIGGER `trg_subtotal_order` BEFORE INSERT ON `detail_order` FOR EACH ROW BEGIN
    SET NEW.subtotal = NEW.jumlah_diminta * NEW.harga_satuan;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_stok_keluar`
--

CREATE TABLE `detail_stok_keluar` (
  `id_detail_keluar` bigint UNSIGNED NOT NULL,
  `id_stok_keluar` bigint UNSIGNED DEFAULT NULL,
  `id_produk` bigint UNSIGNED DEFAULT NULL,
  `id_batch` bigint UNSIGNED DEFAULT NULL,
  `jumlah` int NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00'
) ;

--
-- Dumping data untuk tabel `detail_stok_keluar`
--

INSERT INTO `detail_stok_keluar` (`id_detail_keluar`, `id_stok_keluar`, `id_produk`, `id_batch`, `jumlah`, `harga_jual`, `subtotal`) VALUES
(1, 1, 2, 2, 15, 12000.00, 180000.00),
(2, 2, 3, 3, 675, 72000.00, 48600000.00),
(3, 3, 1, 1, 200, 45000.00, 9000000.00),
(4, 4, 2, 2, 907, 93000.00, 84351000.00);

--
-- Trigger `detail_stok_keluar`
--
DELIMITER $$
CREATE TRIGGER `trg_subtotal_keluar` BEFORE INSERT ON `detail_stok_keluar` FOR EACH ROW BEGIN
    SET NEW.subtotal = NEW.jumlah * NEW.harga_jual;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_stok_keluar` AFTER INSERT ON `detail_stok_keluar` FOR EACH ROW BEGIN
    DECLARE stok_sekarang INT;

    SELECT stok_terkini INTO stok_sekarang
    FROM produk
    WHERE id_produk = NEW.id_produk;

    IF stok_sekarang < NEW.jumlah THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stok tidak mencukupi untuk transaksi ini';
    END IF;

    UPDATE produk
    SET stok_terkini = stok_terkini - NEW.jumlah
    WHERE id_produk = NEW.id_produk;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_stok_masuk`
--

CREATE TABLE `detail_stok_masuk` (
  `id_detail_masuk` bigint UNSIGNED NOT NULL,
  `id_stok_masuk` bigint UNSIGNED DEFAULT NULL,
  `id_produk` bigint UNSIGNED DEFAULT NULL,
  `id_batch` bigint UNSIGNED DEFAULT NULL,
  `jumlah` int NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00'
) ;

--
-- Dumping data untuk tabel `detail_stok_masuk`
--

INSERT INTO `detail_stok_masuk` (`id_detail_masuk`, `id_stok_masuk`, `id_produk`, `id_batch`, `jumlah`, `harga_beli`, `subtotal`) VALUES
(1, 1, 1, 1, 100, 10000.00, 1000000.00),
(2, 1, 2, 2, 80, 9000.00, 720000.00),
(3, 2, 3, 3, 50, 25000.00, 1250000.00);

--
-- Trigger `detail_stok_masuk`
--
DELIMITER $$
CREATE TRIGGER `trg_subtotal_masuk` BEFORE INSERT ON `detail_stok_masuk` FOR EACH ROW BEGIN
    SET NEW.subtotal = NEW.jumlah * NEW.harga_beli;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_stok_masuk` AFTER INSERT ON `detail_stok_masuk` FOR EACH ROW BEGIN
    UPDATE produk
    SET stok_terkini = stok_terkini + NEW.jumlah
    WHERE id_produk = NEW.id_produk;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `distributor`
--

CREATE TABLE `distributor` (
  `id_distributor` bigint UNSIGNED NOT NULL,
  `kode_distributor` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_distributor` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `distributor`
--

INSERT INTO `distributor` (`id_distributor`, `kode_distributor`, `nama_distributor`, `alamat`, `telepon`, `email`, `kontak_person`, `created_at`, `updated_at`) VALUES
(1, 'DST001', 'Distributor Sehat Abadi', 'Jakarta', '081111111111', 'dist1@mail.com', 'Andi', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(2, 'DST002', 'Toko Herbal Makmur', 'Bandung', '082222222222', 'dist2@mail.com', 'Rina', '2026-05-21 07:45:41', '2026-05-21 07:45:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` bigint UNSIGNED NOT NULL,
  `nama_kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Jamu Kesehatan', 'Produk kesehatan herbal', '2026-05-21 07:45:39', '2026-05-21 07:45:39'),
(2, 'Jamu Kecantikan', 'Produk herbal kecantikan', '2026-05-21 07:45:39', '2026-05-21 07:45:39'),
(3, 'Jamu Vitalitas', 'Produk peningkat stamina', '2026-05-21 07:45:39', '2026-05-21 07:45:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_15_134554_create_role_table', 1),
(5, '2026_05_15_134555_create_kategori_table', 1),
(6, '2026_05_15_134557_create_satuan_table', 1),
(7, '2026_05_15_134558_create_supplier_table', 1),
(8, '2026_05_15_134600_create_distributor_table', 1),
(9, '2026_05_15_134601_create_pengguna_table', 1),
(10, '2026_05_15_134603_create_produk_table', 1),
(11, '2026_05_15_134604_create_batch_table', 1),
(12, '2026_05_15_134605_create_order_distribusi_table', 1),
(13, '2026_05_15_134607_create_stok_masuk_table', 1),
(14, '2026_05_15_134608_create_stok_keluar_table', 1),
(15, '2026_05_15_134610_create_detail_order_table', 1),
(16, '2026_05_15_134611_create_detail_stok_masuk_table', 1),
(17, '2026_05_15_134613_create_detail_stok_keluar_table', 1),
(18, '2026_05_15_134614_create_notifikasi_table', 1),
(19, '2026_05_15_134615_create_audit_trail_table', 1),
(20, '2026_05_19_134311_add_deleted_at_to_stok_transactions_tables', 1),
(21, '2026_05_20_000000_create_retur_produk_table', 1),
(22, '2026_05_20_170455_create__e_o_q_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` bigint UNSIGNED NOT NULL,
  `id_produk` bigint UNSIGNED DEFAULT NULL,
  `id_pengguna` bigint DEFAULT NULL,
  `jenis` enum('stok_minimum','stok_habis','kedaluwarsa') COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('belum_dibaca','dibaca') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_dibaca',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`id_notifikasi`, `id_produk`, `id_pengguna`, `jenis`, `pesan`, `status`, `created_at`) VALUES
(1, 3, 1, 'stok_minimum', 'Stok produk \"galian singset\" mendekati batas minimum (3)', 'belum_dibaca', '2026-05-21 08:14:04'),
(2, 3, 2, 'stok_minimum', 'Stok produk \"galian singset\" mendekati batas minimum (3)', 'belum_dibaca', '2026-05-21 08:14:04'),
(3, 3, 3, 'stok_minimum', 'Stok produk \"galian singset\" mendekati batas minimum (3)', 'belum_dibaca', '2026-05-21 08:14:04'),
(4, 2, 1, 'stok_minimum', 'Stok produk \"Empot-empot legit\" mendekati batas minimum (4)', 'belum_dibaca', '2026-05-21 09:14:18'),
(5, 2, 2, 'stok_minimum', 'Stok produk \"Empot-empot legit\" mendekati batas minimum (4)', 'belum_dibaca', '2026-05-21 09:14:18'),
(6, 2, 3, 'stok_minimum', 'Stok produk \"Empot-empot legit\" mendekati batas minimum (4)', 'belum_dibaca', '2026-05-21 09:14:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_distribusi`
--

CREATE TABLE `order_distribusi` (
  `id_order` bigint UNSIGNED NOT NULL,
  `id_distributor` bigint UNSIGNED DEFAULT NULL,
  `id_pengguna` bigint DEFAULT NULL,
  `nomor_order` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_order` date NOT NULL,
  `tanggal_diproses` date DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `order_distribusi`
--

INSERT INTO `order_distribusi` (`id_order`, `id_distributor`, `id_pengguna`, `nomor_order`, `tanggal_order`, `tanggal_diproses`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'ORD-2026-001', '2026-05-10', NULL, 'pending', 'Order pertama', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(2, 2, 3, 'ORD-2026-002', '2026-05-12', NULL, 'disetujui', 'Order kedua', '2026-05-21 07:45:41', '2026-05-21 07:45:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` bigint NOT NULL,
  `id_role` bigint DEFAULT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `id_role`, `nama_lengkap`, `username`, `password`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin Utama', 'admin', '$2y$12$3bDChVCtv/C4lgsl.Fxj2.Fbkpyim4zII17XCadvhg/NaKc03DlR.', 'admin@jamu.com', 'aktif', '2026-05-21 07:45:40', '2026-05-21 07:45:40'),
(2, 2, 'Staf Gudang', 'staf', '$2y$12$7mpHbw5KUO2rGnVIxjAJ5OBG6gsCx13ZPRBNI1OOmGWbar.DiMy8G', 'staf@jamu.com', 'aktif', '2026-05-21 07:45:40', '2026-05-21 07:45:40'),
(3, 3, 'Manajer Operasional', 'manager', '$2y$12$sLsyj5OlzwXMVYXDZOAk3e2/PXLVOh/nB9WEuo6AVJa8NBf0t.rF.', 'manager@jamu.com', 'aktif', '2026-05-21 07:45:40', '2026-05-21 07:45:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id_produk` bigint UNSIGNED NOT NULL,
  `id_kategori` bigint UNSIGNED DEFAULT NULL,
  `id_satuan` bigint UNSIGNED DEFAULT NULL,
  `kode_produk` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_produk` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `stok_terkini` int NOT NULL DEFAULT '0',
  `stok_minimum` int NOT NULL DEFAULT '0',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id_produk`, `id_kategori`, `id_satuan`, `kode_produk`, `nama_produk`, `harga_satuan`, `stok_terkini`, `stok_minimum`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'PRD001', 'Galian Rapat Wangi', 45000.00, 113, 87, 'mengurangi bau badan, mengurangi bau tidak sedap serta membuat rapet dan kesed area kewanitaan', '2026-05-21 07:45:41', '2026-05-21 08:19:22'),
(2, 2, 1, 'PRD002', 'Empot-empot legit', 93000.00, 4, 69, 'Mengurangi lendir berlebih, mengatasi keputihan serta menimbulkan sensasi denyut saat berhubungan', '2026-05-21 07:45:41', '2026-05-21 09:14:18'),
(3, 1, 1, 'PRD003', 'galian singset', 72000.00, 3, 56, 'mengurangi lemak dalam tubuh, menambah nafsu makan dan melancarkan BAB', '2026-05-21 07:45:41', '2026-05-21 08:14:04'),
(4, 1, 1, 'PRD004', 'jamu kecantikan', 85000.00, 200, 66, 'perawatan khusus remaja putri, mengurangi bau badan dan mengatasi keputihan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(5, 2, 2, 'PRD005', 'jamu galian montok', 30000.00, 971, 66, 'untuk menambah nafsu makan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(6, 3, 1, 'PRD006', 'jamu terlambat bulan (lancar darah)', 5000.00, 256, 52, 'mengurangi nyeri haid dan melancarkan peredaran darah sehingga lebih teratur', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(7, 2, 1, 'PRD007', 'jamu melancarkan asi (pejje)', 91000.00, 704, 68, 'melancarkan keluarnya ASI', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(8, 3, 1, 'PRD008', 'serbuk wasiat dan butiran delima', 95000.00, 823, 96, 'mengatasi keputihan dan lendir berlebih di area kewanitaan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(9, 1, 2, 'PRD009', 'parfum rempah', 90000.00, 389, 53, 'menyegarkan bau badan dengan aroma khas rempah khas', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(10, 1, 2, 'PRD010', 'cebokan rempah', 21000.00, 846, 60, 'mengatasi keputihan dan gatal akibat bakteri dan jamur', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(11, 2, 1, 'PRD011', 'pancuran nikmat', 5000.00, 801, 67, 'antiseptic kewanitaan tanpa dibilas', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(12, 1, 1, 'PRD012', 'sabun kesed', 31000.00, 462, 91, 'sabun khusus wanita untuk mengatasi keputihan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(13, 3, 3, 'PRD013', 'v-spa rempah', 81000.00, 30, 90, 'detoksifikasi kewanitaan agar bebas keputihan dan bau yang kurang sedap', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(14, 1, 1, 'PRD014', 'jamu bersalin', 76000.00, 856, 67, 'perawatan khusus pasca melahirkan, mengembalikan tubuh seperti semula, melancarkan ASI dan mencegah timbulnya varises', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(15, 2, 3, 'PRD015', 'paket perawatan pranikah', 55000.00, 312, 91, 'mengurangi bau badan, membuat kulit bersih dan lebih cerah', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(16, 2, 2, 'PRD016', '1 paket ramuan khusus wanita', 40000.00, 706, 53, 'perawatan khusus wanita untuk menjaga tubuh tetap prima dan hubungan suami istri makin mesra', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(17, 3, 1, 'PRD017', 'jamu penyubur kandungan', 94000.00, 345, 91, 'menyuburkan kandungan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(18, 3, 2, 'PRD018', 'cemceman rambut', 11000.00, 546, 81, 'mengatasi ketombe dan kerontokan rambut', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(19, 2, 2, 'PRD019', 'minyak bulus', 28000.00, 491, 74, 'mengencangkan kulit dan area khusus baik pria maupun wanita', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(20, 3, 3, 'PRD020', 'minyak zaitun', 34000.00, 320, 87, '', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(21, 1, 1, 'PRD021', 'lulur rempah', 8000.00, 758, 98, 'membuat kulit bersih serta tampak lebih cerah', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(22, 1, 2, 'PRD022', 'bedak dingin', 50000.00, 962, 61, 'masker bengkoang untuk mencerahkan wajah, memudarkan bekas jerawat dan mengecilkan pori', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(23, 3, 1, 'PRD023', 'pegal linu', 75000.00, 141, 51, 'mengurangi capek, nyeri sendi dan melancarkan peredaran darah', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(24, 3, 1, 'PRD024', 'jamu pembersih darah', 21000.00, 844, 94, 'mengatasi gatal gatal dan alergi pada kulit', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(25, 3, 1, 'PRD025', 'jamu kencing manis', 43000.00, 805, 97, 'menurunkan kadar gula dalam darah', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(26, 3, 3, 'PRD026', 'bumbu minuman pokak', 32000.00, 704, 72, 'minuman herbal dari bahan jahe emprit', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(27, 3, 1, 'PRD027', '1 paket jamu suami istri', 8000.00, 227, 79, 'mengharmoniskan hubungan suami istri', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(28, 3, 3, 'PRD028', 'Jamu ma\'jun super (plus madu)', 60000.00, 468, 66, 'menambah stamina, menambah masa otot dan melancarkan peredaran darah', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(29, 2, 2, 'PRD029', 'jamu jantan super', 98000.00, 78, 85, 'menjaga stamina tubuh agar tetap fit', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(30, 1, 3, 'PRD030', 'jamu aktivitas', 10000.00, 792, 73, 'penambah stamina khusus pekerja berat. Suplemen khusus untuk menambah stamina pria', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(31, 2, 2, 'PRD031', 'jamu darah tinggi', 60000.00, 251, 89, 'menurunkan tekanan darah tinggi', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(32, 1, 3, 'PRD032', '1 paket ramuan khusus pria', 73000.00, 297, 68, 'suplemen khusus bagi pria untuk menjaga stamina dan kesehatan tubuh', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(33, 3, 3, 'PRD033', 'Jamu Tradisional Madura', 41000.00, 889, 53, 'Buat Kesehatan, Bau Badan, Keputihan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(34, 1, 2, 'PRD034', 'Bom', 51000.00, 75, 79, 'Untuk kuat berhubungan, dan Pegal - pegal', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(35, 2, 3, 'PRD035', 'Jamu Hitam', 36000.00, 719, 61, 'Untuk segala macam penyakit', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(36, 1, 3, 'PRD036', 'Jamu Pelintiran darah tinggi', 70000.00, 968, 64, 'Menurunkan tekanan darah tinggi, menetralisir sirkulasi jantung, Menormalkan sirkulasi jantung', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(37, 1, 1, 'PRD037', 'Empot-empot Super', 74000.00, 408, 80, '1.Memulihkan elastisitas organ intim wanita; 2. Mengencangkan otot² miss V; 3. Mengencangkan payudara; 4. Mengencangkan seluruh kulit shg tampak lebih muda; 5. Menambah gairah dan semangat dalam berhubungan suami istri; 6. Menghilangkan gatal² di miss V', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(38, 3, 2, 'PRD038', 'Jamu selokarang', 93000.00, 662, 62, 'Menurunkan pusing, panas dalam sakit gigi tenggorokan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(39, 3, 2, 'PRD039', 'Jamu Bengkes', 14000.00, 186, 76, 'Awet Muda Wajah Berseri', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(40, 3, 3, 'PRD040', 'Empot Ayam Super', 30000.00, 509, 88, '1.Melegitkan dan menghaluskan vagina; 2. Mengencangkan otot kendor setelah melahirkan; 3. Menghilangkan bau serta gatal²; 4. Mengatasi masalah² ibu yg ikut bermacam² KB; 5. Utk wanita frigid dan suami istri selalu harmonis; 6. Membuat awet muda dan cantik', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(41, 3, 1, 'PRD041', 'Jamu Delima Putih', 45000.00, 631, 63, 'Mengatasi keputihan/pektay', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(42, 2, 3, 'PRD042', 'Sariawan Sekalor', 15000.00, 592, 94, 'Menyembuhkan radang gusi, radang tenggorokan, sakit gigi, sakit kepala, amandel, ambeyen, demam,kolesterol dan mendetox darah.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(43, 2, 3, 'PRD043', 'Galian Singset ( Susut Perut)', 50000.00, 42, 57, 'Melangsingkan badan, mengecilkan dan mengencangkan perut kendur,memadatkan tubuh,melancarkan BAB, membuat wajah segar dan tidak mudah keriput, menghilangkan selulit.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(44, 2, 1, 'PRD044', 'Sehat Laki-laki Perkasa', 94000.00, 76, 51, 'Menambah stamina dan juga menambah hormon, Menghilangkan capek/letih/encok, Menjaga tubuh tetap perkasa, Tidak mudah masuk angin, Mengentalkan sperma, Mencegah ejakulasi dini.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(45, 1, 1, 'PRD045', 'Sari Rapet Empot-Empot', 100000.00, 657, 86, 'Mengharumkan bagian kewanitaan, Menyembuhkan keputihan/gatal disebabkan bakteri, Merapatkan vagina,Mengencangkan otot rahim, Menambah keharmonisan rumah tangga.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(46, 1, 3, 'PRD046', 'Pluntur(Pelancar Darah)', 74000.00, 944, 70, 'Melancarkan darah, menghilangkan pegel linu, menjaga stamina tubuh, mengurangi angka kelahiran.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(47, 3, 3, 'PRD047', 'Strong', 81000.00, 486, 97, 'Menambah stamina tubuh, Menghilangkan capek/letih/encok, Menjaga tubuh tetap perkasa,  Mengentalkan sperma, Mencegah ejakulasi dini, Tahan lama.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(48, 3, 1, 'PRD048', 'Seger Montok', 36000.00, 116, 64, 'Mengencangkan dan memontokkan payudara, Memadatkan tubuh, Mencerahkan wajah, Memberi nafsu makan, Menyehatkan badan.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(49, 1, 1, 'PRD049', 'Rapet Wangi Spesial', 10000.00, 336, 70, 'Mengharumkan bagian kewanitaan, Menyembuhkan keputihan/Gatal disebabkan bakteri, merapatkan vagina, Mengencangkan otot rahim, Menambah keharmonisan rumah tangga.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(50, 3, 3, 'PRD050', 'Tolak Angin', 67000.00, 773, 90, 'Menyembuhkan demam,pusing, perut mual, kembung, tenggorokan kering dan menambah daya tahan tubuh.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(51, 1, 2, 'PRD051', 'Subur Kandungan', 56000.00, 954, 73, 'Diramu dari bahan jamu berupa tumbuh-tumbuhan dan akar yang berkhasiat tinggi untuk membantu menyehatkan, menyuburkan dan menguatkan kandungan. Jamu ini sangat baik bagi ibu-ibu yang sering keguguran dan sulit mendapatkan kehamilan.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(52, 2, 1, 'PRD052', 'Sehat Wanita', 36000.00, 444, 73, '', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(53, 3, 3, 'PRD053', 'Sehat Laki-laki ( Helbeh)', 19000.00, 196, 64, '', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(54, 1, 2, 'PRD054', 'Natural Lulur Sari temugiring', 61000.00, 296, 53, 'Menghaluskan dan mencerahkan kulit, melembabkan, menghilangkan bekas noda di tubuh.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(55, 2, 2, 'PRD055', 'Empot Sari Rapet', 9000.00, 991, 65, 'mengembalikan masa keperawanan dan menambah keharmonisan dalam rumah tangga, memadatkan tubuh dan membuat badan lebih segar.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(56, 3, 1, 'PRD056', 'Awet Muda', 99000.00, 395, 63, 'membantu memelihara kecantikan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(57, 3, 3, 'PRD057', 'Ma\'jun', 7000.00, 42, 68, 'menyembuhkan sakit pinggang, pegel linu, encok dan rematik, menghilangkan rasa lelah sehingga menambhan setamina dan syahwat', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(58, 1, 2, 'PRD058', 'Sale Karang', 87000.00, 872, 63, 'sakit gigi, gusi bengkak dan gusi berdarah', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(59, 1, 2, 'PRD059', 'Ibu Hamil', 31000.00, 465, 55, 'biar tidak mudah capek', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(60, 1, 1, 'PRD060', 'Diabet', 94000.00, 929, 84, 'menstabilkan kadar gula', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(61, 2, 3, 'PRD061', 'Nafsu Makan', 41000.00, 362, 76, 'Untuk menambah nafsu makan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(62, 3, 2, 'PRD062', 'Sehat Pria', 6000.00, 227, 73, 'Untuk menambah tenaga lelaki', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(63, 3, 2, 'PRD063', 'Gatal-gatal', 41000.00, 248, 59, 'untuk menyembuhkan gatal-gatar dan alergi', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(64, 3, 1, 'PRD064', 'Ibu Melahirkan', 87000.00, 754, 94, 'memperlancar ASI', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(65, 1, 3, 'PRD065', 'Jamu Sakit Pinggang', 74000.00, 624, 86, 'meredakan nyeri pinggang dan menambah stamina', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(66, 1, 2, 'PRD066', 'Jaselang', 24000.00, 156, 91, 'Menambah stamina, pegal linu, capek-capek, mengahangatkan badan dll', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(67, 1, 3, 'PRD067', 'Macho', 22000.00, 754, 65, 'Menambah stamina dan ereksi, meningkatkan libido, mengobati pegal linu, capek-capek, dan menghangatkan badan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(68, 1, 3, 'PRD068', 'Kusir', 22000.00, 209, 54, 'menghilangkan bau badan, sakit kepala dll', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(69, 2, 1, 'PRD069', 'Sarpet Manjakani', 7000.00, 220, 75, 'mencegah keputihan, menghilangkan bau  badan, meraptkan vagina dll', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(70, 1, 3, 'PRD070', 'Jamu Sondhep', 18000.00, 346, 83, 'meredakan nyeri otot bagian bahu, otot piggang, dan memperlancar peredaran darah', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(71, 2, 1, 'PRD071', 'Daun sirih', 13000.00, 948, 78, 'menghilangkan bau badan, mengobati sariawan, menghilangkan mau bulut, mencegah keropos pada gigi dan tulang', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(72, 3, 1, 'PRD072', 'Kuda Hitam', 37000.00, 183, 57, 'Untuk meningkatkan kekuatan pada pria', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(73, 1, 3, 'PRD073', 'Jahe Merah Plus', 91000.00, 634, 89, 'Untuk menambah stamina tubuh', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(74, 2, 1, 'PRD074', 'ekstrak jahe', 99000.00, 88, 94, 'untuk mengobati gejala masuk angin', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(75, 1, 2, 'PRD075', 'ekstrak temulawak', 53000.00, 895, 54, 'untuk mengobati penyakit asam lambung dan menambah nafsu makan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(76, 3, 1, 'PRD076', 'ektrak kunyit', 46000.00, 697, 50, 'untuk mengobati penyakit asam lambung', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(77, 1, 2, 'PRD077', 'kopi lake\'', 25000.00, 172, 89, 'Untuk meningkatkan kekuatan pada pria', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(78, 2, 2, 'PRD078', 'wedang jahe merah', 73000.00, 733, 100, 'Menambah stamina tubuh', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(79, 1, 1, 'PRD079', 'kunyit asam madu', 11000.00, 988, 93, 'untuk merawat kesehatan lambung, dan mengobati panas dalam', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(80, 1, 2, 'PRD080', 'manjakani', 22000.00, 832, 62, 'untuk merawat kesehatan wanita seperti mempelancar haid dan keputihan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(81, 1, 2, 'PRD081', 'kuda hitam cair', 12000.00, 231, 73, 'Untuk meningkatkan kekuatan pada pria', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(82, 2, 2, 'PRD082', 'ETABARI', 46000.00, 248, 63, 'untuk penderita kencing manis(diabetes)', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(83, 3, 1, 'PRD083', 'TOLAK AMBEYEN', 96000.00, 565, 52, 'Menyembuhkan ambeyen baru atau sudah lama, menjaga kesehatan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(84, 2, 1, 'PRD084', 'TOLAK BATUK/ASMA', 88000.00, 876, 68, 'membantu mengobati batuk yang masih baru maupun yang sudah lama, membantu mengobati penyakit alergi pernapasan bagian atas dan penyakit asma, membantu menurunkan panas demam disebabkan flu dan radang tenggorokan.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(85, 3, 2, 'PRD085', 'Susut perut Tresna', 57000.00, 879, 69, 'membantu mengurangi lemak tubuh terutama di bagian perut dan membantu menurunkan berat badan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(86, 2, 3, 'PRD086', 'PANYAMAN EMPOT-EMPOT', 49000.00, 106, 63, 'membantu merapatkan organ kewanitaan, mengencangkan otot-otot rahim dan sekitarnya, vagina menjadi keset dan sensitif, membantu para wanita agar lebih cepat mencapai orgasme, memberi getaran-getaran apabila ada sentuhan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(87, 3, 3, 'PRD087', 'masker v', 8000.00, 945, 100, 'membantu para wanita mengatasi frigit dan agar mempunyai gairah melayani pasangan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(88, 1, 1, 'PRD088', 'LAMSAM', 14000.00, 193, 71, 'untuk menurunkan/menormalkan tekanan darah tinggi(hipertensi), dan melancarkan peredangan darah dalam tubuh', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(89, 3, 2, 'PRD089', 'lulur wangi', 58000.00, 713, 72, 'untuk mengatasi jerawat dan mengangkat komedo', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(90, 3, 1, 'PRD090', 'PETODHU', 83000.00, 40, 72, 'untuk kesehatan tulang untuk pria dan wanita', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(91, 1, 1, 'PRD091', 'GALIAN PUTRI', 63000.00, 478, 89, 'menjaga agar tubuh tetap segar, sehat, lincah dan tidak pucat, mengatasi bau ketiak dan bau badan yang kurang sedap, membantu mengatasu penyakur keputihan, menjaga organ kewanitaan sehingga pakaian dalan tetap bersih dari noa\\da, melancarkan haid', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(92, 1, 2, 'PRD092', 'GALIAN RAPET', 7000.00, 223, 80, 'membantu merapatkan organ kewanitaan, menjaga keharmonisan rumah tangga, menghilangkan bau tidak sedap, membantu mengatasi keputihan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(93, 1, 3, 'PRD093', 'lulur ayu', 18000.00, 81, 83, 'memelihara dan kecantikan wajah dan anggota badan, membersihkan dan menghaluskan kulit', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(94, 2, 1, 'PRD094', 'PAS JUDHU', 10000.00, 523, 91, 'membantu mengatasi asam urat, kolestrol, nyeri reumatik', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(95, 1, 2, 'PRD095', 'SEHAT L.M', 33000.00, 124, 55, 'memperlancar peredaran darah, menambah tenaga serta meningkatkan daya tahan tubuh terhadap penyakut, membantu mengatasi gejala penuaan, nyeri otot,nyeri sendi,pegal-pegal, rabun mata, encegah masuk angin, pusing dan maag', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(96, 2, 1, 'PRD096', 'cream montok', 21000.00, 846, 62, 'membantu membesarkan payudara', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(97, 2, 2, 'PRD097', 'GALIAN MONTOK', 11000.00, 733, 93, 'membantu mengobati buah dada yang kecil supaya jadi montok dan besar, buah dada menjadi padat berisi', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(98, 3, 2, 'PRD098', 'BERSIH JERAWAT', 53000.00, 868, 79, 'membantu mengatasi / membersihkan jerawat, mencegah timbulnya jerawat, memelihara wajah agar tetap halus', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(99, 3, 3, 'PRD099', 'AWET AYU', 43000.00, 830, 56, 'mengandung formula \'anti aging\' yang berguna untuk menghambat proses penuaan seperti mengurangi keriput, memudarkan lingkaran hitam di mata dan dapat mengurangi kantung mata, membantu mengatasi penyakit keputihan, menghilangkan bau badan, merapatkan vagina, menjaga organ kewanitaan tetap bersih, mengurangi dan menghilangkan rasa sakit serta menjelang haid', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(100, 3, 1, 'PRD100', 'PANYAMAN', 8000.00, 689, 79, 'untuk mnegatasi kurang gairah, mengencangkan otot-otot rahim dan sekitarnya, vagina menjadi keset dan sensitif, membantu para wanita agar lebih cepat mencapai orgasme, memberi getaran-getaran apabila ada sentuhan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(101, 1, 2, 'PRD101', 'JAMU PERKASA', 81000.00, 324, 88, 'mencegah ejakulasi dini, menambah daya tahan lama pria, menambah tenaga dan gairah keperkasaan pria, menambah kesehatan dan kebugaran', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(102, 2, 1, 'PRD102', 'peluntur &pelangsing', 68000.00, 402, 80, 'mengurangi lemak dalam tubuh terutama di perut yang gendut, mengencangkan otot-otot yang kendor, memelihara tubuh agar tetap langsing padat dan ideal secara alami', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(103, 3, 2, 'PRD103', 'BEDAK SARI', 50000.00, 111, 81, 'pengharum tubuh', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(104, 1, 1, 'PRD104', 'HABBATUS SAUDA', 24000.00, 875, 96, 'menjaga stamina, memperkuat daya kosentrasi, anti bakteri, mengatasi infeksi, menghangatkan dan melegakan saluran pernapasan, meringankan gejala batuk pilek, menurunkan demam, membantu mempercepat penyembuhan penyakit, membantu mengatasu maag, mengeluarkan masuk angin dan kembung', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(105, 1, 3, 'PRD105', 'SELEDRI MIX', 26000.00, 577, 73, 'untuk mengatasi asam urat', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(106, 3, 2, 'PRD106', 'KUNYIT ASEM', 48000.00, 139, 65, 'meningkatkan data tahan tubuh, menjaga stamina, menghilangkan bau badan, melancarkan datang bulan, menghaluskan kulit, mencegah penuaan dini, mencegah diare', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(107, 2, 2, 'PRD107', 'TEMU LAWAK', 75000.00, 735, 94, 'mengatasi maag, mengatasi radang sendi, menghilangkan bau badan, mengatasi diare, mengatasi kolesterol, melancarkan pencernaan, mencegah penuaan dini, anti kanker, menyehatkan liver dan ginjal', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(108, 2, 3, 'PRD108', 'BERAS KENCUR', 16000.00, 579, 72, 'mengatasi batuk pilek, meredakan hidung tersumbat, menambah nafsu makan, mengatasi sakit maag, meningkatkan imun tubuh', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(109, 1, 2, 'PRD109', 'SIRIH PINANG', 97000.00, 206, 83, 'menjaga kesehatan gigi, mencegah sariawan, menghilangkan bau mulut, menghilangkan bau badan, mengatasi keputihan, melancarkan pencernaan, mencegah gusi berdarah', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(110, 2, 2, 'PRD110', 'POKA\'', 94000.00, 770, 67, 'mengatasi masuk angin,mual, pusing, meredakan nyeri sendi, meningkatkan imun tubuh, mencegah penuaan dini, anti kanker, mencegah kolesterol', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(111, 2, 2, 'PRD111', 'BUNGA TELANG', 90000.00, 338, 65, 'menyehatkan mata, menyehatkan otak, menghaluskan kulit, melancarkan pencernaan, mencegah penuaan dini', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(112, 1, 3, 'PRD112', 'jahe merah instant', 9000.00, 528, 57, 'untuk mengobati masuk angin, pusing,dan mual', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(113, 3, 2, 'PRD113', 'Empot wangi', 78000.00, 122, 95, 'membantu mengurangi lendir yang berlebihan pada daerah kewanitaan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(114, 1, 1, 'PRD114', 'Galian singset ++', 85000.00, 382, 57, 'mengurangi kelebihan lemak (kolestrol) dalam tubuh, mengecilkan perut, menjaga bentuk tubuh yang ideal, menjaga penampilan selalu awet muda, membantu mengurangi lemak di tubuh, melangsingkan badan, membuat tampil seksi dan percaya diri, menormalkan BAB yang tidak lancar', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(115, 1, 3, 'PRD115', 'asi booster', 59000.00, 145, 60, 'melancarkan asi, menyegarkan badan muka tidak pucat, menyehatkan ibu dan bayi yang dilahirkan, menuntaskan sisa darah, meningkatkan kualitas nutrisi dalam asi', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(116, 2, 3, 'PRD116', 'gadis remaja', 65000.00, 333, 75, 'menghilangkan bau badan yang kurang sedap, mencegah tumbuhnya jerawat, menyembuhkan keputihan, menjaga bentuk tubuh tetap ideal', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(117, 3, 3, 'PRD117', 'empot-empot', 88000.00, 245, 52, 'menambah kemesraan dan keharmonisan keluarga, kembali muda bagaikan gadis remaja, membuat suami makin sayang sama istri, kenikmatan dan kepuasan selalu menjelma', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(118, 1, 3, 'PRD118', 'sehat lelaki', 80000.00, 674, 76, 'menambah semangat stamina dan kesehatan, menguatkan dan menghangatkan badan, pegal linu otot kaku dan lesu', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(119, 2, 3, 'PRD119', 'rapet harum asmara', 40000.00, 323, 54, 'merapatkan dan melenturkan miss v, mengatasi bau tak sedap, serta gatal-gatal disekitar organ intim, mengatasi cairan berlebih, mengaharumkan area vagina, menjaga keharmonisan hubungan suami istri, menyembuhkan keputihan, serta gejalanya', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(120, 2, 2, 'PRD120', 'feminia', 64000.00, 253, 86, 'menjaga miss v tetap keset elastis, menambah libido, menghilangkan bau badann dan miss v yang kurang sedap, menyembuhkan keputihan, menjaga payudara tetap kenyal dan montok, melindungi rahim dari berbagai virus, menormalkan hormon estrogen, menghangatkan badan, mengurangi pegel-pegel/linu', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(121, 2, 3, 'PRD121', 'jakuat', 71000.00, 257, 67, 'memperbaiki stamina pria, meningkatkan kebugaran tubuh dalam hubungan pasutri, membantu mengatasi ejakulasi dini, mengentalkan sperma, menambah kejantanan pria', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(122, 1, 2, 'PRD122', 'sirih wangi', 21000.00, 844, 97, 'menghilangkan bau badan dan mulut, menyembuhkan radang tenggorokan dan gusi, mengatasi keputihan, menyembuhkan jerawat, menghaluskan kulit, melancarkan air seni, penambah tenaga/ stamina, mengurangi keringat berlebih, mengatasi penyakit kulit/gatal-gatal', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(123, 1, 1, 'PRD123', 'Rapet Wangi', 69000.00, 561, 91, 'membantu mengurangi lendir yang berlebihan pada daerah kewanitaan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(124, 2, 3, 'PRD124', 'bersih darah', 63000.00, 781, 75, 'melancarkan peredaran darah di dalam tubuh, membersihkan darah kotor yang ada di dalam tubuh, mengatasi gatal-gatal pada kulit, mengatasi / meringankan bisul, mengobati korengan dan beberapa masalah kulit lainnya, menyembuhkan serta mencegah jerawat', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(125, 3, 1, 'PRD125', 'kencing manis', 26000.00, 216, 73, 'mengobati kencing manis, menurunkan kadar gula seperti sering merasa haus, sering kencing terutama dimalam hari', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(126, 3, 3, 'PRD126', 'sari rapat', 85000.00, 756, 55, 'menambah gairah hubungan suami istri, mengobati keputihan dan gejalanya seperti berair, berlendir, berbau, merapatkan otot-otot vagina', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(127, 2, 2, 'PRD127', 'delima putih', 13000.00, 284, 92, 'mencegah dan mengobati keputihan seperti muka pucat tidak bergairah sakit pinggang mengurangi lendir serta menghilangkan bau (keringat) kurang sedap, merapatkan vagina pemakaian secara teratur maka wajah bercahaya cantik & awet muda', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(128, 1, 1, 'PRD128', 'empot-empot love', 59000.00, 190, 84, 'mengencangkan organ intim (rapet), mengencangkan kulit, menambah libido, atasi payudaya kendur, membersihkan bakteri dan jamur (keputihan), mengobati kista, mencegah kanker payudaram menguatkan pinggang, melancarkan datang bulan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(129, 2, 1, 'PRD129', 'gemuk sehat', 17000.00, 794, 86, 'menambah nafsu makan, menghambat pertumbuhan sek kanker, meningkatkan daya tahan tubuh, menjaga kesehatan liver, memperbaiki jaringan tubuh yang rusak, membantu melawan bakteri dan jamur di tubuh, menyembuhkan penyakit kuning', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(130, 3, 3, 'PRD130', 'oil mahabbah', 33000.00, 342, 60, 'agar payudaya tetap kencang dan motok setelah melahirkan, merawat kesehatan payudara, menghaluskan kulit payudara', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(131, 2, 2, 'PRD131', 'bubuk herbal ajaib', 56000.00, 616, 81, 'merapatkan dan mengencangkan otot vagina, mengeringkan miss v yang basah, sama-sama meberikan kenikmatan dan kebahagiaan hubungan suami istri', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(132, 3, 1, 'PRD132', 'butiran putri delima', 66000.00, 405, 52, 'merapatkan dan mengencangkan otot vagina, keset/kering bikin wanita percaya diri, sama-sama meberikan kenikmatan dan kebahagiaan hubungan suami-istri', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(133, 3, 1, 'PRD133', 'penyubur kandungan', 12000.00, 803, 84, 'menguatkan rahim, menambah hormon menyuburkan kandungan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(134, 1, 1, 'PRD134', 'setanggi zahra', 34000.00, 748, 54, 'bermanfaat sebagai aromatherapy menenangkan  hati dan menambah keintiman suami istri', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(135, 3, 2, 'PRD135', 'timung / mandi uap', 64000.00, 860, 67, 'melancarkan peredaran darah, mengeluarkan keringat berlebih, membakar lemak, mengharumkan badan, membuat badan segar, harum dan berseri', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(136, 3, 1, 'PRD136', 'hormolak', 10000.00, 657, 100, 'menstabilkan hormon laki-laki yang sangat diperlukan untuk produksi sperma, memberikan suplement tambahan sehingga produksi sperma dapat lebih maksimal, mempercepat produksi sperma dan memperbanyak jumlah cairan sprema, menghangatkan badan, mengurangi ejakulasi dini', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(137, 2, 3, 'PRD137', 'lintah hitam papua', 84000.00, 603, 79, 'melancarkan sirkulasi darah, mengencangkan otot-otot yang kendor, meningkatkan kajantanan pria, mengobati lemah syahwat, ejakulasi dini, memperbesar dan memperpanjang penis.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(138, 1, 1, 'PRD138', 'jamu gemuk sehat', 20000.00, 44, 93, 'membantu memperbaiki nafsu makan, menambah berat badan, menyelaraskan alat-alat pencernaan tubuh, menghilangkan letih, lesu, lemah dan kurang bersemangat', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(139, 3, 2, 'PRD139', 'jamu sepet madura *empot-empot)', 77000.00, 891, 53, 'membantu mengurangi lendir yang berlebihan pada daerah kewanitaan, memperkuat otot-otot vagina untuk membuat denyut-denyut istimewa pada vagina, mengeraskan otot vagina, menghilangkan keputihan dan gatal-gatal', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(140, 1, 3, 'PRD140', 'PLUNTUR(TERLAMBAT BULAN)', 56000.00, 328, 92, 'sangat efektif untuk wanita yang sering terlambat bulan, datang bulan tidak teratur, sakit badan pada waktu datang bulan (dianjurkan untuk keterlambat) yang tidak lebih dari tiga bulan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(141, 3, 2, 'PRD141', 'Lulur Bengkoang', 68000.00, 742, 71, 'mengangkat sel-sel kulit mati, menghilangkan jerawat, flex hitam juga memutihkan kulit', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(142, 2, 2, 'PRD142', 'pintu surga', 21000.00, 430, 93, 'bermanfaat untuk perawatan kewanitaan dan juga menuntaskan keputihan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(143, 2, 2, 'PRD143', 'jamu manjakani', 90000.00, 939, 56, 'merapetkan vagina, bikin keset untuk keputihan dan juga menyehatkan badan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(144, 1, 2, 'PRD144', 'air cewok', 23000.00, 399, 54, 'bikin keset, merapetkan, menghilangkan keputihan juga menghilangkan bau daerah v', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(145, 1, 3, 'PRD145', 'jamu melahirkan', 38000.00, 298, 82, 'ibu dan bayi menjadi sehat dan bugar', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(146, 3, 3, 'PRD146', 'salekarang', 59000.00, 193, 54, 'untuk flu, batuk, sakit gigi, pusing', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(147, 2, 1, 'PRD147', 'Kopi racikan madura', 94000.00, 865, 52, 'Menjaga organ hati, membantu membersihkan saluran pencernaan, membantu membakar lemak, mencegah penyakit jantung, diabetes', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(148, 3, 1, 'PRD148', 'dupa kemanten', 24000.00, 547, 68, 'mengharumkan tubuh, baju, juga ruangan. Harumnya lengket sepanjang hari', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(149, 1, 1, 'PRD149', 'jamu jerawat', 88000.00, 288, 92, 'membantu mengatasi jerawat, menjaga kulit tetap halus bercahaya', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(150, 1, 3, 'PRD150', 'jamu pelangsing(bobot idaman)', 10000.00, 198, 54, 'menurunkan berat badan sesuai dengan idaman, menahan bobot badan supaya tidak gemuk berlebihan, mengontrol kadar lemak bagian perut', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(151, 2, 3, 'PRD151', 'jamu pegel linu', 97000.00, 781, 55, 'sangat berkhasiat menyembuhkan pegel-pegel linu otot-otot anda, menyembuhkan penyakit rheumatic/encok dan sakit pinggang', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(152, 1, 3, 'PRD152', 'jamu nifas (pluntur)', 13000.00, 423, 98, 'sangat efektif untuk wanita yang sering terlambat bulan, datang bulan tidak teratur, sakit-sakit badan waktu datang bulan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(153, 1, 1, 'PRD153', 'sehat lelaki (perkasa lelaki)', 88000.00, 877, 98, 'Memperkuat fungsi lelaki serta meningkatkan katahanan kejantanan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(154, 3, 2, 'PRD154', 'sepet madura(empot-empotan', 88000.00, 683, 68, 'membuat denyut-denyut istimewa yang khas, memperkuat otot-otot kewanitaan, menghilangkan bau tidak enak gatal-gatal dan keputihan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(155, 2, 3, 'PRD155', 'sehat perempuan(montok payudaya)', 76000.00, 469, 56, 'mengencangkan payudara, memperbesar payudara yang kurang ideal, menyehatkan dan memperindah payudara', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(156, 2, 1, 'PRD156', 'sari harum(keputihan)', 36000.00, 707, 77, 'sangat mujarab dan benar-benar ampuh untuk segala macam keputihan yang lama maupun yang baru. Menghilangkan bau yang tidak enak, gatal-gatal dll', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(157, 1, 1, 'PRD157', 'SUPER SEHAT BURUNG WALET', 52000.00, 913, 82, 'Insya Allah sangat ampuh untuk mencegah dan menyembuhkan Reumatik, Asam urat, Tekanan darah tinggi, Asma, Maag, Ginjal, Tipes, Cacingan, Sakit gigi, Lemah syahwat, Gatal-gatal alergi, Paru-paru basah, Kurang nafsu makan, Melancarkan Sirkulasi darah, Jerawat, Bisul, Kudis, Panu, Merawat Ms.V, Mengatasi keputihan, Merawat dan mengencangkan kulit, Mencegah dan mengatasi penyakit menahun, Kelumpuhan/badan mati separuh seperti Stroke, Kanker, Liver, Kencing manis basah/kencing manis kering, Menjaga dan menambah daya tahan tubuh.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(158, 1, 1, 'PRD158', 'SUPER KUAT BURUNG WALET', 25000.00, 326, 92, 'Insyaallah sangat ampuh untuk menambah kepuasan Sex, Tahan keluarnya air sperma, menambah kejantanan, mengencangkan otot dzakar, menguatkan dan mengobati impoten yang menahun, merangsang dan mempercepat hidupnya dzakar, menambah energi baru setelah berhubungan, mengganti sel-sel tubuh yang aus, mengobati lesu, loyo, sakit pinggang, sakit kepala, dan menambah daya tahan tubuh dan menambah vitalitas pria dan wanita.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(159, 3, 1, 'PRD159', 'KOPI JANTAN ANTI DIABETES', 89000.00, 549, 53, 'Insya Allah sangat ampuh untuk mengatasi penyakit diabetes, lemah syahwat, ejakulasi dini, meningkatkan libido, menambah stamina, vitalitas dan meningkatkan daya tahan tubuh pria dan wanita.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(160, 3, 3, 'PRD160', 'MADU Ruqyah', 55000.00, 528, 53, 'Insya Allah dapat mencegah dan Menyembuhkan segala macam penyakit: Diabetes, Kolesterol, Asam Urat, Asam Lambung, Alergi, Ginjal, Asthma, Maag, Darah Tinggi, Lever, Kanker, Kusta, Stroke, Lumpuh, Keputihan, Meningkatkan gairah sex pria/wanita, kesulitan mendapatkan keturunan, Gangguan Jin/Psikis, dan kena Sihir/Santet', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(161, 2, 3, 'PRD161', 'SUSU SUPER SEHAT GOLD', 47000.00, 788, 94, 'Mengatasi kencing manis basah/kering, Diabetes, Asam urat, Liver, Kanker, Mudah lesu/loyo/letih/kurang semangat atau kurang energik, Merawat dan memperkuat otot Mr.P, Menambah stamina pria dan wanita, Merawat dan mengencangkan Ms V, Mengatasi keputihan, Lendir berlebih pada Ms,V, Bau badan tidak sedap, Kurang darah, Jerawat, bisul, kudis dan sejenisnya, Merawat, mengencangkan dan mengatasi kulit mati/ mudah kering, Menambah nafsu makan, baik untuk dewasa ataupun bagi anak-anak(usia minimal 5 tahun), Menambah berat badan dengan menambah nafsu makan, DII, intinya MULTI KHAS', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(162, 2, 1, 'PRD162', 'SABUN CAIR SIRIH', 65000.00, 201, 62, 'harum dan keset pembersih daerah khusus kewanitaan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(163, 2, 3, 'PRD163', 'krim rondo ayu', 36000.00, 138, 96, 'Khasiat dapat mengencangkan payudara yang turun. Disarankan diminum dengan kapsul montok payudara untuk hasil yang maksimal.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(164, 1, 2, 'PRD164', 'harum rempah spray(perfume)', 76000.00, 512, 96, 'Dapat menghilangkan bau badan. Bisa digunakan untuk ketiak, leher, kaki, dan rambut.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(165, 1, 2, 'PRD165', 'virginity spray', 79000.00, 739, 62, 'dapat membuat wangi bagian bawah', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(166, 2, 2, 'PRD166', 'empot-empot ayam super', 32000.00, 633, 82, 'Menggairahkan kembali hubungan suami istri, Menyerap kelebihan lendir (keputihan), Mencegah dan menghilangkan bau tidak sedap pada bagian kewanitaan dan menghilangkan bau badan, Mengencangkan kembali otot² kewanitaan setelah melahirkan, Mengembalikan kegadisan / keperawanan, Tidak dianjurkan diminum ketika sedang masa haid/masa nifas', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(167, 3, 1, 'PRD167', 'galian rapet awet muda', 29000.00, 13, 50, 'Menggairahkan kembali hubungan suami istri, Menyerap kelebihan lendir (keputihan), Mencegah dan menghilangkan bau tidak sedap pada bagian kewanitaan dan menghilangkan bau badan, Dapat dikonsumsi oleh gadis / remaja, Mengembalikan kegadisan / keperawanan, Tidak dianjurkan diminum ketika sedang masa haid/masa nifas', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(168, 2, 2, 'PRD168', 'serbuk nikmat surga', 47000.00, 25, 90, 'Menambah rasa kenikmatan dalam bersetubuh, Menggairahkan kembali hubungan suami istri, Menyerap kelebihan lendir (keputihan), Menghilangkan bau tidak sedap pada bagian kewanitaan, Tidak dianjurkan diminum ketika sedang masa haid/masa nifas', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(169, 2, 1, 'PRD169', 'Bedak madu', 96000.00, 272, 98, 'Mengangkat sel² kulit mati, Mencegah timbulnya jerawat, Menghilangkan flek hitam di wajah, Menghilangkan mata panda, menghilangkan biang keringat', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(170, 3, 1, 'PRD170', 'Montok Payudara', 35000.00, 679, 57, 'Membuat tubuh montok dan berisi, Melangsingkan dan menyingsetkan tubuh, Mengencangkan payudara, Menghilangkan pegal² dan kecapean, Menambah semangat kerja, Menggantikan sel² kulit mati dengan segera, Kulit menjadi halus dan berseri', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(171, 2, 2, 'PRD171', 'Peluntur Lemak', 20000.00, 555, 79, 'Melarutkan lemak dalam tubuh, Mengurangi nafsu makan, Menyusutkan lemak perut, Menurunkan kolesterol, Melancarkan BAB', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(172, 2, 1, 'PRD172', 'empot-empotan', 28000.00, 950, 72, 'Mengobati keputihan, Menyehatkan / menyingsetkan badan, Menambah gairah hubungan suami-istri, Mengobati gatal² dan menghilangkan bau badan dan bau mulut, Cocok bulanan, Menguatkan otot² muda kembali', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(173, 3, 2, 'PRD173', 'Kuat Semangat Helbeh Mutiara', 73000.00, 824, 73, 'Mengatasi lemah syahwat, sakit pinggang, rematik, encok dll, Helbeh mutiara ini merangsang otot dan syaraf yang lemah pada seluruh organ tubuh, Menghilangkan jantung berdebar, letih, lesu, dan asam urat, Menghilangkan nyeri tulang dan tambah darah', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(174, 3, 2, 'PRD174', 'jamu kuat Helbeh Mutiara', 32000.00, 694, 66, 'Menghilangkan rasa letih, Menambah tenaga /energi, Mengobati lemah syahwat, Mengobati jantung berdebar, Menghilangkan sakit pinggang, Menghilangkan rematik/encok, Mengurangi asam urat, Menghancurkan batu ginjal', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(175, 2, 2, 'PRD175', 'jamu kuat pria dewasa semalam di madura', 62000.00, 746, 56, 'meningkatkan stamina dan daya tubuh pria', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(176, 1, 2, 'PRD176', 'jamu helbeh semangat ramuan herbal madura', 7000.00, 484, 79, 'menguatkan badan, mencerdaskan otak, mencegah reumatik, menambah semangat, menyembuhkan batuk asma, menyembuhkan sakit pinggang', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(177, 2, 2, 'PRD177', 'jamu sariwangi ramuan madura', 36000.00, 972, 83, 'untuk membantu menghilangkan bau mulut dan bau badan pada pria dan wanita, produk sari wangi ini merupakan jamu herbal yang bisa membantu menghilangkan bau mulut, bau badan dan keringat serta bau yang keluar dari tubuh manusia, dan juga bau yang keluar dari mulut yang bersumber dari perut dan pencernaan.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(178, 1, 1, 'PRD178', 'Serimpang', 62000.00, 776, 70, '', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(179, 2, 3, 'PRD179', 'Lancar darah', 84000.00, 569, 65, 'untuk melancarkan keluarnya darah pada waktu dan masa datang bulan, mencegah dan mengurangi sakit perut, sakit pinggang, pusing, dan nyeri yang timbul pada masa datang bulan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(180, 3, 1, 'PRD180', 'jamu tradisional madura kecantikan', 74000.00, 623, 87, 'menjaga agr badan tetap sehat, segar, awet muda dan mencegah timbulnya jerawat, melangsingkan tubuh dan mengencangkan tubuh, mengurangi bau badan', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(181, 2, 2, 'PRD181', 'jamu gendong klasik madura(JKM)', 82000.00, 656, 84, 'Menjaga kebersihan Miss V, Mencegah keputihan, Menghilangkan bau tak sedap, Menghilangkan gatal - gatal, Mengurangi lendir berlebih, Merapatkan miss V, Empot - Empot, Membuat Miss V lebih keset dan harum, Mengencangkan otot - otot Miss V, Cocok untuk ibu menyusui, Melancarkan HAID, Mendetox kotoran yg ada pada rahim', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(182, 1, 3, 'PRD182', 'ramuan tradisional madura', 40000.00, 835, 78, 'asma, reumatik, tekanan darah tinggi, kencing manis, kencing batu, ginjal, amandel, tumor, migrain, lever alergi mag, mag, lemah jantung, exim, kurang nafsu makan, kurang darah, lemah syahwat, asam urat', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(183, 2, 3, 'PRD183', 'jamu sehat bugar', 82000.00, 996, 68, 'Membantu menyembuhkan, Asma, Sebagai obat Reumatik, Bisa mengobati Tekanan, Darah Tinggi, Dapat membantu menyembuhkan, Kencing Manis (diabetes), Dapat membantu menyembuhkan Kencing Batu, Bisa Membantu menyembuhkan Penyakit, Ginjal, Membantu menyembuhkan Amandel, Membantu Menyembuhkan,Tumor, Dapat membantu menyembuhkan, Migrain (sakit kepala), Bisa membantu menyembuhkan, Lever atau hepatitis, Dapat membantu menyembuhkan, Alergi, Bisa membantu menyembuhkan, Menyembuhkan Mag, Dapat membantu menyembuhkan, Lemah Jantung, Membantu , Mengobati Eksim (penyakit kulit), Menambah Nafsu makan, Bisa membantu menyembuhkan Kurang darah, Bisa membantu menyembuhkan Lemah, Syahwat, dan Dapat membantu menyembuhkan Asam Urat', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(184, 1, 3, 'PRD184', 'galian singset (susut perut)', 66000.00, 480, 71, 'untuk mengurangi lemak dalam tubuh', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(185, 3, 1, 'PRD185', 'super empot', 89000.00, 77, 92, 'Mengencangkan kembali otot otot kewanitaan, Mengurangi lendir berlebihan pada daerah kewanitaan, Memberikan kepuasan hubungan suami istri, Menjaga kesehatan wanita, Menjaga agar awet muda.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(186, 1, 1, 'PRD186', 'super rapet', 57000.00, 323, 58, 'membantu mengurangi lendir', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(187, 1, 2, 'PRD187', 'jamu dalimah putih', 85000.00, 66, 98, 'dan selalu awet muda', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(188, 2, 1, 'PRD188', 'GALIAN SEHAT / MONTOK', 18000.00, 515, 62, 'Pil jamu ramuan Madura untuk wanita / pria yang kurang nafsu makan, berbadan kurus, muka pucat, sering sakit, badan lemah dan lain-lain. Minumlah jamu ini secara teratur supaya badan jadi sehat, segar dan montok padat berisi.', '2026-05-21 07:45:41', '2026-05-21 07:45:41'),
(189, 3, 2, 'PRD189', 'super pria', 30000.00, 8, 73, 'Menyembuhkan lemah syahwat dan impotensi pada laki laki, Menambah gairah bagi pria.', '2026-05-21 07:45:41', '2026-05-21 07:45:41');

--
-- Trigger `produk`
--
DELIMITER $$
CREATE TRIGGER `trg_notifikasi_stok` AFTER UPDATE ON `produk` FOR EACH ROW BEGIN
    IF NEW.stok_terkini = 0 AND OLD.stok_terkini > 0 THEN
        INSERT INTO notifikasi (id_produk, id_pengguna, jenis, pesan)
        SELECT NEW.id_produk,
               p.id_pengguna,
               'stok_habis',
               CONCAT('Stok produk "', NEW.nama_produk, '" telah habis')
        FROM pengguna p
        WHERE p.status = 'aktif';

    ELSEIF NEW.stok_terkini <= NEW.stok_minimum
        AND OLD.stok_terkini > NEW.stok_minimum THEN
        INSERT INTO notifikasi (id_produk, id_pengguna, jenis, pesan)
        SELECT NEW.id_produk,
               p.id_pengguna,
               'stok_minimum',
               CONCAT('Stok produk "', NEW.nama_produk,
                      '" mendekati batas minimum (', NEW.stok_terkini, ')')
        FROM pengguna p
        WHERE p.status = 'aktif';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `retur_produk`
--

CREATE TABLE `retur_produk` (
  `id_retur` bigint UNSIGNED NOT NULL,
  `id_distributor` bigint UNSIGNED NOT NULL,
  `id_produk` bigint UNSIGNED NOT NULL,
  `id_pengguna` bigint DEFAULT NULL,
  `tanggal_lapor` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `jumlah_retur` int NOT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci,
  `foto_bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `id_admin_verifikator` bigint DEFAULT NULL,
  `tanggal_verifikasi` datetime DEFAULT NULL,
  `alasan_penolakan` text COLLATE utf8mb4_unicode_ci,
  `id_supplier` bigint UNSIGNED DEFAULT NULL,
  `supplier_diberitahu` tinyint(1) NOT NULL DEFAULT '0',
  `tanggal_selesai` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `role`
--

CREATE TABLE `role` (
  `id_role` bigint NOT NULL,
  `nama_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role`
--

INSERT INTO `role` (`id_role`, `nama_role`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'Admin sistem', '2026-05-21 07:45:39', '2026-05-21 07:45:39'),
(2, 'Staf Gudang', 'Pengelola stok', '2026-05-21 07:45:39', '2026-05-21 07:45:39'),
(3, 'Manajer', 'Manajemen distribusi', '2026-05-21 07:45:39', '2026-05-21 07:45:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `satuan`
--

CREATE TABLE `satuan` (
  `id_satuan` bigint UNSIGNED NOT NULL,
  `nama_satuan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `singkatan` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `satuan`
--

INSERT INTO `satuan` (`id_satuan`, `nama_satuan`, `singkatan`, `created_at`, `updated_at`) VALUES
(1, 'Botol', 'btl', '2026-05-21 07:45:39', '2026-05-21 07:45:39'),
(2, 'Kotak', 'ktk', '2026-05-21 07:45:39', '2026-05-21 07:45:39'),
(3, 'Pcs', 'pcs', '2026-05-21 07:45:39', '2026-05-21 07:45:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('OBJSGy5fKimZVUz3MfKMGkYvol1C90wkOCou2tdz', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJYckdZRUtubUM3TmR6R01KR2E4dnYwWlpNYml5YmIyOUw5SU1LTll0IiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL25vdGlmaWthc2lcL3ByZXZpZXciLCJyb3V0ZSI6Im5vdGlmaWthc2kucHJldmlldyJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwicGVuZ2d1bmEiOnsiaWRfcGVuZ2d1bmEiOjIsIm5hbWFfbGVuZ2thcCI6IlN0YWYgR3VkYW5nIiwicm9sZSI6IlN0YWYgR3VkYW5nIn19', 1779330682);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_keluar`
--

CREATE TABLE `stok_keluar` (
  `id_stok_keluar` bigint UNSIGNED NOT NULL,
  `id_pengguna` bigint DEFAULT NULL,
  `id_distributor` bigint UNSIGNED DEFAULT NULL,
  `id_order` bigint UNSIGNED DEFAULT NULL,
  `nomor_transaksi` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_keluar` date NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stok_keluar`
--

INSERT INTO `stok_keluar` (`id_stok_keluar`, `id_pengguna`, `id_distributor`, `id_order`, `nomor_transaksi`, `tanggal_keluar`, `catatan`, `created_at`, `deleted_at`) VALUES
(1, 2, 2, 2, 'SK-2026-001', '2026-05-13', 'Pengiriman order', '2026-05-21 07:45:41', NULL),
(2, 1, 2, 2, 'SK-20260521-0001', '2026-05-21', 'Stok Keluar', '2026-05-21 01:14:04', NULL),
(3, 2, 2, 2, 'SK-20260521-0002', '2026-05-21', NULL, '2026-05-21 01:19:22', NULL),
(4, 1, 1, 2, 'SK-20260521-0003', '2026-05-21', NULL, '2026-05-21 02:14:18', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_masuk`
--

CREATE TABLE `stok_masuk` (
  `id_stok_masuk` bigint UNSIGNED NOT NULL,
  `id_supplier` bigint UNSIGNED DEFAULT NULL,
  `id_pengguna` bigint DEFAULT NULL,
  `nomor_transaksi` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stok_masuk`
--

INSERT INTO `stok_masuk` (`id_stok_masuk`, `id_supplier`, `id_pengguna`, `nomor_transaksi`, `tanggal_masuk`, `catatan`, `created_at`, `deleted_at`) VALUES
(1, 1, 2, 'SM-2026-001', '2026-05-01', 'Pengadaan awal', '2026-05-21 07:45:41', NULL),
(2, 2, 2, 'SM-2026-002', '2026-05-03', 'Restock bulanan', '2026-05-21 07:45:41', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` bigint UNSIGNED NOT NULL,
  `kode_supplier` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_supplier` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `kode_supplier`, `nama_supplier`, `alamat`, `telepon`, `email`, `kontak_person`, `created_at`, `updated_at`) VALUES
(1, 'SUP001', 'CV Herbal Nusantara', 'Surabaya', '081234567890', 'herbal@nusantara.com', 'Budi', '2026-05-21 07:45:40', '2026-05-21 07:45:40'),
(2, 'SUP002', 'PT Rempah Madura', 'Madura', '081298765432', 'rempah@madura.com', 'Siti', '2026-05-21 07:45:40', '2026-05-21 07:45:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`id_audit`),
  ADD KEY `idx_audit_pengguna` (`id_pengguna`),
  ADD KEY `idx_audit_modul` (`modul`),
  ADD KEY `idx_audit_waktu` (`waktu_aksi`);

--
-- Indeks untuk tabel `batch`
--
ALTER TABLE `batch`
  ADD PRIMARY KEY (`id_batch`),
  ADD UNIQUE KEY `uq_batch_produk` (`id_produk`,`nomor_batch`),
  ADD KEY `idx_batch_produk` (`id_produk`),
  ADD KEY `idx_batch_expired` (`tanggal_expired`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `data_eoq`
--
ALTER TABLE `data_eoq`
  ADD PRIMARY KEY (`id_eoq`),
  ADD KEY `fk_produk_eoq` (`id_produk`);

--
-- Indeks untuk tabel `detail_order`
--
ALTER TABLE `detail_order`
  ADD PRIMARY KEY (`id_detail_order`),
  ADD KEY `fk_detail_order_header` (`id_order`),
  ADD KEY `fk_detail_order_produk` (`id_produk`);

--
-- Indeks untuk tabel `detail_stok_keluar`
--
ALTER TABLE `detail_stok_keluar`
  ADD PRIMARY KEY (`id_detail_keluar`),
  ADD KEY `idx_detail_keluar_prd` (`id_produk`),
  ADD KEY `fk_detail_keluar_batch` (`id_batch`),
  ADD KEY `fk_detail_keluar_header` (`id_stok_keluar`);

--
-- Indeks untuk tabel `detail_stok_masuk`
--
ALTER TABLE `detail_stok_masuk`
  ADD PRIMARY KEY (`id_detail_masuk`),
  ADD KEY `idx_detail_masuk_produk` (`id_produk`),
  ADD KEY `fk_detail_masuk_batch` (`id_batch`),
  ADD KEY `fk_detail_masuk_header` (`id_stok_masuk`);

--
-- Indeks untuk tabel `distributor`
--
ALTER TABLE `distributor`
  ADD PRIMARY KEY (`id_distributor`),
  ADD UNIQUE KEY `uq_kode_distributor` (`kode_distributor`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `uq_kategori_nama` (`nama_kategori`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `idx_notif_produk` (`id_produk`),
  ADD KEY `idx_notif_status` (`status`),
  ADD KEY `fk_notifikasi_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `order_distribusi`
--
ALTER TABLE `order_distribusi`
  ADD PRIMARY KEY (`id_order`),
  ADD UNIQUE KEY `uq_order_nomor` (`nomor_order`),
  ADD KEY `idx_order_distributor` (`id_distributor`),
  ADD KEY `idx_order_status` (`status`),
  ADD KEY `idx_order_tanggal` (`tanggal_order`),
  ADD KEY `fk_order_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `uq_username` (`username`),
  ADD UNIQUE KEY `uq_email` (`email`),
  ADD KEY `fk_pengguna_role` (`id_role`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD UNIQUE KEY `uq_kode_produk` (`kode_produk`),
  ADD UNIQUE KEY `uq_nama_produk` (`nama_produk`),
  ADD KEY `idx_produk_kategori` (`id_kategori`),
  ADD KEY `idx_produk_satuan` (`id_satuan`),
  ADD KEY `idx_produk_stok` (`stok_terkini`);

--
-- Indeks untuk tabel `retur_produk`
--
ALTER TABLE `retur_produk`
  ADD PRIMARY KEY (`id_retur`),
  ADD KEY `idx_retur_distributor` (`id_distributor`),
  ADD KEY `idx_retur_produk` (`id_produk`),
  ADD KEY `idx_retur_pengguna` (`id_pengguna`),
  ADD KEY `idx_retur_status` (`status`),
  ADD KEY `fk_retur_admin_verifikator` (`id_admin_verifikator`),
  ADD KEY `fk_retur_supplier` (`id_supplier`);

--
-- Indeks untuk tabel `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `uq_role_nama` (`nama_role`);

--
-- Indeks untuk tabel `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id_satuan`),
  ADD UNIQUE KEY `uq_satuan_nama` (`nama_satuan`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `stok_keluar`
--
ALTER TABLE `stok_keluar`
  ADD PRIMARY KEY (`id_stok_keluar`),
  ADD UNIQUE KEY `uq_stok_keluar_nomor` (`nomor_transaksi`),
  ADD KEY `idx_stokkeluar_dist` (`id_distributor`),
  ADD KEY `idx_stokkeluar_order` (`id_order`),
  ADD KEY `idx_stokkeluar_tgl` (`tanggal_keluar`),
  ADD KEY `fk_stok_keluar_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `stok_masuk`
--
ALTER TABLE `stok_masuk`
  ADD PRIMARY KEY (`id_stok_masuk`),
  ADD UNIQUE KEY `uq_stok_masuk_nomor` (`nomor_transaksi`),
  ADD KEY `fk_stok_masuk_pengguna` (`id_pengguna`),
  ADD KEY `idx_stokmasuk_supplier` (`id_supplier`),
  ADD KEY `idx_stokmasuk_tanggal` (`tanggal_masuk`);

--
-- Indeks untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`),
  ADD UNIQUE KEY `uq_kode_supplier` (`kode_supplier`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `id_audit` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `batch`
--
ALTER TABLE `batch`
  MODIFY `id_batch` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `data_eoq`
--
ALTER TABLE `data_eoq`
  MODIFY `id_eoq` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT untuk tabel `detail_order`
--
ALTER TABLE `detail_order`
  MODIFY `id_detail_order` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detail_stok_keluar`
--
ALTER TABLE `detail_stok_keluar`
  MODIFY `id_detail_keluar` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detail_stok_masuk`
--
ALTER TABLE `detail_stok_masuk`
  MODIFY `id_detail_masuk` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `distributor`
--
ALTER TABLE `distributor`
  MODIFY `id_distributor` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `order_distribusi`
--
ALTER TABLE `order_distribusi`
  MODIFY `id_order` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `retur_produk`
--
ALTER TABLE `retur_produk`
  MODIFY `id_retur` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id_satuan` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `stok_keluar`
--
ALTER TABLE `stok_keluar`
  MODIFY `id_stok_keluar` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `stok_masuk`
--
ALTER TABLE `stok_masuk`
  MODIFY `id_stok_masuk` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD CONSTRAINT `fk_audit_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`);

--
-- Ketidakleluasaan untuk tabel `batch`
--
ALTER TABLE `batch`
  ADD CONSTRAINT `fk_batch_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `data_eoq`
--
ALTER TABLE `data_eoq`
  ADD CONSTRAINT `fk_produk_eoq` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `detail_order`
--
ALTER TABLE `detail_order`
  ADD CONSTRAINT `fk_detail_order_header` FOREIGN KEY (`id_order`) REFERENCES `order_distribusi` (`id_order`),
  ADD CONSTRAINT `fk_detail_order_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `detail_stok_keluar`
--
ALTER TABLE `detail_stok_keluar`
  ADD CONSTRAINT `fk_detail_keluar_batch` FOREIGN KEY (`id_batch`) REFERENCES `batch` (`id_batch`),
  ADD CONSTRAINT `fk_detail_keluar_header` FOREIGN KEY (`id_stok_keluar`) REFERENCES `stok_keluar` (`id_stok_keluar`),
  ADD CONSTRAINT `fk_detail_keluar_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `detail_stok_masuk`
--
ALTER TABLE `detail_stok_masuk`
  ADD CONSTRAINT `fk_detail_masuk_batch` FOREIGN KEY (`id_batch`) REFERENCES `batch` (`id_batch`),
  ADD CONSTRAINT `fk_detail_masuk_header` FOREIGN KEY (`id_stok_masuk`) REFERENCES `stok_masuk` (`id_stok_masuk`),
  ADD CONSTRAINT `fk_detail_masuk_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `fk_notifikasi_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`),
  ADD CONSTRAINT `fk_notifikasi_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `order_distribusi`
--
ALTER TABLE `order_distribusi`
  ADD CONSTRAINT `fk_order_distributor` FOREIGN KEY (`id_distributor`) REFERENCES `distributor` (`id_distributor`),
  ADD CONSTRAINT `fk_order_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`);

--
-- Ketidakleluasaan untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `fk_pengguna_role` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`),
  ADD CONSTRAINT `fk_produk_satuan` FOREIGN KEY (`id_satuan`) REFERENCES `satuan` (`id_satuan`);

--
-- Ketidakleluasaan untuk tabel `retur_produk`
--
ALTER TABLE `retur_produk`
  ADD CONSTRAINT `fk_retur_admin_verifikator` FOREIGN KEY (`id_admin_verifikator`) REFERENCES `pengguna` (`id_pengguna`),
  ADD CONSTRAINT `fk_retur_distributor` FOREIGN KEY (`id_distributor`) REFERENCES `distributor` (`id_distributor`),
  ADD CONSTRAINT `fk_retur_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`),
  ADD CONSTRAINT `fk_retur_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`),
  ADD CONSTRAINT `fk_retur_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`);

--
-- Ketidakleluasaan untuk tabel `stok_keluar`
--
ALTER TABLE `stok_keluar`
  ADD CONSTRAINT `fk_stok_keluar_distributor` FOREIGN KEY (`id_distributor`) REFERENCES `distributor` (`id_distributor`),
  ADD CONSTRAINT `fk_stok_keluar_order` FOREIGN KEY (`id_order`) REFERENCES `order_distribusi` (`id_order`),
  ADD CONSTRAINT `fk_stok_keluar_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`);

--
-- Ketidakleluasaan untuk tabel `stok_masuk`
--
ALTER TABLE `stok_masuk`
  ADD CONSTRAINT `fk_stok_masuk_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
