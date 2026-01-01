<aside class="hidden lg:block fixed top-16 left-0 bottom-0 w-64 z-10">
    <div class="h-full p-4">
        <div class="glassmorphism rounded-2xl p-4 border border-white/10 h-full">
            @if(auth()->user()?->hasRole('consultant'))
                <div class="text-white/80 text-xs uppercase tracking-wider mb-3">Consultant Panel</div>
            @else
                <div class="text-white/80 text-xs uppercase tracking-wider mb-3">{{ __('menu.user_panel') }}</div>
            @endif

            <nav class="space-y-1">
                @if(auth()->user()?->hasRole('consultant'))
                    <a href="{{ route('consultant.dashboard', ['locale' => app()->getLocale()]) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('consultant.dashboard') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-comments text-emerald-300"></i>
                        <span>Consultations</span>
                    </a>

                    <a href="{{ route('consultant.assessments.index', ['locale' => app()->getLocale()]) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('consultant.assessments.*') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-clipboard-list text-emerald-300"></i>
                        <span>Assessments</span>
                    </a>

                    <a href="{{ route('consultant.daily-missions.index', ['locale' => app()->getLocale()]) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('consultant.daily-missions.*') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-bullseye text-emerald-300"></i>
                        <span>Daily Missions</span>
                    </a>

                    <a href="{{ route('consultant.videos.index', ['locale' => app()->getLocale()]) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('consultant.videos.*') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-film text-emerald-300"></i>
                        <span>Video Resources</span>
                    </a>

                    <a href="{{ route('user.profile.settings.edit', ['locale' => app()->getLocale()]) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('user.profile.settings.*') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-sliders-h text-emerald-300"></i>
                        <span>My Skills</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-chart-line text-emerald-300"></i>
                        <span>{{ __('menu.dashboard') }}</span>
                    </a>

                    <a href="{{ route('user.assessment') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('user.assessment') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-clipboard-check text-emerald-300"></i>
                        <span>{{ __('menu.soul_assessment') }}</span>
                    </a>

                    <a href="{{ route('videos.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('videos.index') || request()->routeIs('videos.show') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-video text-emerald-300"></i>
                        <span>{{ __('menu.videos') }}</span>
                    </a>

                    <a href="{{ route('consultations.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('consultations.*') ? 'bg-white/10 text-white' : '' }}">
                        <i class="fas fa-comments text-emerald-300"></i>
                        <span>{{ __('menu.consultations') }}</span>
                    </a>

                    @if(auth()->user()?->hasRole('admin'))
                        <a href="{{ route('admin.assessments.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('admin.assessments.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-tools text-emerald-300"></i>
                            <span>Assessment Management</span>
                        </a>

                        <a href="{{ route('admin.videos.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('admin.videos.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-film text-emerald-300"></i>
                            <span>Video Resources</span>
                        </a>

                        <a href="{{ route('admin.daily-missions.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition {{ request()->routeIs('admin.daily-missions.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-bullseye text-emerald-300"></i>
                            <span>Daily Missions</span>
                        </a>
                    @endif
                @endif
            </nav>
        </div>
    </div>
</aside>

<div class="lg:hidden fixed bottom-0 inset-x-0 z-20">
    <div class="glassmorphism border-t border-white/10">
        <div class="grid grid-cols-4">
            @if(auth()->user()?->hasRole('consultant'))
                <a href="{{ route('consultant.dashboard', ['locale' => app()->getLocale()]) }}" class="py-3 text-center text-white/80 {{ request()->routeIs('consultant.dashboard') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-comments"></i>
                </a>
                <a href="{{ route('consultant.assessments.index', ['locale' => app()->getLocale()]) }}" class="py-3 text-center text-white/80 {{ request()->routeIs('consultant.assessments.*') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                </a>
                <a href="{{ route('consultant.daily-missions.index', ['locale' => app()->getLocale()]) }}" class="py-3 text-center text-white/80 {{ request()->routeIs('consultant.daily-missions.*') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-bullseye"></i>
                </a>
                <a href="{{ route('user.profile.settings.edit', ['locale' => app()->getLocale()]) }}" class="py-3 text-center text-white/80 {{ request()->routeIs('user.profile.settings.*') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-sliders-h"></i>
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="py-3 text-center text-white/80 {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-chart-line"></i>
                </a>
                <a href="{{ route('user.assessment') }}" class="py-3 text-center text-white/80 {{ request()->routeIs('user.assessment') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-clipboard-check"></i>
                </a>
                <a href="{{ route('videos.index') }}" class="py-3 text-center text-white/80 {{ request()->routeIs('videos.*') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-video"></i>
                </a>
                <a href="{{ route('consultations.index') }}" class="py-3 text-center text-white/80 {{ request()->routeIs('consultations.*') ? 'bg-white/10 text-white' : '' }}">
                    <i class="fas fa-comments"></i>
                </a>
            @endif
        </div>
    </div>
</div>
