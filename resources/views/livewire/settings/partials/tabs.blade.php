<ul class="nav nav-tabs" id="settings-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}" 
           href="#" 
           wire:click.prevent="$set('activeTab', 'general')"
           role="tab">
            <i class="me-1 fas fa-cog"></i> General
        </a>
    </li>
 
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'tax' ? 'active' : '' }}" 
           href="#" 
           wire:click.prevent="$set('activeTab', 'tax')"
           role="tab">
            <i class="me-1 fas fa-percent"></i> Tax
        </a>
    </li>
   
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'payment' ? 'active' : '' }}" 
           href="#" 
           wire:click.prevent="$set('activeTab', 'payment')"
           role="tab">
            <i class="me-1 fas fa-credit-card"></i> Payment Methods
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'expense' ? 'active' : '' }}" 
           href="#" 
           wire:click.prevent="$set('activeTab', 'expense')"
           role="tab">
            <i class="me-1 fas fa-tags"></i> Expense Categories
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'units' ? 'active' : '' }}" 
           href="#" 
           wire:click.prevent="$set('activeTab', 'units')"
           role="tab">
            <i class="me-1 fas fa-ruler"></i> Units
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'users' ? 'active' : '' }}" 
           href="#" 
           wire:click.prevent="$set('activeTab', 'users')"
           role="tab">
            <i class="me-1 fas fa-users"></i> Users
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'roles' ? 'active' : '' }}" 
           href="#" 
           wire:click.prevent="$set('activeTab', 'roles')"
           role="tab">
            <i class="me-1 fas fa-lock"></i> Roles & Permissions
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'backup' ? 'active' : '' }}" 
           href="#" 
           wire:click.prevent="$set('activeTab', 'backup')"
           role="tab">
            <i class="me-1 fas fa-database"></i> Backup
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'system' ? 'active' : '' }}" 
           href="#" 
           wire:click.prevent="$set('activeTab', 'system')"
           role="tab">
            <i class="me-1 fas fa-info-circle"></i> System
        </a>
    </li>
</ul>