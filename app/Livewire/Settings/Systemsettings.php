<?php

namespace App\Livewire\Settings;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SystemSetting;

class Systemsettings extends Component
{
    public $name, $title, $logo, $logoPreview, $buttonText = 'Submit';
    use WithFileUploads;
    public string $pageTitle = 'System Setting ';

    public function mount()
    {
        $system = SystemSetting::first();

        if ($system) {
            $this->name = $system->name;
            $this->title = $system->title;
            $this->logoPreview = $system->logo;
            $this->buttonText = 'Update';
        } else {
            $this->buttonText = 'Submit';
        }
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'logo' => 'nullable|mimes:png|max:1024',
        ]);

        $system = SystemSetting::first();
        $logoPath = $system?->logo;

        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
        }

        if ($system) {
            $system->update([
                'name' => $this->name,
                'title' => $this->title,
                'logo' => $logoPath,
            ]);
        } else {
            SystemSetting::create([
                'name' => $this->name,
                'title' => $this->title,
                'logo' => $logoPath,
            ]);
        }

        $this->dispatch('show-notification', 'System saved successfully!');
        $this->redirectRoute('settings.system', navigate: true);
    }

    public function render()
    {
        return view('livewire.settings.systemsettings')->layout('components.layouts.app', ['title'=>$this->pageTitle]);;
    }
}