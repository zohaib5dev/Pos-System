<?php

namespace App\Livewire\Setting;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\BusinessSetting;
use App\Models\PaymentMethod;
use App\Models\ExpenseCategory;
use App\Models\Unit;
use App\Models\User;
use App\Models\TaxRate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithFileUploads;

    public $activeTab = 'general';

    // Business Settings
    public $business_name;
    public $business_email;
    public $business_phone;
    public $business_address;
    public $tax_number;
    public $registration_number;
    public $currency_code;
    public $currency_symbol;
    public $timezone;
    public $date_format;
    public $time_format;
    public $receipt_footer;
    public $logo;
    public $existing_logo;

    // Tax Settings
    public $taxRates = [];
    public $newTaxRate = ['name' => '', 'rate' => 0, 'is_default' => false, 'is_active' => true];
    public $editingTaxRateId = null;
    public $default_tax_rate;
    public $tax_inclusive = false;

    // Receipt Settings
    public $receipt_header;
    public $receipt_show_logo = true;
    public $receipt_show_address = true;
    public $receipt_show_phone = true;
    public $receipt_show_tax = true;
    public $receipt_show_discount = true;

    // Payment Methods
    public $paymentMethods = [];
    public $newPaymentMethod = ['name' => '', 'is_active' => true];
    public $editingPaymentMethodId = null;

    // Expense Categories
    public $expenseCategories = [];
    public $newExpenseCategory = ['name' => '', 'description' => '', 'is_active' => true];
    public $editingExpenseCategoryId = null;

    // Units
    public $units = [];
    public $newUnit = ['name' => '', 'short_name' => '', 'is_active' => true];
    public $editingUnitId = null;

    // Users
    public $users = [];
    public $showUserModal = false;
    public $editingUserId = null;
    public $userName = '';
    public $userEmail = '';
    public $userPassword = '';
    public $userPasswordConfirmation = '';
    public $userRole = '';
    public $userIsActive = true;
    public $roles = [];

    // Roles & Permissions
    public $rolesList = [];
    public $permissionsList = [];
    public $showRoleModal = false;
    public $editingRoleId = null;
    public $roleName = '';
    public $rolePermissions = [];
    public $showPermissionModal = false;
    public $editingPermissionId = null;
    public $permissionName = '';

    // Backup
    public $backupFiles = [];
    public $backupSchedule = 'daily';
    public $backupKeepDays = 30;
    public $showBackupModal = false;
    public $backupFileName = '';

    // System Info
    public $phpVersion;
    public $laravelVersion;
    public $mysqlVersion;
    public $serverSoftware;
    public $serverOS;

    // Confirmation Modals
    public $showRestoreModal = false;
    public $showDeleteModal = false;
    public $showDeletePaymentMethodModal = false;
    public $showDeleteExpenseCategoryModal = false;
    public $showDeleteUnitModal = false;
    public $showDeleteUserModal = false;
    public $showDeleteRoleModal = false;
    public $showDeletePermissionModal = false;
    public $showDeleteTaxRateModal = false;
    
    public $restoreFile = null;
    public $deleteFile = null;
    public $deleteItemId = null;
    public $deleteItemName = null;
    public $deleteType = null;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function mount($section = null)
    {
        if ($section) {
            $this->activeTab = $section;
        }

        $this->loadBusinessSettings();
        $this->loadTaxRates();
        $this->loadPaymentMethods();
        $this->loadExpenseCategories();
        $this->loadUnits();
        $this->loadUsers();
        $this->loadRolesAndPermissions();
        $this->loadBackupFiles();
        $this->loadSystemInfo();
    }

    public function loadBusinessSettings()
    {
        $settings = BusinessSetting::getSettings();

        $this->business_name = $settings->business_name;
        $this->business_email = $settings->business_email;
        $this->business_phone = $settings->business_phone;
        $this->business_address = $settings->business_address;
        $this->tax_number = $settings->tax_number;
        $this->registration_number = $settings->registration_number;
        $this->currency_code = $settings->currency_code;
        $this->currency_symbol = $settings->currency_symbol;
        $this->timezone = $settings->timezone;
        $this->date_format = $settings->date_format;
        $this->time_format = $settings->time_format;
        $this->receipt_footer = $settings->receipt_footer;
        $this->existing_logo = $settings->business_logo;
        
        // Load tax settings
        $this->default_tax_rate = $settings->default_tax_rate ?? 0;
        $this->tax_inclusive = $settings->tax_inclusive ?? false;
    }

    public function loadTaxRates()
    {
        $this->taxRates = TaxRate::withCount('orders')->get()->map(function ($tax) {
            return [
                'id' => $tax->id,
                'name' => $tax->name,
                'rate' => $tax->rate,
                'is_default' => $tax->is_default,
                'is_active' => $tax->is_active,
                'orders_count' => $tax->orders_count,
            ];
        })->toArray();
    }

    public function loadPaymentMethods()
    {
        $this->paymentMethods = PaymentMethod::all()->toArray();
    }

    public function loadExpenseCategories()
    {
        $this->expenseCategories = ExpenseCategory::all()->toArray();
    }

    public function loadUnits()
    {
        $this->units = Unit::all()->toArray();
    }

    public function loadUsers()
    {
        $this->users = User::with('roles')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name ?? 'No Role',
                'is_active' => $user->is_active,
                'last_login' => $user->last_login_at ? $user->last_login_at : 'Never',
            ];
        })->toArray();

        $this->roles = Role::all()->pluck('name', 'id')->toArray();
    }

    public function loadRolesAndPermissions()
    {
        $this->rolesList = Role::with('permissions')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions_count' => $role->permissions->count(),
                'users_count' => $role->users->count(),
            ];
        })->toArray();

        $this->permissionsList = Permission::all()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'roles_count' => $permission->roles->count(),
            ];
        })->toArray();
    }

    public function loadBackupFiles()
    {
        $backupPath = storage_path('app/backups');

        if (file_exists($backupPath)) {
            $sqlFiles = glob($backupPath . '/*.sql');
            $sqliteFiles = glob($backupPath . '/*.sqlite');
            $files = array_merge($sqlFiles, $sqliteFiles);

            $this->backupFiles = collect($files)->map(function ($file) {
                return [
                    'name' => basename($file),
                    'size' => filesize($file),
                    'size_formatted' => $this->formatBytes(filesize($file)),
                    'date' => filemtime($file),
                    'date_formatted' => date('Y-m-d H:i:s', filemtime($file)),
                    'extension' => pathinfo($file, PATHINFO_EXTENSION),
                ];
            })->sortByDesc('date')->values()->toArray();
        } else {
            $this->backupFiles = [];
        }
    }

    public function loadSystemInfo()
    {
        $this->phpVersion = phpversion();
        $this->laravelVersion = app()->version();

        try {
            $pdo = DB::connection()->getPdo();
            $this->mysqlVersion = $pdo->query('select version()')->fetchColumn();
        } catch (\Exception $e) {
            $this->mysqlVersion = 'Unknown';
        }

        $this->serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        $this->serverOS = PHP_OS;
    }

    public function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // ========== BUSINESS SETTINGS ==========
    
    public function saveBusinessSettings()
    {
        $this->validate([
            'business_name' => 'required|min:3',
            'business_email' => 'nullable|email',
            'business_phone' => 'nullable',
            'currency_code' => 'required|size:3',
            'currency_symbol' => 'required',
        ]);

        $settings = BusinessSetting::first();
        $oldData = collect($settings->toArray())->except(['updated_at'])->toArray();

        if ($this->logo) {
            $this->validate([
                'logo' => 'image|max:1024|mimes:jpeg,png,jpg,gif,svg',
            ]);

            $fileName = 'logo_' . time() . '.' . $this->logo->getClientOriginalExtension();
            $destinationPath = public_path('assets/img');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $sourcePath = $this->logo->getRealPath();
            $targetPath = $destinationPath . '/' . $fileName;

            if ($settings->business_logo && file_exists($destinationPath . '/' . $settings->business_logo)) {
                unlink($destinationPath . '/' . $settings->business_logo);
            }

            copy($sourcePath, $targetPath);
            $settings->business_logo = $fileName;
        }

        $settings->update([
            'business_name' => $this->business_name,
            'business_email' => $this->business_email,
            'business_phone' => $this->business_phone,
            'business_address' => $this->business_address,
            'tax_number' => $this->tax_number,
            'registration_number' => $this->registration_number,
            'currency_code' => $this->currency_code,
            'currency_symbol' => $this->currency_symbol,
            'timezone' => $this->timezone,
            'date_format' => $this->date_format,
            'time_format' => $this->time_format,
            'receipt_footer' => $this->receipt_footer,
        ]);

        $this->existing_logo = $settings->business_logo;
        logActivity('updated', $settings, $oldData, $settings->toArray());

        $this->dispatch('notify', [
            'message' => 'Business settings saved successfully',
            'type' => 'success'
        ]);
    }

    // ========== TAX RATES ==========
    
    public function saveTaxRate()
    {
        $this->validate([
            'newTaxRate.name' => 'required|min:2|unique:tax_rates,name',
            'newTaxRate.rate' => 'required|numeric|min:0|max:100',
        ]);

        try {
            if ($this->newTaxRate['is_default']) {
                TaxRate::where('is_default', true)->update(['is_default' => false]);
            }

            $tax = TaxRate::create([
                'name' => $this->newTaxRate['name'],
                'rate' => $this->newTaxRate['rate'],
                'is_active' => $this->newTaxRate['is_active'],
                'is_default' => $this->newTaxRate['is_default'],
            ]);

            $this->resetNewTaxRate();
            $this->loadTaxRates();

            $this->dispatch('notify', [
                'message' => 'Tax rate added successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error adding tax rate: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function editTaxRate($id)
    {
        $tax = TaxRate::find($id);
        if ($tax) {
            $this->newTaxRate = [
                'name' => $tax->name,
                'rate' => $tax->rate,
                'is_active' => $tax->is_active,
                'is_default' => $tax->is_default,
            ];
            $this->editingTaxRateId = $id;
        }
    }

    public function updateTaxRate()
    {
        $this->validate([
            'newTaxRate.name' => 'required|min:2|unique:tax_rates,name,' . $this->editingTaxRateId,
            'newTaxRate.rate' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $tax = TaxRate::find($this->editingTaxRateId);

            if ($this->newTaxRate['is_default'] && !$tax->is_default) {
                TaxRate::where('is_default', true)->update(['is_default' => false]);
            }
            if (!$this->newTaxRate['is_default'] && $tax->is_default) {
                $otherTax = TaxRate::where('id', '!=', $tax->id)->where('is_active', true)->first();
                if ($otherTax) {
                    $otherTax->update(['is_default' => true]);
                }
            }

            $tax->update([
                'name' => $this->newTaxRate['name'],
                'rate' => $this->newTaxRate['rate'],
                'is_active' => $this->newTaxRate['is_active'],
                'is_default' => $this->newTaxRate['is_default'],
            ]);

            $this->cancelEditTaxRate();
            $this->loadTaxRates();

            $this->dispatch('notify', [
                'message' => 'Tax rate updated successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error updating tax rate: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function cancelEditTaxRate()
    {
        $this->resetNewTaxRate();
        $this->editingTaxRateId = null;
    }

    public function resetNewTaxRate()
    {
        $this->newTaxRate = ['name' => '', 'rate' => 0, 'is_active' => true, 'is_default' => false];
    }

    public function setDefaultTaxRate($id)
    {
        try {
            TaxRate::where('is_default', true)->update(['is_default' => false]);

            $tax = TaxRate::find($id);
            $tax->update(['is_default' => true]);

            $this->loadTaxRates();

            $this->dispatch('notify', [
                'message' => 'Default tax rate updated successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error setting default tax rate: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function toggleTaxRateStatus($id)
    {
        $tax = TaxRate::find($id);

        if ($tax->is_default && $tax->is_active) {
            $this->dispatch('notify', [
                'message' => 'Cannot deactivate default tax rate. Set another tax as default first.',
                'type' => 'error'
            ]);
            return;
        }

        $tax->update(['is_active' => !$tax->is_active]);
        $this->loadTaxRates();

        $this->dispatch('notify', [
            'message' => 'Tax rate status updated successfully',
            'type' => 'success'
        ]);
    }

    public function saveTaxSettings()
    {
        $this->validate([
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $settings = BusinessSetting::first();
        $oldData = collect($settings->toArray())->except(['updated_at'])->toArray();

        $settings->update([
            'default_tax_rate' => $this->default_tax_rate,
            'tax_inclusive' => $this->tax_inclusive,
        ]);

        if ($this->default_tax_rate > 0) {
            TaxRate::where('rate', $this->default_tax_rate)
                ->orWhere('is_default', true)
                ->update(['is_default' => false]);

            $defaultTax = TaxRate::where('rate', $this->default_tax_rate)->first();
            if ($defaultTax) {
                $defaultTax->update(['is_default' => true]);
            }
        }

        logActivity('updated', $settings, $oldData, $settings->toArray());

        $this->loadTaxRates();

        $this->dispatch('notify', [
            'message' => 'Tax settings saved successfully',
            'type' => 'success'
        ]);
    }

    // ========== PAYMENT METHODS ==========
    
    public function addPaymentMethod()
    {
        $this->validate([
            'newPaymentMethod.name' => 'required|min:2|unique:payment_methods,name',
        ]);

        $method = PaymentMethod::create([
            'name' => $this->newPaymentMethod['name'],
            'slug' => \Str::slug($this->newPaymentMethod['name']),
            'is_active' => $this->newPaymentMethod['is_active'],
        ]);

        $this->newPaymentMethod = ['name' => '', 'is_active' => true];
        $this->loadPaymentMethods();

        logActivity('created', $method, [], $method->toArray());

        $this->dispatch('notify', [
            'message' => 'Payment method added successfully',
            'type' => 'success'
        ]);
    }

    public function editPaymentMethod($id)
    {
        $method = PaymentMethod::find($id);
        $this->newPaymentMethod = [
            'name' => $method->name,
            'is_active' => $method->is_active,
        ];
        $this->editingPaymentMethodId = $id;
    }

    public function updatePaymentMethod()
    {
        $this->validate([
            'newPaymentMethod.name' => 'required|min:2|unique:payment_methods,name,' . $this->editingPaymentMethodId,
        ]);

        $method = PaymentMethod::find($this->editingPaymentMethodId);
        $oldData = collect($method->toArray())->except(['updated_at'])->toArray();

        $method->update([
            'name' => $this->newPaymentMethod['name'],
            'slug' => \Str::slug($this->newPaymentMethod['name']),
            'is_active' => $this->newPaymentMethod['is_active'],
        ]);

        $this->cancelEditPaymentMethod();
        $this->loadPaymentMethods();

        logActivity('updated', $method, $oldData, $method->toArray());

        $this->dispatch('notify', [
            'message' => 'Payment method updated successfully',
            'type' => 'success'
        ]);
    }

    public function cancelEditPaymentMethod()
    {
        $this->newPaymentMethod = ['name' => '', 'is_active' => true];
        $this->editingPaymentMethodId = null;
    }

    // ========== EXPENSE CATEGORIES ==========
    
    public function addExpenseCategory()
    {
        $this->validate([
            'newExpenseCategory.name' => 'required|min:2|unique:expense_categories,name',
        ]);

        $category = ExpenseCategory::create([
            'name' => $this->newExpenseCategory['name'],
            'description' => $this->newExpenseCategory['description'],
            'is_active' => $this->newExpenseCategory['is_active'],
        ]);

        $this->newExpenseCategory = ['name' => '', 'description' => '', 'is_active' => true];
        $this->loadExpenseCategories();

        logActivity('created', $category, [], $category->toArray());

        $this->dispatch('notify', [
            'message' => 'Expense category added successfully',
            'type' => 'success'
        ]);
    }

    public function editExpenseCategory($id)
    {
        $category = ExpenseCategory::find($id);
        $this->newExpenseCategory = [
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->is_active,
        ];
        $this->editingExpenseCategoryId = $id;
    }

    public function updateExpenseCategory()
    {
        $this->validate([
            'newExpenseCategory.name' => 'required|min:2|unique:expense_categories,name,' . $this->editingExpenseCategoryId,
        ]);

        $category = ExpenseCategory::find($this->editingExpenseCategoryId);
        $oldData = collect($category->toArray())->except(['updated_at'])->toArray();

        $category->update([
            'name' => $this->newExpenseCategory['name'],
            'description' => $this->newExpenseCategory['description'],
            'is_active' => $this->newExpenseCategory['is_active'],
        ]);

        $this->cancelEditExpenseCategory();
        $this->loadExpenseCategories();

        logActivity('updated', $category, $oldData, $category->toArray());

        $this->dispatch('notify', [
            'message' => 'Expense category updated successfully',
            'type' => 'success'
        ]);
    }

    public function cancelEditExpenseCategory()
    {
        $this->newExpenseCategory = ['name' => '', 'description' => '', 'is_active' => true];
        $this->editingExpenseCategoryId = null;
    }

    // ========== UNITS ==========
    
    public function addUnit()
    {
        $this->validate([
            'newUnit.name' => 'required|min:1|unique:units,name',
            'newUnit.short_name' => 'required|min:1|max:10',
        ]);

        $unit = Unit::create([
            'name' => $this->newUnit['name'],
            'short_name' => $this->newUnit['short_name'],
            'is_active' => $this->newUnit['is_active'],
        ]);

        $this->newUnit = ['name' => '', 'short_name' => '', 'is_active' => true];
        $this->loadUnits();

        logActivity('created', $unit, [], $unit->toArray());

        $this->dispatch('notify', [
            'message' => 'Unit added successfully',
            'type' => 'success'
        ]);
    }

    public function editUnit($id)
    {
        $unit = Unit::find($id);
        $this->newUnit = [
            'name' => $unit->name,
            'short_name' => $unit->short_name,
            'is_active' => $unit->is_active,
        ];
        $this->editingUnitId = $id;
    }

    public function updateUnit()
    {
        $this->validate([
            'newUnit.name' => 'required|min:1|unique:units,name,' . $this->editingUnitId,
            'newUnit.short_name' => 'required|min:1|max:10',
        ]);

        $unit = Unit::find($this->editingUnitId);
        $oldData = collect($unit->toArray())->except(['updated_at'])->toArray();

        $unit->update([
            'name' => $this->newUnit['name'],
            'short_name' => $this->newUnit['short_name'],
            'is_active' => $this->newUnit['is_active'],
        ]);

        $this->cancelEditUnit();
        $this->loadUnits();

        logActivity('updated', $unit, $oldData, $unit->toArray());

        $this->dispatch('notify', [
            'message' => 'Unit updated successfully',
            'type' => 'success'
        ]);
    }

    public function cancelEditUnit()
    {
        $this->newUnit = ['name' => '', 'short_name' => '', 'is_active' => true];
        $this->editingUnitId = null;
    }

    // ========== USERS ==========
    
    public function openUserModal($id = null)
    {
        $this->resetUserForm();

        if ($id) {
            $user = User::find($id);
            if ($user) {
                $this->editingUserId = $user->id;
                $this->userName = $user->name;
                $this->userEmail = $user->email;
                $this->userIsActive = $user->is_active;

                $firstRole = $user->roles->first();
                $this->userRole = $firstRole ? $firstRole->id : '';
            }
        }

        $this->showUserModal = true;
    }

    public function resetUserForm()
    {
        $this->reset([
            'editingUserId',
            'userName',
            'userEmail',
            'userPassword',
            'userPasswordConfirmation',
            'userRole',
            'userIsActive'
        ]);
        $this->userIsActive = true;
    }

    public function saveUser()
    {
        $rules = [
            'userName' => 'required|min:3',
            'userEmail' => 'required|email|unique:users,email,' . $this->editingUserId,
            'userRole' => 'required',
        ];

        if (!$this->editingUserId) {
            $rules['userPassword'] = 'required|min:8|same:userPasswordConfirmation';
        } elseif ($this->userPassword) {
            $rules['userPassword'] = 'min:8|same:userPasswordConfirmation';
        }

        $this->validate($rules);

        try {
            $userData = [
                'name' => $this->userName,
                'email' => $this->userEmail,
                'is_active' => $this->userIsActive,
            ];

            if ($this->userPassword) {
                $userData['password'] = Hash::make($this->userPassword);
            }

            if ($this->editingUserId) {
                $user = User::find($this->editingUserId);
                $oldData = collect($user->toArray())->except(['updated_at', 'password'])->toArray();
                $user->update($userData);

                if ($this->userRole) {
                    $role = Role::findById($this->userRole);
                    if ($role) {
                        $user->syncRoles([$role->name]);
                    }
                }

                logActivity('updated', $user, $oldData, $user->toArray());

                $this->dispatch('notify', [
                    'message' => 'User updated successfully',
                    'type' => 'success'
                ]);
            } else {
                $userData['password'] = Hash::make($this->userPassword);
                $user = User::create($userData);

                if ($this->userRole) {
                    $role = Role::findById($this->userRole);
                    if ($role) {
                        $user->assignRole($role->name);
                    }
                }

                logActivity('created', $user, [], $user->toArray());

                $this->dispatch('notify', [
                    'message' => 'User created successfully',
                    'type' => 'success'
                ]);
            }

            $this->showUserModal = false;
            $this->loadUsers();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error saving user: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            Log::error('User save error: ' . $e->getMessage());
        }
    }

    // ========== ROLES & PERMISSIONS ==========
    
    public function openRoleModal($id = null)
    {
        $this->resetRoleForm();

        if ($id) {
            try {
                $role = Role::with('permissions')->find($id);
                if ($role) {
                    $this->editingRoleId = $role->id;
                    $this->roleName = $role->name;
                    $this->rolePermissions = $role->permissions->pluck('name')->toArray();
                }
            } catch (\Exception $e) {
                \Log::error('Error loading role', ['error' => $e->getMessage()]);
                $this->dispatch('notify', [
                    'message' => 'Error loading role: ' . $e->getMessage(),
                    'type' => 'error'
                ]);
                return;
            }
        }

        $this->showRoleModal = true;
    }

    public function resetRoleForm()
    {
        $this->reset(['editingRoleId', 'roleName', 'rolePermissions']);
    }

    public function saveRole()
    {
        $this->validate([
            'roleName' => 'required|unique:roles,name,' . $this->editingRoleId,
        ]);

        try {
            if ($this->editingRoleId) {
                $role = Role::findById($this->editingRoleId);
                $oldData = collect($role->toArray())->except(['updated_at'])->toArray();
                $role->name = $this->roleName;
                $role->save();

                $role->syncPermissions($this->rolePermissions);

                logActivity('updated', $role, $oldData, $role->toArray());

                $this->dispatch('notify', [
                    'message' => 'Role updated successfully',
                    'type' => 'success'
                ]);
            } else {
                $role = Role::create(['name' => $this->roleName]);
                $role->syncPermissions($this->rolePermissions);

                logActivity('created', $role, [], $role->toArray());

                $this->dispatch('notify', [
                    'message' => 'Role created successfully',
                    'type' => 'success'
                ]);
            }

            $this->showRoleModal = false;
            $this->loadRolesAndPermissions();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error saving role: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            Log::error('Role save error: ' . $e->getMessage());
        }
    }

    public function openPermissionModal($id = null)
    {
        $this->resetPermissionForm();

        if ($id) {
            $permission = Permission::findById($id);
            if ($permission) {
                $this->editingPermissionId = $permission->id;
                $this->permissionName = $permission->name;
            }
        }

        $this->showPermissionModal = true;
    }

    public function resetPermissionForm()
    {
        $this->reset(['editingPermissionId', 'permissionName']);
    }

    public function savePermission()
    {
        $this->validate([
            'permissionName' => 'required|unique:permissions,name,' . $this->editingPermissionId,
        ]);

        try {
            if ($this->editingPermissionId) {
                $permission = Permission::findById($this->editingPermissionId);
                $oldData = collect($permission->toArray())->except(['updated_at'])->toArray();
                $permission->name = $this->permissionName;
                $permission->save();

                logActivity('updated', $permission, $oldData, $permission->toArray());

                $this->dispatch('notify', [
                    'message' => 'Permission updated successfully',
                    'type' => 'success'
                ]);
            } else {
                $permission = Permission::create(['name' => $this->permissionName]);

                logActivity('created', $permission, [], $permission->toArray());

                $this->dispatch('notify', [
                    'message' => 'Permission created successfully',
                    'type' => 'success'
                ]);
            }

            $this->showPermissionModal = false;
            $this->loadRolesAndPermissions();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error saving permission: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            Log::error('Permission save error: ' . $e->getMessage());
        }
    }

    // ========== BACKUPS ==========
    
    public function createBackup()
    {
        $this->validate([
            'backupFileName' => 'nullable|regex:/^[a-zA-Z0-9\-_]+$/',
        ]);

        try {
            $fileName = $this->backupFileName ?: 'backup-' . date('Y-m-d-H-i-s');
            $backupPath = storage_path('app/backups');

            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $connection = config('database.default');
            $extension = ($connection === 'sqlite') ? '.sqlite' : '.sql';
            $backupFile = $backupPath . '/' . $fileName . $extension;

            if ($connection === 'mysql') {
                $this->createMySQLBackup($backupFile, $fileName);
            } elseif ($connection === 'sqlite') {
                $this->createSQLiteBackup($backupFile, $fileName);
            } else {
                throw new \Exception('Unsupported database connection: ' . $connection);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error creating backup: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            \Log::error('Backup error: ' . $e->getMessage());
        }
    }

    private function createMySQLBackup($backupFile, $fileName)
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', '3306');

        if (!empty($password)) {
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > "%s" 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                $backupFile
            );
        } else {
            $command = sprintf(
                'mysqldump --user=%s --host=%s --port=%s %s > "%s" 2>&1',
                escapeshellarg($username),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                $backupFile
            );
        }

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($backupFile) && filesize($backupFile) > 0) {
            $this->showBackupModal = false;
            $this->backupFileName = '';
            $this->loadBackupFiles();

            logActivity('backup_created', new BusinessSetting(), [], ['filename' => $fileName]);

            $this->dispatch('notify', [
                'message' => 'Backup created successfully',
                'type' => 'success'
            ]);
        } else {
            $this->createBackupAlternative($database, $username, $password, $host, $backupFile, $fileName);
        }
    }

    private function createBackupAlternative($database, $username, $password, $host, $backupFile, $fileName)
    {
        try {
            $pdo = new \PDO("mysql:host=$host;dbname=$database", $username, $password);
            $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

            $handle = fopen($backupFile, 'w');

            foreach ($tables as $table) {
                $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(\PDO::FETCH_ASSOC);
                fwrite($handle, "\n\n" . $createTable['Create Table'] . ";\n\n");

                $rows = $pdo->query("SELECT * FROM `$table`");

                while ($row = $rows->fetch(\PDO::FETCH_ASSOC)) {
                    $values = array_map(function ($value) use ($pdo) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return $pdo->quote($value);
                    }, array_values($row));

                    $insert = "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                    fwrite($handle, $insert);
                }
            }

            fclose($handle);

            if (file_exists($backupFile) && filesize($backupFile) > 0) {
                $this->showBackupModal = false;
                $this->backupFileName = '';
                $this->loadBackupFiles();

                logActivity('backup_created', new BusinessSetting(), [], ['filename' => $fileName, 'method' => 'php_fallback']);

                $this->dispatch('notify', [
                    'message' => 'Backup created successfully (using PHP fallback)',
                    'type' => 'success'
                ]);
            } else {
                throw new \Exception('Backup file is empty');
            }
        } catch (\Exception $e) {
            throw new \Exception('Both backup methods failed: ' . $e->getMessage());
        }
    }

    private function createSQLiteBackup($backupFile, $fileName)
    {
        $sqlitePath = database_path('database.sqlite');

        if (file_exists($sqlitePath)) {
            if (copy($sqlitePath, $backupFile)) {
                $this->showBackupModal = false;
                $this->backupFileName = '';
                $this->loadBackupFiles();

                logActivity('backup_created', new BusinessSetting(), [], ['filename' => $fileName]);

                $this->dispatch('notify', [
                    'message' => 'Backup created successfully',
                    'type' => 'success'
                ]);
            } else {
                throw new \Exception('Failed to copy SQLite database file');
            }
        } else {
            throw new \Exception('SQLite database file not found');
        }
    }

    public function restoreBackup($file)
    {
        try {
            $backupFile = storage_path('app/backups/' . $file);

            if (!file_exists($backupFile)) {
                throw new \Exception('Backup file not found');
            }

            $extension = pathinfo($backupFile, PATHINFO_EXTENSION);
            $connection = config('database.default');

            if ($extension === 'sqlite' && $connection === 'sqlite') {
                $this->restoreSQLiteBackup($backupFile, $file);
            } elseif ($extension === 'sql' && $connection === 'mysql') {
                $this->restoreMySQLBackup($backupFile, $file);
            } else {
                throw new \Exception('Cannot restore ' . strtoupper($extension) . ' file to ' . strtoupper($connection) . ' database.');
            }

            $this->showRestoreModal = false;
            $this->restoreFile = null;

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error restoring backup: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            \Log::error('Restore error: ' . $e->getMessage());
        }
    }

    protected function restoreSQLiteBackup($backupFile, $fileName)
    {
        try {
            $currentDbPath = database_path('database.sqlite');
            
            $backupCurrentPath = database_path('database_backup_' . date('Y-m-d-H-i-s') . '.sqlite');
            if (file_exists($currentDbPath)) {
                copy($currentDbPath, $backupCurrentPath);
            }

            if (copy($backupFile, $currentDbPath)) {
                logActivity('backup_restored', new BusinessSetting(), [], ['filename' => $fileName]);

                $this->dispatch('notify', [
                    'message' => 'SQLite database restored successfully from: ' . $fileName,
                    'type' => 'success'
                ]);
                
                $this->loadBackupFiles();
            } else {
                throw new \Exception('Failed to restore SQLite database');
            }

        } catch (\Exception $e) {
            throw new \Exception('SQLite restore failed: ' . $e->getMessage());
        }
    }

    protected function restoreMySQLBackup($backupFile, $fileName)
    {
        try {
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', '3306');

            if (!empty($password)) {
                $command = sprintf(
                    'mysql --user=%s --password=%s --host=%s --port=%s %s < "%s" 2>&1',
                    escapeshellarg($username),
                    escapeshellarg($password),
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($database),
                    $backupFile
                );
            } else {
                $command = sprintf(
                    'mysql --user=%s --host=%s --port=%s %s < "%s" 2>&1',
                    escapeshellarg($username),
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($database),
                    $backupFile
                );
            }

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                logActivity('backup_restored', new BusinessSetting(), [], ['filename' => $fileName]);

                $this->dispatch('notify', [
                    'message' => 'MySQL database restored successfully from: ' . $fileName,
                    'type' => 'success'
                ]);
                
                $this->loadBackupFiles();
            } else {
                throw new \Exception('Restore failed: ' . implode("\n", $output));
            }

        } catch (\Exception $e) {
            throw new \Exception('MySQL restore failed: ' . $e->getMessage());
        }
    }

    public function downloadBackup($file)
    {
        $backupFile = storage_path('app/backups/' . $file);

        if (!file_exists($backupFile)) {
            $this->dispatch('notify', [
                'message' => 'Backup file not found',
                'type' => 'error'
            ]);
            return;
        }

        logActivity('backup_downloaded', new BusinessSetting(), [], ['filename' => $file]);

        return response()->download($backupFile, $file);
    }

    // ========== CONFIRMATION MODAL METHODS ==========
    
    public function confirmRestore($file)
    {
        $this->restoreFile = $file;
        $this->showRestoreModal = true;
    }

    public function confirmDeleteBackup($file)
    {
        $this->deleteFile = $file;
        $this->deleteType = 'backup';
        $this->showDeleteModal = true;
    }

    public function confirmDeletePaymentMethod($id)
    {
        $method = PaymentMethod::find($id);
        if ($method) {
            $this->deleteItemId = $id;
            $this->deleteItemName = $method->name;
            $this->deleteType = 'payment_method';
            $this->showDeletePaymentMethodModal = true;
        }
    }

    public function confirmDeleteExpenseCategory($id)
    {
        $category = ExpenseCategory::find($id);
        if ($category) {
            $this->deleteItemId = $id;
            $this->deleteItemName = $category->name;
            $this->deleteType = 'expense_category';
            $this->showDeleteExpenseCategoryModal = true;
        }
    }

    public function confirmDeleteUnit($id)
    {
        $unit = Unit::find($id);
        if ($unit) {
            $this->deleteItemId = $id;
            $this->deleteItemName = $unit->name;
            $this->deleteType = 'unit';
            $this->showDeleteUnitModal = true;
        }
    }

    public function confirmDeleteUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $this->deleteItemId = $id;
            $this->deleteItemName = $user->name;
            $this->deleteType = 'user';
            $this->showDeleteUserModal = true;
        }
    }

    public function confirmDeleteRole($id)
    {
        try {
            $role = Role::findById($id);
            if ($role) {
                $this->deleteItemId = $id;
                $this->deleteItemName = $role->name;
                $this->deleteType = 'role';
                $this->showDeleteRoleModal = true;
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function confirmDeletePermission($id)
    {
        try {
            $permission = Permission::findById($id);
            if ($permission) {
                $this->deleteItemId = $id;
                $this->deleteItemName = $permission->name;
                $this->deleteType = 'permission';
                $this->showDeletePermissionModal = true;
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function confirmDeleteTaxRate($id)
    {
        $tax = TaxRate::find($id);
        if ($tax) {
            $this->deleteItemId = $id;
            $this->deleteItemName = $tax->name;
            $this->deleteType = 'tax_rate';
            $this->showDeleteTaxRateModal = true;
        }
    }

    // ========== EXECUTE DELETE METHODS ==========

    public function executeDelete()
    {
        switch ($this->deleteType) {
            case 'backup':
                $this->deleteBackup($this->deleteFile);
                break;
            case 'payment_method':
                $this->deletePaymentMethod($this->deleteItemId);
                break;
            case 'expense_category':
                $this->deleteExpenseCategory($this->deleteItemId);
                break;
            case 'unit':
                $this->deleteUnit($this->deleteItemId);
                break;
            case 'user':
                $this->deleteUser($this->deleteItemId);
                break;
            case 'role':
                $this->deleteRole($this->deleteItemId);
                break;
            case 'permission':
                $this->deletePermission($this->deleteItemId);
                break;
            case 'tax_rate':
                $this->deleteTaxRate($this->deleteItemId);
                break;
        }
        
        $this->closeAllModals();
    }

    public function closeAllModals()
    {
        $this->showRestoreModal = false;
        $this->showDeleteModal = false;
        $this->showDeletePaymentMethodModal = false;
        $this->showDeleteExpenseCategoryModal = false;
        $this->showDeleteUnitModal = false;
        $this->showDeleteUserModal = false;
        $this->showDeleteRoleModal = false;
        $this->showDeletePermissionModal = false;
        $this->showDeleteTaxRateModal = false;
        $this->deleteItemId = null;
        $this->deleteItemName = null;
        $this->deleteType = null;
        $this->restoreFile = null;
    }

    // ========== DELETE METHODS ==========

    public function deleteBackup($file)
    {
        $backupFile = storage_path('app/backups/' . $file);

        if (file_exists($backupFile)) {
            unlink($backupFile);
            $this->loadBackupFiles();

            logActivity('backup_deleted', new BusinessSetting(), [], ['filename' => $file]);
            $this->showDeleteModal = false;
            $this->dispatch('notify', [
                'message' => 'Backup deleted successfully',
                'type' => 'success'
            ]);
        }
    }

    public function deletePaymentMethod($id)
    {
        $method = PaymentMethod::find($id);

        if ($method->payments()->count() > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete payment method with payments',
                'type' => 'error'
            ]);
            return;
        }

        $methodData = collect($method->toArray())->except(['updated_at'])->toArray();
        $method->delete();
        $this->loadPaymentMethods();

        logActivity('deleted', $method, $methodData, []);

        $this->dispatch('notify', [
            'message' => 'Payment method deleted successfully',
            'type' => 'success'
        ]);
    }

    public function deleteExpenseCategory($id)
    {
        $category = ExpenseCategory::find($id);

        if ($category->expenses()->count() > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete category with expenses',
                'type' => 'error'
            ]);
            return;
        }

        $categoryData = collect($category->toArray())->except(['updated_at'])->toArray();
        $category->delete();
        $this->loadExpenseCategories();

        logActivity('deleted', $category, $categoryData, []);

        $this->dispatch('notify', [
            'message' => 'Expense category deleted successfully',
            'type' => 'success'
        ]);
    }

    public function deleteUnit($id)
    {
        $unit = Unit::find($id);

        if ($unit->products()->count() > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete unit used by products',
                'type' => 'error'
            ]);
            return;
        }

        $unitData = collect($unit->toArray())->except(['updated_at'])->toArray();
        $unit->delete();
        $this->loadUnits();

        logActivity('deleted', $unit, $unitData, []);

        $this->dispatch('notify', [
            'message' => 'Unit deleted successfully',
            'type' => 'success'
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if ($user->id === auth()->id()) {
            $this->dispatch('notify', [
                'message' => 'You cannot delete your own account',
                'type' => 'error'
            ]);
            return;
        }

        $userData = collect($user->toArray())->except(['updated_at'])->toArray();
        $user->delete();
        $this->loadUsers();

        logActivity('deleted', $user, $userData, []);

        $this->dispatch('notify', [
            'message' => 'User deleted successfully',
            'type' => 'success'
        ]);
    }

    public function deleteRole($id)
    {
        try {
            $role = Role::findById($id);

            if ($role->name === 'Super Admin') {
                $this->dispatch('notify', [
                    'message' => 'Cannot delete Super Admin role',
                    'type' => 'error'
                ]);
                return;
            }

            $roleData = collect($role->toArray())->except(['updated_at'])->toArray();
            $role->delete();
            $this->loadRolesAndPermissions();

            logActivity('deleted', $role, $roleData, []);

            $this->dispatch('notify', [
                'message' => 'Role deleted successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error deleting role: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function deletePermission($id)
    {
        try {
            $permission = Permission::findById($id);

            if ($permission->roles()->count() > 0) {
                $this->dispatch('notify', [
                    'message' => 'Cannot delete permission that is assigned to roles',
                    'type' => 'error'
                ]);
                return;
            }

            $permissionData = collect($permission->toArray())->except(['updated_at'])->toArray();
            $permission->delete();
            $this->loadRolesAndPermissions();

            logActivity('deleted', $permission, $permissionData, []);

            $this->dispatch('notify', [
                'message' => 'Permission deleted successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error deleting permission: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function deleteTaxRate($id)
    {
        $tax = TaxRate::withCount('orders')->find($id);

        if ($tax->orders_count > 0) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete tax rate that is used in orders',
                'type' => 'error'
            ]);
            return;
        }

        if ($tax->is_default) {
            $this->dispatch('notify', [
                'message' => 'Cannot delete default tax rate. Set another tax as default first.',
                'type' => 'error'
            ]);
            return;
        }

        $tax->delete();
        $this->loadTaxRates();

        $this->dispatch('notify', [
            'message' => 'Tax rate deleted successfully',
            'type' => 'success'
        ]);
    }

    // ========== SYSTEM TOOLS ==========
    
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            logActivity('cache_cleared', new BusinessSetting(), [], []);

            $this->dispatch('notify', [
                'message' => 'Cache cleared successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error clearing cache: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            Log::error('Cache clear error: ' . $e->getMessage());
        }
    }

    public function optimizeApplication()
    {
        try {
            Artisan::call('optimize');

            logActivity('application_optimized', new BusinessSetting(), [], []);

            $this->dispatch('notify', [
                'message' => 'Application optimized successfully',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error optimizing application: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            Log::error('Optimize error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.settings.index')->layout('layouts.app');
    }
}