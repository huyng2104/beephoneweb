<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
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

        $returnRequests = ReturnRequest::with(['order', 'user', 'items.orderItem'])
            ->when(
                in_array($returnStatus, array_keys(ReturnRequest::statusLabels()), true),
                fn ($q) => $q->where('status', $returnStatus)
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('return_code', 'like', '%' . $search . '%')
                      ->orWhereHas('order', fn ($sub) =>
                            $sub->where('order_code', 'like', '%' . $search . '%')
                                ->orWhere('customer_name', 'like', '%' . $search . '%')
                                ->orWhere('customer_phone', 'like', '%' . $search . '%')
                      );
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.returns', [
            'returnRequests'     => $returnRequests,
            'returnStatuses'     => array_keys(ReturnRequest::statusLabels()),
            'returnStatusLabels' => ReturnRequest::statusLabels(),
            'activeReturnStatus' => $returnStatus,
            'search'             => $search,
        ]);
    }
}
