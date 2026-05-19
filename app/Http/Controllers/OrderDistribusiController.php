<?php

namespace App\Http\Controllers;

use App\Exports\OrderDistribusiExport;
use App\Http\Requests\ApproveOrderDistribusiRequest;
use App\Http\Requests\RejectOrderDistribusiRequest;
use App\Http\Requests\StoreOrderDistribusiRequest;
use App\Http\Requests\UpdateOrderDistribusiRequest;
use App\Models\DetailOrder;
use App\Models\Distributor;
use App\Models\OrderDistribusi;
use App\Models\Pengguna;
use App\Models\Produk;
use App\Services\OrderDistribusiService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class OrderDistribusiController extends Controller
{
    /**
     * Buat controller order distribusi baru.
     */
    public function __construct(private readonly OrderDistribusiService $orderService) {}

    /**
     * Tampilkan daftar order distribusi.
     */
    public function index(Request $request): View
    {
        $sort = $this->sortColumn($request->string('sort')->toString());
        $direction = $request->string('direction')->lower()->toString() === 'asc' ? 'asc' : 'desc';

        $orders = $this->filteredOrders($request)
            ->with(['distributor', 'pengguna'])
            ->withCount('detailOrders')
            ->withSum('detailOrders as total_nilai_order', 'subtotal')
            ->when(
                $sort === 'distributor',
                fn (Builder $query): Builder => $query->orderBy(
                    Distributor::query()
                        ->select('nama_distributor')
                        ->whereColumn('distributor.id_distributor', 'order_distribusi.id_distributor'),
                    $direction
                ),
                fn (Builder $query): Builder => $query->orderBy($sort, $direction)
            )
            ->orderByDesc('id_order')
            ->paginate(15)
            ->withQueryString();

        return view('order-distribusi.index', [
            'orders' => $orders,
            'distributors' => Distributor::query()->orderBy('nama_distributor')->get(),
            'filters' => $request->only(['status', 'id_distributor', 'tanggal_mulai', 'tanggal_selesai', 'q', 'sort', 'direction']),
            'statistics' => $this->statistics(),
        ]);
    }

    /**
     * Tampilkan form order baru.
     */
    public function create(): View
    {
        return view('order-distribusi.form', [
            'order' => null,
            'nomorOrder' => $this->orderService->generateNomorOrder(today()->toDateString()),
            'distributors' => Distributor::query()->aktif()->orderBy('nama_distributor')->get(),
            'products' => Produk::query()->with('satuan')->orderBy('nama_produk')->get(),
            'action' => route('order-distribusi.store'),
            'method' => 'POST',
        ]);
    }

    /**
     * Simpan order distribusi baru.
     */
    public function store(StoreOrderDistribusiRequest $request): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $order = $this->orderService->store([...$request->validated(), 'ip_address' => $request->ip()], $pengguna);

        return redirect()
            ->route('order-distribusi.show', $order)
            ->with('success', 'Order distribusi berhasil dibuat.');
    }

    /**
     * Tampilkan detail order distribusi.
     */
    public function show(OrderDistribusi $orderDistribusi): View
    {
        $orderDistribusi->load([
            'distributor',
            'pengguna',
            'detailOrders.produk.satuan',
            'stokKeluar.detailStokKeluar.produk.satuan',
            'stokKeluar.detailStokKeluar.batch',
        ]);

        return view('order-distribusi.show', [
            'order' => $orderDistribusi,
            'stokByProduk' => Produk::query()
                ->whereIn('id_produk', $orderDistribusi->detailOrders->pluck('id_produk'))
                ->pluck('stok_terkini', 'id_produk'),
        ]);
    }

    /**
     * Tampilkan form edit order pending.
     */
    public function edit(OrderDistribusi $orderDistribusi): View|RedirectResponse
    {
        if (! $orderDistribusi->isPending()) {
            return redirect()
                ->route('order-distribusi.show', $orderDistribusi)
                ->with('error', 'Order tidak dapat diubah karena sudah diproses.');
        }

        $orderDistribusi->load('detailOrders.produk');

        return view('order-distribusi.form', [
            'order' => $orderDistribusi,
            'nomorOrder' => $orderDistribusi->nomor_order,
            'distributors' => Distributor::query()->aktif()->orderBy('nama_distributor')->get(),
            'products' => Produk::query()->with('satuan')->orderBy('nama_produk')->get(),
            'action' => route('order-distribusi.update', $orderDistribusi),
            'method' => 'PUT',
        ]);
    }

    /**
     * Simpan perubahan order distribusi pending.
     */
    public function update(UpdateOrderDistribusiRequest $request, OrderDistribusi $orderDistribusi): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $order = $this->orderService->update($orderDistribusi, [...$request->validated(), 'ip_address' => $request->ip()], $pengguna);

        return redirect()
            ->route('order-distribusi.show', $order)
            ->with('success', 'Order distribusi berhasil diperbarui.');
    }

    /**
     * Setujui order distribusi dan buat stok keluar otomatis.
     */
    public function approve(ApproveOrderDistribusiRequest $request, OrderDistribusi $orderDistribusi): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $order = $this->orderService->approve($orderDistribusi, [...$request->validated(), 'ip_address' => $request->ip()], $pengguna);

        return redirect()
            ->route('order-distribusi.show', $order)
            ->with('success', 'Order berhasil disetujui dan stok keluar telah dibuat.');
    }

    /**
     * Tolak order distribusi pending.
     */
    public function reject(RejectOrderDistribusiRequest $request, OrderDistribusi $orderDistribusi): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $order = $this->orderService->reject($orderDistribusi, $request->string('alasan')->toString(), $pengguna, $request->ip());

        return redirect()
            ->route('order-distribusi.show', $order)
            ->with('success', 'Order distribusi berhasil ditolak.');
    }

    /**
     * Batalkan order distribusi pending.
     */
    public function cancel(Request $request, OrderDistribusi $orderDistribusi): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        abort_unless($pengguna->canManageStock(), 403, 'Anda tidak memiliki hak akses untuk membatalkan order.');

        $order = $this->orderService->cancel($orderDistribusi, $pengguna, $request->ip());

        return redirect()
            ->route('order-distribusi.show', $order)
            ->with('success', 'Order distribusi berhasil dibatalkan.');
    }

    /**
     * Buat ulang order yang ditolak.
     */
    public function reorder(Request $request, OrderDistribusi $orderDistribusi): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        abort_unless($pengguna->canManageStock(), 403, 'Anda tidak memiliki hak akses untuk membuat ulang order.');

        $newOrder = $this->orderService->reorder($orderDistribusi, $pengguna, $request->ip());

        return redirect()
            ->route('order-distribusi.edit', $newOrder)
            ->with('success', 'Order baru berhasil dibuat ulang. Periksa kembali sebelum menyimpan perubahan.');
    }

    /**
     * Cetak surat jalan order.
     */
    public function print(Request $request, OrderDistribusi $orderDistribusi): Response
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        $orderDistribusi->load([
            'distributor',
            'pengguna',
            'detailOrders.produk.satuan',
            'stokKeluar.detailStokKeluar.produk.satuan',
            'stokKeluar.detailStokKeluar.batch',
        ]);

        $this->orderService->recordPrint($orderDistribusi, $pengguna, $request->ip());

        if (app()->bound('dompdf.wrapper')) {
            return app('dompdf.wrapper')
                ->loadView('order-distribusi.print', ['order' => $orderDistribusi])
                ->download('surat-jalan-'.$orderDistribusi->nomor_order.'.pdf');
        }

        return response()
            ->view('order-distribusi.print', ['order' => $orderDistribusi])
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Ekspor data order sesuai filter aktif.
     */
    public function export(Request $request): Response
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        abort_unless($pengguna->canExportOrders(), 403, 'Anda tidak memiliki hak akses untuk ekspor order.');

        $orders = $this->filteredOrders($request)
            ->with(['distributor', 'pengguna'])
            ->withCount('detailOrders')
            ->withSum('detailOrders as total_nilai_order', 'subtotal')
            ->latest('tanggal_order')
            ->get();

        $this->orderService->recordExport($request->query(), $orders->count(), $pengguna, $request->ip());

        return Excel::download(
            new OrderDistribusiExport($orders),
            'order-distribusi-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    /**
     * Query order berdasarkan filter request.
     */
    private function filteredOrders(Request $request): Builder
    {
        return OrderDistribusi::query()
            ->byStatus($request->input('status'))
            ->byDistributor($request->input('id_distributor'))
            ->byDateRange($request->input('tanggal_mulai'), $request->input('tanggal_selesai'))
            ->search($request->input('q'));
    }

    /**
     * Ringkasan statistik daftar order.
     *
     * @return array<string, mixed>
     */
    private function statistics(): array
    {
        return [
            'today' => OrderDistribusi::query()->whereDate('tanggal_order', today())->count(),
            'pending' => OrderDistribusi::query()->where('status', 'pending')->count(),
            'done_this_month' => OrderDistribusi::query()
                ->where('status', 'selesai')
                ->whereBetween('tanggal_order', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->count(),
            'value_this_month' => DetailOrder::query()
                ->whereHas('orderDistribusi', fn (Builder $query): Builder => $query
                    ->where('status', 'selesai')
                    ->whereBetween('tanggal_order', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]))
                ->sum('subtotal'),
        ];
    }

    /**
     * Validasi kolom sorting.
     */
    private function sortColumn(string $sort): string
    {
        return [
            'nomor_order' => 'nomor_order',
            'tanggal_order' => 'tanggal_order',
            'status' => 'status',
            'distributor' => 'distributor',
        ][$sort] ?? 'tanggal_order';
    }
}
