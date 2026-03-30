<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\PointHistory;
use App\Models\PointSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->boolean('skip_review')) {
            $request->session()->forget('review_order_id');
            return redirect()->route('client.orders.index');
        }

        $user = Auth::user();

        $orders = Order::with(['items', 'items.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $reviewOrder = null;
        $reviewOrderId = $request->session()->get('review_order_id');

        if (is_numeric($reviewOrderId)) {
            $reviewOrder = Order::with(['items', 'items.product'])
                ->where('user_id', Auth::id())
                ->find((int) $reviewOrderId);
        }

        if ($reviewOrderId !== null) {
            $request->session()->forget('review_order_id');
        }

        return view('client.profiles.orders', compact('orders', 'user', 'reviewOrder'));
    }

    public function show($id)
    {
        $order = Order::with('items')->where('user_id', Auth::id())->findOrFail($id);

        return view('client.orders.show', compact('order'));
    }

    public function confirmReceived($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status === Order::STATUS_DELIVERED) {
            $order->status = Order::STATUS_RECEIVED;
            $order->payment_status = 'paid';
            $order->paid_at = $order->paid_at ?? now();
            $order->save();

            $pointsEarned = 0;
            $setting = PointSetting::first();
            $earnRate = $setting ? $setting->earn_rate : 100000;

            if ($earnRate > 0) {
                $pointsEarned = floor($order->total_amount / $earnRate);

                if ($pointsEarned > 0) {
                    PointHistory::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'points' => $pointsEarned,
                        'type' => 'earn',
                        'description' => 'Tich diem hoan thanh don hang ' . $order->order_code,
                    ]);

                    $customer = \App\Models\User::find($order->user_id);
                    $customer->reward_points += $pointsEarned;
                    $customer->save();
                }
            }

            $msg = 'Cam on ban da xac nhan. Don hang da hoan thanh.';
            if ($pointsEarned > 0) {
                $msg .= ' Ban duoc cong them ' . $pointsEarned . ' Bee Point vao tai khoan.';
            }

            return redirect()->back()
                ->with('success', $msg)
                ->with('review_order_id', $order->id);
        }

        return redirect()->back()->with('error', 'Trang thai don hang khong hop le.');
    }

    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status === Order::STATUS_PENDING) {
            $order->status = Order::STATUS_CANCELLED;
            $order->cancellation_reason = 'Khach hang tu huy don tren web';
            $order->cancelled_at = now();
            $order->save();

            return redirect()->back()->with('success', 'Da huy don hang thanh cong.');
        }

        return redirect()->back()->with('error', 'Don hang nay dang duoc xu ly, khong the huy.');
    }

    public function requestReturn(Request $request, $id)
    {
        $validated = $request->validate([
            'return_note' => ['required', 'string', 'max:1000'],
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if (! $order->canRequestReturn()) {
            throw ValidationException::withMessages([
                'return_note' => 'Don hang nay chua du dieu kien hoan hang hoac da gui yeu cau truoc do.',
            ]);
        }

        $order->update([
            'return_status' => Order::RETURN_REQUESTED,
            'return_note' => $validated['return_note'],
            'return_requested_at' => now(),
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'status' => '(Hoan hang) ' . Order::RETURN_REQUESTED,
            'note' => 'Khach hang gui yeu cau hoan hang: ' . $validated['return_note'],
        ]);

        return redirect()->back()->with('success', 'Da gui yeu cau hoan hang. Cua hang se xu ly som nhat.');
    }
}
