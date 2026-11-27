<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function loginForm()
    {
        return view('admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Correo o contraseña incorrectos.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.pedidos'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function pedidos()
    {
        $orders = Order::with('items.product')->latest()->get();

        return view('admin-pedidos', ['orders' => $orders]);
    }

    public function estado(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pendiente,pagado,enviado,cancelado',
        ]);

        $order->update($data);

        return back();
    }
}