// Mostrar / ocultar contraseña con el ícono de ojo dentro del campo.
// Uso: <button type="button" class="auth-eye" data-toggle-password="idDelInput">
document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
    const input = document.getElementById(btn.dataset.togglePassword);
    const icon = btn.querySelector('i');
    if (!input) return;

    btn.addEventListener('click', function () {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';

        icon.classList.toggle('bi-eye', !show);
        icon.classList.toggle('bi-eye-slash', show);

        const label = show ? 'Ocultar contraseña' : 'Mostrar contraseña';
        btn.setAttribute('aria-label', label);
        btn.setAttribute('title', label);
        btn.setAttribute('aria-pressed', String(show));

        // Mantiene el cursor en el campo para seguir escribiendo
        input.focus();
    });
});
