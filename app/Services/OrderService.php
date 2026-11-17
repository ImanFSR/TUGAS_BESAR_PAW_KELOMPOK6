<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Order;
use App\Models\StockLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Logika bisnis transaksi kasir. Dipanggil dari controller POS.
 *
 * Keputusan desain (konfirmasi ke kelompok/dosen):
 *  - Stok DIPOTONG saat pesanan dibuat (pending), bukan saat selesai, supaya kasir
 *    tidak menjual menu yang sebenarnya sudah dipesan orang lain. Jika dibatalkan, stok dikembalikan.
 *  - Pembayaran (tunai/QRIS + kembalian) diisi saat pesanan diselesaikan, sesuai alur toko (bayar di akhir).
 */
class OrderService
{
    /** @param array<int, array{menu_id:int, qty:int}> $items */
    public function create(int $userId, string $channel, array $items, ?string $note = null): Order
    {
        return DB::transaction(function () use ($userId, $channel, $items, $note) {
            $order = Order::create([
                'invoice_no' => $this->nextInvoiceNo(),
                'user_id' => $userId,
                'channel' => $channel,
                'status' => 'pending',
                'note' => $note,
            ]);

            $total = 0;
            foreach ($items as $i => $row) {
                // lockForUpdate: cegah dua kasir menjual stok terakhir bersamaan
                $menu = Menu::lockForUpdate()->findOrFail($row['menu_id']);

                if (! $menu->is_active || $row['qty'] > $menu->stock) {
                    throw ValidationException::withMessages([
                        "items.$i.qty" => "Stok {$menu->name} tidak cukup (sisa {$menu->stock}).",
                    ]);
                }

                $subtotal = $row['qty'] * $menu->price;
                $order->items()->create([
                    'menu_id' => $menu->id, 'qty' => $row['qty'],
                    'price' => $menu->price, 'subtotal' => $subtotal,
                ]);

                $this->moveStock($menu, -$row['qty'], 'penjualan', $userId, $order->id);
                $total += $subtotal;
            }

            $order->update(['total' => $total]);

            return $order->fresh('items.menu');
        });
    }

    /** pending -> diproses */
    public function process(Order $order): Order
    {
        $this->assertStatus($order, ['pending']);
        $order->update(['status' => 'diproses']);

        return $order;
    }

    /** diproses -> selesai, sekaligus catat pembayaran dan hitung kembalian. */
    public function complete(Order $order, string $method, int $amountPaid): Order
    {
        $this->assertStatus($order, ['diproses']);

        if ($method === 'qris') {
            $amountPaid = $order->total; // QRIS selalu pas
        }
        if ($amountPaid < $order->total) {
            throw ValidationException::withMessages([
                'amount_paid' => 'Uang diterima kurang dari total tagihan.',
            ]);
        }

        $order->update([
            'status' => 'selesai',
            'payment_method' => $method,
            'amount_paid' => $amountPaid,
            'change_amount' => $amountPaid - $order->total,
            'completed_at' => now(),
        ]);

        return $order;
    }

    /** pending/diproses -> dibatalkan, stok dikembalikan. */
    public function cancel(Order $order, int $userId): Order
    {
        $this->assertStatus($order, ['pending', 'diproses']);

        return DB::transaction(function () use ($order, $userId) {
            foreach ($order->items as $item) {
                $menu = Menu::lockForUpdate()->find($item->menu_id);
                $this->moveStock($menu, $item->qty, 'pembatalan', $userId, $order->id);
            }
            $order->update(['status' => 'dibatalkan']);

            return $order;
        });
    }

    /** Set/tambah stok harian (dipakai halaman Stok Harian). */
    public function adjustStock(Menu $menu, int $newStock, int $userId, string $type = 'stok_harian', ?string $note = null): void
    {
        DB::transaction(function () use ($menu, $newStock, $userId, $type, $note) {
            $menu = Menu::lockForUpdate()->find($menu->id);
            $this->moveStock($menu, $newStock - $menu->stock, $type, $userId, null, $note);
        });
    }

    private function moveStock(Menu $menu, int $delta, string $type, int $userId, ?int $orderId, ?string $note = null): void
    {
        $menu->update(['stock' => $menu->stock + $delta]);

        StockLog::create([
            'menu_id' => $menu->id, 'user_id' => $userId, 'order_id' => $orderId,
            'type' => $type, 'qty_change' => $delta, 'stock_after' => $menu->stock, 'note' => $note,
        ]);
    }

    private function assertStatus(Order $order, array $allowed): void
    {
        if (! in_array($order->status, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => "Pesanan berstatus {$order->status} tidak bisa diubah ke langkah ini.",
            ]);
        }
    }

    private function nextInvoiceNo(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $count = Order::where('invoice_no', 'like', $prefix . '%')->lockForUpdate()->count() + 1;

        return $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
