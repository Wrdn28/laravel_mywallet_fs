<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>Admin Dashboard - {{ $appName }}</title>
    @include('partials.head')
</head>
<body class="bg-surface overflow-x-hidden">

<div class="fixed inset-0 pointer-events-none -z-10">
    <div class="absolute top-[5%] left-[20%] w-[40rem] h-[40rem] bg-violet-700/8 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-[5%] right-[5%] w-[30rem] h-[30rem] bg-red-700/5 rounded-full blur-[100px]"></div>
</div>

<div class="min-h-screen">

    @include('admin.partials.header')

    @include('partials.toast')

    <div class="p-4 lg:p-8 space-y-6">

        @include('admin.partials.stats-cards')

        @include('admin.partials.tabs')

        {{-- Tab Contents --}}
        <div id="tab-users"        class="tab-content {{ $activeTab === 'users'        ? '' : 'hidden' }}">
            @include('admin.partials.tab-users')
        </div>
        <div id="tab-transactions" class="tab-content {{ $activeTab === 'transactions' ? '' : 'hidden' }}">
            @include('admin.partials.tab-transactions')
        </div>
        <div id="tab-budgets"      class="tab-content {{ $activeTab === 'budgets'      ? '' : 'hidden' }}">
            @include('admin.partials.tab-budgets')
        </div>
        <div id="tab-reports"      class="tab-content {{ $activeTab === 'reports'      ? '' : 'hidden' }}">
            @include('admin.partials.tab-reports')
        </div>
        <div id="tab-config"       class="tab-content {{ $activeTab === 'config'       ? '' : 'hidden' }}">
            @include('admin.partials.tab-config')
        </div>

    </div>
</div>

@include('admin.partials.modals')
@include('admin.partials.scripts')

</body>
</html>
