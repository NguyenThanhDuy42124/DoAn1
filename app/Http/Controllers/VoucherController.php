<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherController extends Controller
{
    public function index()
    {
        // Hiển thị danh sách voucher cho seller đang đăng nhập
        $vouchers = Voucher::where('seller_id', auth()->id())->paginate(10);
        return view('seller.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('seller.vouchers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id'     => 'required|exists:users,id',
            'discount_rate' => 'required|numeric|min:0',
            'condition'     => 'nullable|string|max:255',
            'expiry_date'   => 'required|date|after:today',
        ]);

        Voucher::create($validated);

        return redirect()->route('vouchers.index')
                         ->with('success', 'Tạo voucher thành công!');
    }

    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('seller.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'discount_rate' => 'required|numeric|min:0',
            'condition'     => 'nullable|string|max:255',
            'expiry_date'   => 'required|date|after:today',
        ]);

        $voucher = Voucher::findOrFail($id);
        $voucher->update($validated);

        return redirect()->route('vouchers.index')
                         ->with('success', 'Cập nhật voucher thành công!');
    }

    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->route('vouchers.index')
                         ->with('success', 'Xóa voucher thành công!');
    }

    public function listVouchers()
    {
        // Trang public
        $vouchers = Voucher::active()->paginate(10);
        return view('pages.listvouchers', compact('vouchers'));
    }
}
