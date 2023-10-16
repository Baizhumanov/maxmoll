<?php

namespace App\Http\Controllers;

use App\Models\MovementHistory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Order::query();

        if ($request->customer !== null) {
            $query->where('customer', 'like', sprintf('%s', "%" . trim($request->customer) . "%"));
        }

        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        if ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $orders = $query->paginate(5);
        return view('orders.index', [
            "orders" => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('orders.create', [
            "products" => Product::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validateOrderData($request);
        $products = $request->input('products');

        // Проверка на наличие продуктов (count не должен превышать текущий count продукта)
        $dbProducts = Product::whereIn('id', array_keys($products))->get();
        foreach ($products as $id => $count) {
            $dbProduct = $dbProducts->find($id);
            if ($dbProduct->stock < $count) {
                return back()->withInput()->withErrors(['products' => sprintf("Недостаточное количество товара (Товар: %s)", $dbProduct->name)]);
            }
        }

        $order = new Order();
        $order->customer = $request->customer;
        $order->status = "active";
        $order->save();

        foreach ($products as $id => $count) {
            if ($count === null) {
                continue;
            }

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $id;
            $orderItem->count = $count;
            $orderItem->save();

            $dbProduct = $dbProducts->find($id);
            $dbProduct->stock -= $count;
            $dbProduct->save();

            $this->updateStock($id, $count, false);
        }

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $order = Order::findOrFail($id);
        return view('orders.edit', [
            "order" => $order,
            "items" => $order->items
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        $this->validateOrderData($request);
        $products = $request->input('products');

        // Проверка на наличие продуктов (count не должен превышать текущий count продукта)
        $dbProducts = Product::whereIn('id', array_keys($products))->get();
        foreach ($products as $id => $newCount) {
            $currentCount = $order->items->where('product_id', $id)->first()->count;

            // "нужно больше товаров"
            if ($newCount > $currentCount) {
                $dbProduct = $dbProducts->find($id);

                if ($dbProduct->stock < ($newCount - $currentCount)) {
                    return back()->withInput()->withErrors(['products' => sprintf("Недостаточное количество товара (Товар: %s)", $dbProduct->name)]);
                }
            }
        }

        $order->customer = $request->customer;
        $order->save();

        $orderItems = $order->items;
        foreach ($orderItems as $orderItem) {
            $currentCount = $orderItem->count;
            $newCount = $products[$orderItem->product_id];
            $dbProduct = $dbProducts->find($orderItem->product_id);

            if ($newCount === $currentCount) {
                continue;
            } else if ($newCount <= 0) {
                // вернуть все количество
                if ($order->status === "active") {
                    $dbProduct->stock += $currentCount;
                    $dbProduct->save();

                    $this->updateStock($dbProduct->id, $currentCount);
                }

                // удалить позицию
                $orderItem->delete();
            } else {
                // взять еще продуктов
                if ($order->status === "active") {
                    $dbProduct->stock -= ($newCount - $currentCount);
                    $dbProduct->save();

                    $this->updateStock($dbProduct->id, ($newCount - $currentCount), false);
                }

                // изменить позицию
                $orderItem->count = $newCount;
                $orderItem->save();
            }
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Валидация данных с форм
     * @param Request $request
     * @return RedirectResponse|void
     */
    protected function validateOrderData(Request $request)
    {
        $request->validate([
            'customer' => 'required',
        ]);

        $products = $request->input('products');
        if (empty($products) || count(array_filter($products, function ($value) { return $value !== null; })) === 0) {
            return back()->withInput()->withErrors(['products' => 'Необходимо выбрать хотя бы один продукт.']);
        }
    }

    /**
     * Complete Order
     */
    public function complete(string $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        if ($order->status === "active") {
            $order->status = "completed";
            $order->completed_at = Carbon::now();
            $order->save();
        }

        return back();
    }

    /**
     * Cancel Order
     */
    public function cancel(string $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        if ($order->status === "active") {
            $order->status = "canceled";
            $order->save();

            // вернуть товары
            $items = $order->items;
            $dbProducts = Product::whereIn('id', $items->pluck('product_id')->toArray())->get();
            foreach ($items as $item) {
                $dbProduct = $dbProducts->find($item->product_id);
                $dbProduct->stock += $item->count;
                $dbProduct->save();

                $this->updateStock($dbProduct->id, $item->count);
            }
        }

        return back();
    }

    /**
     * Resume Order
     */
    public function resume(string $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        if ($order->status === "canceled") {
            // проверка на наличие товаров
            $items = $order->items;
            $dbProducts = Product::whereIn('id', $items->pluck('product_id')->toArray())->get();
            foreach ($items as $item) {
                $dbProduct = $dbProducts->find($item->product_id);
                if ($dbProduct->stock < $item->count) {
                    return back()->withErrors(['products' => sprintf("Недостаточное количество товара (Товар: %s)", $dbProduct->name)]);
                }
            }

            // изменить статус ордера
            $order->status = "active";
            $order->save();

            // вернуть товары в ордер
            foreach ($items as $item) {
                $dbProduct = $dbProducts->find($item->product_id);
                $dbProduct->stock -= $item->count;
                $dbProduct->save();

                $this->updateStock($dbProduct->id, $item->count, false);
            }
        }

        return back();
    }

    /**
     * Изменить данные в Stock
     */
    private function updateStock(int $product_id, int $count, $increase = true): void
    {
        $stocks = DB::table('stocks')->where('product_id', $product_id)->get();

        if ($increase) {
            DB::table('stocks')
                ->where('product_id', $product_id)
                ->where('warehouse_id', $stocks[0]->warehouse_id)
                ->update(['stock' => $count]);

            MovementHistory::create([
                "product_id" => $product_id,
                "warehouse_id" => $stocks[0]->warehouse_id,
                "old_count" => $stocks[0]->stock,
                "new_count" => $count,
            ]);
        } else {
            // Уменьшать значение stock (возможно, в нескольких складах)
            foreach ($stocks as $stock) {
                $decrementValue = min($count, $stock->stock);
                DB::table('stocks')
                    ->where('product_id', $product_id)
                    ->where('warehouse_id', $stock->warehouse_id)
                    ->update(['stock' => ($stock->stock - $decrementValue)]);
                $count -= $decrementValue;

                MovementHistory::create([
                    "product_id" => $product_id,
                    "warehouse_id" => $stock->warehouse_id,
                    "old_count" => $stock->stock,
                    "new_count" => ($stock->stock - $decrementValue),
                ]);

                if ($count <= 0) {
                    break;
                }
            }
        }
    }
}
