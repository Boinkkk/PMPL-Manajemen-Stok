<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetujuiReturRequest;
use App\Http\Requests\StoreReturRequest;
use App\Http\Requests\TolakReturRequest;
use App\Models\AuditTrail;
use App\Models\Distributor;
use App\Models\Produk;
use App\Models\ReturProduk;
use App\Models\Supplier;
use App\Models\Pengguna;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class ReturController extends Controller
{
    // TODO: Ganti '1' dengan auth()->id() setelah login diintegrasikan
    private const HARDCODED_USER_ID = 1;

    public function index(Request $request)
    {
        // TODO: Hapus komentar $this->authorize saat login ready
        // $this->authorize('viewAny', ReturProduk::class);

        $query = ReturProduk::with(['produk', 'distributor', 'pelapor', 'adminVerifikator', 'supplier']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('produk', function ($q) use ($search) {
                    $q->where('nama_produk', 'like', "%{$search}%");
                })->orWhereHas('distributor', function ($q) use ($search) {
                    $q->where('nama_distributor', 'like', "%{$search}%");
                })->orWhereHas('pelapor', function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%");
                });
            });
        }

        $returs = $query->orderByDesc('tanggal_lapor')->paginate(15)->withQueryString();

        return view('retur.index', [
            'returs' => $returs,
            'status' => $request->status,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        // TODO: Hapus komentar $this->authorize saat login ready
        // $this->authorize('create', ReturProduk::class);

        return view('retur.create', [
            'distributors' => Distributor::all(),
            'produks' => Produk::orderBy('nama_produk')->get(),
            'suppliers' => Supplier::orderBy('nama_supplier')->get(),
        ]);
    }

    public function store(StoreReturRequest $request)
    {
        // TODO: Hapus komentar $this->authorize saat login ready
        // $this->authorize('create', ReturProduk::class);

        $imagePath = null;

        if ($request->hasFile('foto_bukti')) {
            $imagePath = $request->file('foto_bukti')->store('retur', 'public');
        }

        // TODO: Ganti HARDCODED_USER_ID dengan auth()->id()
        $supplierId = $request->input('id_supplier') ?? Supplier::query()->whereNotNull('telepon')->value('id_supplier');

        $retur = DB::transaction(function () use ($request, $imagePath, $supplierId) {
            return ReturProduk::create([
                'id_distributor' => $request->id_distributor,
                'id_produk' => $request->id_produk,
                'id_pengguna' => self::HARDCODED_USER_ID,
                'jumlah_retur' => $request->jumlah_retur,
                'alasan' => $request->alasan,
                'foto_bukti' => $imagePath,
                'status' => 'pending',
                'id_supplier' => $supplierId,
                'supplier_diberitahu' => false,
                'tanggal_lapor' => now(),
            ]);
        });

        AuditTrail::create([
            'id_pengguna' => self::HARDCODED_USER_ID,
            'aksi' => 'create',
            'modul' => 'retur_produk',
            'data_lama' => null,
            'data_baru' => $retur->toArray(),
            'ip_address' => request()->ip(),
            'waktu_aksi' => Carbon::now(),
        ]);

        $service = new WhatsAppNotificationService();

        if ($service->kirimNotifikasiSupplier($retur, 'retur_baru')) {
            $retur->update(['supplier_diberitahu' => true]);
        }

        return redirect()->route('retur.index')->with('success', 'Retur berhasil diajukan.');
    }

    public function show(ReturProduk $retur)
    {
        // TODO: Hapus komentar $this->authorize saat login ready
        // $this->authorize('view', $retur);

        $auditTrail = AuditTrail::query()
            ->where('modul', 'retur_produk')
            ->where(function ($query) use ($retur) {
                $query->whereJsonContains('data_baru->id_retur', $retur->id_retur)
                    ->orWhereJsonContains('data_lama->id_retur', $retur->id_retur);
            })
            ->orderByDesc('waktu_aksi')
            ->get();

        return view('retur.show', [
            'retur' => $retur->load(['produk', 'distributor', 'pelapor', 'adminVerifikator', 'supplier']),
            'auditTrail' => $auditTrail,
        ]);
    }

    public function setujui(SetujuiReturRequest $request, ReturProduk $retur): RedirectResponse
    {
        // TODO: Hapus komentar $this->authorize saat login ready
        // $this->authorize('setujui', $retur);

        // Cek status - retur hanya bisa disetujui jika masih pending
        if ($retur->status !== 'pending') {
            return redirect()->route('retur.show', $retur)->with('error', 'Retur tidak dalam status pending.');
        }

        // TODO: Ganti HARDCODED_USER_ID dengan auth()->id()
        DB::transaction(function () use ($retur) {
            $produk = $retur->produk;
            $oldData = $retur->toArray();

            // Validasi stok cukup
            if ($produk->stok_terkini < $retur->jumlah_retur) {
                throw new \Exception('Stok produk tidak cukup untuk diproses.');
            }

            $produk->decrement('stok_terkini', $retur->jumlah_retur);

            $retur->update([
                'status' => 'disetujui',
                'id_admin_verifikator' => self::HARDCODED_USER_ID,
                'tanggal_verifikasi' => Carbon::now(),
                'tanggal_selesai' => Carbon::now(),
            ]);

            AuditTrail::create([
                'id_pengguna' => self::HARDCODED_USER_ID,
                'aksi' => 'approve',
                'modul' => 'retur_produk',
                'data_lama' => $oldData,
                'data_baru' => $retur->fresh()->toArray(),
                'ip_address' => request()->ip(),
                'waktu_aksi' => Carbon::now(),
            ]);
        });

        $service = new WhatsAppNotificationService();

        if ($service->kirimNotifikasiSupplier($retur->fresh(), 'retur_diproses')) {
            $retur->update(['supplier_diberitahu' => true]);
        }

        return redirect()->route('retur.show', $retur)->with('success', 'Retur berhasil disetujui dan stok produk diperbarui.');
    }

    public function tolak(TolakReturRequest $request, ReturProduk $retur): RedirectResponse
    {
        // TODO: Hapus komentar $this->authorize saat login ready
        // $this->authorize('tolak', $retur);

        // Cek status - retur hanya bisa ditolak jika masih pending
        if ($retur->status !== 'pending') {
            return redirect()->route('retur.show', $retur)->with('error', 'Retur tidak dalam status pending.');
        }

        // TODO: Ganti HARDCODED_USER_ID dengan auth()->id()
        $oldData = $retur->toArray();

        $retur->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
            'id_admin_verifikator' => self::HARDCODED_USER_ID,
            'tanggal_verifikasi' => Carbon::now(),
            'tanggal_selesai' => Carbon::now(),
        ]);

        AuditTrail::create([
            'id_pengguna' => self::HARDCODED_USER_ID,
            'aksi' => 'reject',
            'modul' => 'retur_produk',
            'data_lama' => $oldData,
            'data_baru' => $retur->fresh()->toArray(),
            'ip_address' => request()->ip(),
            'waktu_aksi' => Carbon::now(),
        ]);

        $service = new WhatsAppNotificationService();

        if ($service->kirimNotifikasiSupplier($retur->fresh(), 'retur_diproses')) {
            $retur->update(['supplier_diberitahu' => true]);
        }

        return redirect()->route('retur.show', $retur)->with('success', 'Retur ditolak dan supplier telah diberi tahu.');
    }

    public function destroy(ReturProduk $retur): RedirectResponse
    {
        // Hanya retur dengan status 'pending' yang boleh dihapus
        if ($retur->status !== 'pending') {
            return redirect()->route('retur.index')->with('error', 'Hanya retur pending yang boleh dihapus.');
        }

        $oldData = $retur->toArray();

        if ($retur->foto_bukti && Storage::disk('public')->exists($retur->foto_bukti)) {
            Storage::disk('public')->delete($retur->foto_bukti);
        }

        $retur->delete();

        // TODO: Ganti HARDCODED_USER_ID dengan auth()->id()
        AuditTrail::create([
            'id_pengguna' => self::HARDCODED_USER_ID,
            'aksi' => 'delete',
            'modul' => 'retur_produk',
            'data_lama' => $oldData,
            'data_baru' => null,
            'ip_address' => request()->ip(),
            'waktu_aksi' => Carbon::now(),
        ]);

        return redirect()->route('retur.index')->with('success', 'Retur pending berhasil dihapus.');
    }
}
