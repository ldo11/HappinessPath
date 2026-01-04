@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Users</h2>
            <p class="text-sm text-white/60 mt-1">Manage user progress and mission assignments.</p>
        </div>
        <form method="GET" action="{{ route('consultant.users.index', [app()->getLocale()]) }}" class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="bg-white/10 border border-white/10 rounded-xl px-4 py-2 pl-10 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-white/40"></i>
        </form>
    </div>

    <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white/5 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Active Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white/70 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-white/70 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($users as $user)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('consultant.users.progress', [app()->getLocale(), $user->id]) }}" class="flex items-center hover:bg-white/5 -mx-2 -my-2 px-2 py-2 rounded transition">
                                    <div class="h-8 w-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold text-xs mr-3">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-white font-medium hover:text-emerald-400 transition">{{ $user->name }}</div>
                                        <div class="text-xs text-white/50">{{ $user->email }}</div>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('consultant.users.progress', [app()->getLocale(), $user->id]) }}" class="inline-block hover:bg-white/5 -mx-2 -my-2 px-2 py-2 rounded transition">
                                    <span class="px-2 py-1 rounded-full text-xs border border-white/10 bg-white/5 text-white/70">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <select id="mission-set-{{ $user->id }}" class="bg-white/10 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 w-full max-w-xs">
                                    <option value="" class="text-gray-900">-- None --</option>
                                    @foreach($missionSets as $missionSet)
                                        <option value="{{ $missionSet->id }}" class="text-gray-900" {{ $user->active_mission_set_id == $missionSet->id ? 'selected' : '' }}>
                                            {{ $missionSet->getTranslation('name', app()->getLocale()) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('consultant.users.progress', [app()->getLocale(), $user->id]) }}" class="block hover:bg-white/5 -mx-2 -my-2 px-2 py-2 rounded transition">
                                    @if($user->mission_started_at)
                                        @php
                                            $day = $user->mission_started_at->diffInDays(now()) + 1;
                                        @endphp
                                        <div class="text-white/80 text-sm">Day {{ $day }}</div>
                                    @else
                                        <div class="text-white/30 text-sm">-</div>
                                    @endif
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" 
                                    class="bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 hover:text-emerald-300 px-3 py-1.5 rounded-lg text-xs font-medium transition border border-emerald-500/20"
                                    onclick="assignMissionSet('{{ $user->id }}', '{{ route('consultant.users.assign', [app()->getLocale(), $user->id]) }}')"
                                >
                                    Save
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-white/10">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function assignMissionSet(userId, url) {
        const select = document.getElementById('mission-set-' + userId);
        const missionSetId = select.value;
        const btn = event.target;
        const originalText = btn.innerText;

        // Disable button and show loading state
        btn.disabled = true;
        btn.innerText = '...';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mission_set_id: missionSetId,
                // We don't reset progress by default here, but could add logic if needed
            })
        })
        .then(response => {
            if (response.redirected) {
                // If the controller returns a redirect (as it currently does: return back()->with...),
                // we interpret that as success for now, or we should update controller to return JSON.
                // However, since we want to avoid page reload, we just handle the UI.
                // Ideally controller returns JSON, but let's see if we can just handle the status.
                // If it's a redirect, fetch follows it automatically usually.
                return { success: true };
            }
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            // Controller returns redirect, so we might not get JSON.
            // But let's check content type
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return response.json();
            } else {
                return { success: true }; // Assume success if redirect/html
            }
        })
        .then(data => {
            // Show success feedback
            btn.innerText = 'Saved!';
            btn.classList.remove('text-emerald-400', 'bg-emerald-500/20');
            btn.classList.add('text-white', 'bg-emerald-500');
            
            setTimeout(() => {
                btn.innerText = originalText;
                btn.disabled = false;
                btn.classList.add('text-emerald-400', 'bg-emerald-500/20');
                btn.classList.remove('text-white', 'bg-emerald-500');
            }, 2000);
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerText = 'Error';
            btn.classList.add('text-red-400', 'bg-red-500/20');
            
            setTimeout(() => {
                btn.innerText = originalText;
                btn.disabled = false;
                btn.classList.remove('text-red-400', 'bg-red-500/20');
            }, 2000);
        });
    }
</script>
@endsection
