<?php

namespace App\Http\Controllers;

use App\Models\MovementHistory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovementHistoryController extends Controller
{
    /**
     * Display a listing of the history
     */
    public function index(Request $request): View
    {
        $query = MovementHistory::query();

        if ($request->product_name !== null) {
            $query->whereIn('product_id', Product::where('name', 'like', sprintf('%s', "%" . trim($request->product_name) . "%"))->pluck('id')->toArray());
        }

        if ($request->warehouse_name !== null) {
            $query->whereIn('warehouse_id', Warehouse::where('name', 'like', sprintf('%s', "%" . trim($request->warehouse_name) . "%"))->pluck('id')->toArray());
        }

        if ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $histories = $query->paginate(5);
        return view('histories', [
            "histories" => $histories
        ]);
    }
}
