@props(['name', 'show' => true, 'bag' => 'default'])

{{-- Mensaje de validación bajo un campo; "show" indica si los errores son de este formulario
     y "bag" la bolsa de errores (Fortify usa updateProfileInformation, updatePassword, etc.) --}}
@if ($show)
    @error($name, $bag)
        <span class="gf-form-error">{{ $message }}</span>
    @enderror
@endif
