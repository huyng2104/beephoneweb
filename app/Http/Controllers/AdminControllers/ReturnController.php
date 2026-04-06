<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class ReturnController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('order.view');

        $returnStatus = $request->string('return_status')->toString();
        $search = $request->string('q')->toString();

        $returnItems = OrderItem::with(['order', 'product'])
            ->where('return_status', '!=', OrderItem::RETURN_NONE)
            ->when(in_array($returnStatus, OrderItem::returnStatuses(), true), fn ($query) => $query->where('return_status', $returnStatus))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('product_name', 'like', '%' . $search . '%')
                      ->orWhereHas('order', function ($subQuery) use ($search) {
                          $subQuery->where('order_code', 'like', '%' . $search . '%')
                                   ->orWhere('customer_name', 'like', '%' . $search . '%')
                                   ->orWhere('customer_phone', 'like', '%' . $search . '%');
                      });
                });
            })
            ->orderByDesc('return_requested_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.returns', [
            'returnItems' => $returnItems,
            'returnStatuses' => OrderItem::returnStatuses(),
            'returnStatusLabels' => OrderItem::returnStatusLabels(),
            'activeReturnStatus' => $returnStatus,
            'search' => $search,
        ]);
    }
}
