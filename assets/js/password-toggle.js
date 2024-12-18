document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', () => {
        const target = document.getElementById(button.dataset.target);
        if (target.type === 'password') {
            target.type = 'text';
            button.textContent = '🙈';
        } else {
            target.type = 'password';
            button.textContent = '👁️';
        }
    });
}); 