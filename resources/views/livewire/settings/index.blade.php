<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
               
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid">
        @include('livewire.settings.partials.flash-messages')

        <div class="card ">
            <div class="card-header ">
                @include('livewire.settings.partials.tabs')
            </div>

            <div class="card-body">
                <div class="tab-content">
                    @includeWhen($activeTab === 'general', 'livewire.settings.tabs.general')
                    @includeWhen($activeTab === 'business', 'livewire.settings.tabs.business')
                    @includeWhen($activeTab === 'tax', 'livewire.settings.tabs.tax')
                    @includeWhen($activeTab === 'payment', 'livewire.settings.tabs.payment-methods')
                    @includeWhen($activeTab === 'expense', 'livewire.settings.tabs.expense-categories')
                    @includeWhen($activeTab === 'units', 'livewire.settings.tabs.units')
                    @includeWhen($activeTab === 'users', 'livewire.settings.tabs.users')
                    @includeWhen($activeTab === 'roles', 'livewire.settings.tabs.roles-permissions')
                    @includeWhen($activeTab === 'backup', 'livewire.settings.tabs.backup')
                    @includeWhen($activeTab === 'system', 'livewire.settings.tabs.system')
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('livewire.settings.modals.user-modal')
    @include('livewire.settings.modals.role-modal')
    @include('livewire.settings.modals.permission-modal')
    @include('livewire.settings.modals.backup-modal')
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/settings.css') }}">
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    document.getElementById('logo')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Choose new logo';
        const label = e.target.nextElementSibling;
        if (label) {
            label.textContent = fileName;
        }
    });
  
 
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            @this.set('showUserModal', false);
            @this.set('showRoleModal', false);
            @this.set('showPermissionModal', false);
            @this.set('showBackupModal', false);
        }
    });
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal') && 
        e.target.style.display === 'block') {
        @this.set('showUserModal', false);
        @this.set('showRoleModal', false);
        @this.set('showPermissionModal', false);
        @this.set('showBackupModal', false);
    }
});
</script>
@endpush