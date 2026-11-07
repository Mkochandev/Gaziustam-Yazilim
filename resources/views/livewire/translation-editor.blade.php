<div>
<form wire:submit="save">
    {{ $this->form }}
    <button type="submit" wire:loading.attr="disabled">Kaydet</button>
</form>
<x-filament-actions::modals />
</div>

