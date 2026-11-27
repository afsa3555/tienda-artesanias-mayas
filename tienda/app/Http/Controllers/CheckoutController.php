<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    private function stripe(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }

    public function show()
    {
        $lines = Cart::lines();
        if ($lines->isEmpty()) {
            return redirect()->route('carrito');
        }

        return view('checkout', ['lines' => $lines, 'total' => Cart::total()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
        ]);

        $lines = Cart::lines();
        if ($lines->isEmpty()) {
            return redirect()->route('carrito');
        }

        $order = DB::transaction(function () use ($data, $lines) {
            $order = Order::create([
                'customer_name'  => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'total'          => Cart::total(),
                'status'         => 'pendiente',
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'quantity'   => $line['qty'],
                    'unit_price' => $line['product']->price,
                ]);
            }

            return $order;
        });

        $session = $this->stripe()->checkout->sessions->create([
            'mode'           => 'payment',
            'customer_email' => $data['customer_email'],
            'line_items'     => $lines->map(fn ($l) => [
                'quantity'   => $l['qty'],
                'price_data' => [
                    'currency'     => 'mxn',
                    'unit_amount'  => (int) round($l['product']->price * 100),
                    'product_data' => ['name' => $l['product']->name],
                ],
            ])->all(),
            'metadata'    => ['order_id' => $order->id],
            'success_url' => route('checkout.exito', ['order' => $order->id]) . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('checkout.cancelado', ['order' => $order->id]),
        ]);

        $order->update(['payment_reference' => $session->id]);

        return redirect()->away($session->url);
    }

    public function exito(Request $request)
    {
        $order = Order::with('items.product')->findOrFail($request->query('order'));

        $session = $this->stripe()->checkout->sessions->retrieve($order->payment_reference);

        if ($session->payment_status !== 'paid') {
            return redirect()->route('checkout.cancelado', ['order' => $order->id]);
        }

        if ($order->status === 'pendiente') {
            DB::transaction(function () use ($order) {
                $order->update(['status' => 'pagado']);
                foreach ($order->items as $item) {
                    $item->product->decrement('stock', $item->quantity);
                }
            });
            Cart::clear();
        }

        return view('checkout-exito', ['order' => $order]);
    }

    public function cancelado(Request $request)
    {
        $order = Order::findOrFail($request->query('order'));

        if ($order->status === 'pendiente') {
            $order->update(['status' => 'cancelado']);
        }

        return view('checkout-cancelado', ['order' => $order]);
    }
}