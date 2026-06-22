<?php

namespace App\Livewire\Layout;

use Livewire\Component;

class Sidebar extends Component
{
    public $isCollapsed = false;
    public $isMobileOpen = false;
    
    protected $listeners = [
        'toggle-sidebar' => 'toggleSidebar',
        'toggle-sidebar-mobile' => 'toggleMobileSidebar',
        'close-sidebar' => 'closeSidebar'
    ];
    
    public function toggleSidebar()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }
    
    public function toggleMobileSidebar()
    {
        $this->isMobileOpen = !$this->isMobileOpen;
    }
    
    public function closeSidebar()
    {
        $this->isMobileOpen = false;
    }
    
    public function render()
    {
        return view('livewire.layout.sidebar');
    }
}