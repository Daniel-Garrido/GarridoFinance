@props(['name', 'show' => true])

{{-- Mensaje de validación bajo un campo; "show" indica si los errores son de este formulario --}}
@if ($show)
    @error($name)
        <span class="gf-form-error">{{ $message }}</span>
    @enderror
@endif
