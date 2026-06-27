<?php

namespace App\Livewire;

use Livewire\Component;

class SortDay extends Component
{
    public $selected = 'Last 30 days';
    public $search = '';

    public function updatedSelected()
    {
        $this->dispatch('filter-changed', selected: $this->selected);
    }

    public function updatedSearch()
    {
        $this->dispatch('search-changed', search: $this->search);
    }
    public function render()
    {
        return view('livewire.sort-day');
    }
}
