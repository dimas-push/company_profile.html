document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
    const inputId = toggle.getAttribute('aria-controls');
    const passwordInput = document.getElementById(inputId);

    if (!passwordInput) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isVisible = passwordInput.type === 'password';
        const label = isVisible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi';

        passwordInput.type = isVisible ? 'text' : 'password';
        toggle.setAttribute('aria-label', label);
        toggle.setAttribute('title', label);
        toggle.setAttribute('aria-pressed', String(isVisible));
        toggle.querySelector('[data-eye-show]').hidden = isVisible;
        toggle.querySelector('[data-eye-hide]').hidden = !isVisible;
    });
});
