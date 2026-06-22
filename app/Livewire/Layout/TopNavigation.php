<?php

namespace App\Livewire\Layout;

use Livewire\Component;

class TopNavigation extends Component
{
    public $search = '';
    public $darkMode = false;
    public $showNotifications = false;
    public $showUserMenu = false;
    public $showMobileSearch = false;
    
    protected $listeners = [
        'dark-mode-changed' => 'updateDarkMode'
    ];
    
    public function mount()
    {
        // Check both session and localStorage via JavaScript
        $this->dispatch('check-dark-mode');
    }
    
    public function updateDarkMode($darkMode)
    {
        $this->darkMode = $darkMode;
    }
    
    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        session(['dark_mode' => $this->darkMode]);
        
        // Dispatch to update HTML class and localStorage
        $this->dispatch('dark-mode-toggled', darkMode: $this->darkMode);
    }
    
    public function updatedSearch()
    {
        $this->dispatch('global-search', search: $this->search);
    }
    
    public function render()
    {
        return view('livewire.layout.top-navigation');
    }
}