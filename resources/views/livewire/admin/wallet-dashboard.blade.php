<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Quản Lý Ví Hệ Thống</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="text-gray-500 text-sm font-medium uppercase">Doanh Thu Sàn (Ví Admin)</div>
            <div class="text-3xl font-bold text-green-600 mt-2">
                {{ number_format($adminBalance, 0, ',', '.') }} VNĐ
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="text-gray-500 text-sm font-medium uppercase">Tổng Số Dư Seller (Phải trả)</div>
            <div class="text-3xl font-bold text-blue-600 mt-2">
                {{ number_format($totalSellerBalance, 0, ',', '.') }} VNĐ
            </div>
        </div>

    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-bold text-gray-700">Lịch sử giao dịch gần đây</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                        <th class="p-4">Thời gian</th>
                        <th class="p-4">Mã tham chiếu</th>
                        <th class="p-4">User</th>
                        <th class="p-4">Loại GD</th>
                        <th class="p-4 text-right">Số tiền</th>
                        <th class="p-4">Nội dung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transactions as $tx)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 text-sm text-gray-500">
                            {{ $tx->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="p-4 text-sm font-mono text-gray-500">
                            #{{ $tx->reference_id ?? 'N/A' }}
                        </td>
                        <td class="p-4 text-sm font-bold text-gray-700">
                            {{ $tx->wallet->user->name ?? 'Unknown' }}
                            <span class="block text-xs font-normal text-gray-400">
                                {{ $tx->wallet->user->role }}
                            </span>
                        </td>
                        <td class="p-4">
                            @if($tx->type == 'commission')
                                <span class="px-2 py-1 text-xs font-bold text-green-700 bg-green-100 rounded">Hoa hồng</span>
                            @elseif($tx->type == 'deposit')
                                <span class="px-2 py-1 text-xs font-bold text-blue-700 bg-blue-100 rounded">Thanh toán</span>
                            @elseif($tx->type == 'withdraw')
                                <span class="px-2 py-1 text-xs font-bold text-red-700 bg-red-100 rounded">Rút tiền</span>
                            @else
                                <span class="px-2 py-1 text-xs font-bold text-gray-700 bg-gray-100 rounded">{{ $tx->type }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-right font-bold {{ $tx->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 0, ',', '.') }}
                        </td>
                        <td class="p-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $tx->description }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>