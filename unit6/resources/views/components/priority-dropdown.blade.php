@props(['priority' => 3, 'name' => 'priority', 'disabled' => false])

<select 
    name="{{ $name }}" 
    {{ $disabled ? 'disabled' : '' }}
    @class([
        'form-select',
        'priority-dropdown',
        'priority-1' => $priority == 1,
        'priority-2' => $priority == 2,
        'priority-3' => $priority == 3,
        'priority-4' => $priority == 4,
        'priority-5' => $priority == 5,
    ])
    style="
        padding: 0.5rem;
        border-radius: 4px;
        border: 2px solid #dee2e6;
        font-weight: bold;
        @if($priority == 1) background-color: #dc3545; color: white; @endif
        @if($priority == 2) background-color: #fd7e14; color: white; @endif
        @if($priority == 3) background-color: #ffc107; color: #333; @endif
        @if($priority == 4) background-color: #28a745; color: white; @endif
        @if($priority == 5) background-color: #6c757d; color: white; @endif
    "
>
    <option value="1" @selected($priority == 1) style="background-color: #dc3545; color: white;">🔴 Critical (1)</option>
    <option value="2" @selected($priority == 2) style="background-color: #fd7e14; color: white;">🟠 High (2)</option>
    <option value="3" @selected($priority == 3) style="background-color: #ffc107; color: #333;">🟡 Medium (3)</option>
    <option value="4" @selected($priority == 4) style="background-color: #28a745; color: white;">🟢 Low (4)</option>
    <option value="5" @selected($priority == 5) style="background-color: #6c757d; color: white;">⚫ Minimal (5)</option>
</select>

<style>
    .priority-dropdown {
        transition: all 0.3s ease;
    }
    
    .priority-dropdown:hover {
        border-color: #495057;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    }
    
    .priority-dropdown:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }
</style>
