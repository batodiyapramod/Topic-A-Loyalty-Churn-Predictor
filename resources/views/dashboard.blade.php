<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AllCalls.io — Premium Churn Prevention Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-8">
    <div class="max-w-7xl mx-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-white">Churn Risk Command</h1>
            <p class="text-slate-400 mt-2">Top 50 High-Value At-Risk Gold and Platinum Loyalty Accounts</p>
        </header>

        @if(session('flash_success'))
            <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 p-4 rounded-lg mb-6 text-sm">
                {{ session('flash_success') }}
            </div>
        @endif

        <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-850 border-b border-slate-700 text-slate-300 text-xs font-semibold uppercase tracking-wider">
                        <th class="p-4">Member ID</th>
                        <th class="p-4">Tier</th>
                        <th class="p-4">Tenure (Mo)</th>
                        <th class="p-4">30D Metrics</th>
                        <th class="p-4">P(Churn)</th>
                        <th class="p-4">AI Retention Copy</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700 text-sm">
                    @foreach($predictions as $row)
                        <tr class="hover:bg-slate-750/50 transition">
                            <td class="p-4 font-mono text-slate-400">#{{ $row->loyaltyMember->id }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $row->loyaltyMember->tier === 'Platinum' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                    {{ $row->loyaltyMember->tier }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-300">{{ $row->loyaltyMember->tenure_months }} mos</td>
                            <td class="p-4 text-slate-300">
                                <div class="text-xs font-semibold text-slate-400">{{ $row->loyaltyMember->visits_30d }} visits</div>
                                <div class="text-xs text-slate-500">${{ number_format($row->loyaltyMember->spend_30d, 2) }} spent</div>
                            </td>
                            <td class="p-4 font-bold {{ $row->predicted_probability >= 0.70 ? 'text-rose-400' : 'text-amber-400' }}">
                                {{ number_format($row->predicted_probability * 100, 1) }}%
                            </td>
                            <td class="p-4 max-w-xs text-xs text-slate-400 italic">
                                {{ $row->generated_retention_offer ?? 'No Offer Required' }}
                            </td>
                            <td class="p-4 text-right">
                                @if($row->status === 'offered')
                                    <span class="text-xs font-medium text-slate-500 bg-slate-700 px-3 py-1.5 rounded-lg">Dispatched</span>
                                @else
                                    <form action="{{ route('prediction.offer', $row->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-medium text-xs px-3 py-1.5 rounded-lg shadow dynamic transition">
                                            Send Offer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
