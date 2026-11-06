document.querySelectorAll('[data-dirty]').forEach(form => {
    let dirty = false;
    form.addEventListener('input', () => { dirty = true });
    form.addEventListener('submit', () => { dirty = false });
    window.addEventListener('beforeunload', event => {
        if (dirty) { event.preventDefault(); event.returnValue = ''; }
    });
});
