document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('serviceForm');

    if (!form) {
        return;
    }

    form.addEventListener('submit', function (event) {
        const title = document.getElementById('title');
        const description = document.getElementById('description');
        const price = document.getElementById('price');
        const errors = [];

        if (!title.value.trim()) {
            errors.push('Tajuk service wajib diisi.');
        }

        if (!description.value.trim()) {
            errors.push('Penerangan service wajib diisi.');
        }

        const priceValue = price.value.trim();

        if (
            priceValue === '' ||
            !Number.isFinite(Number(priceValue)) ||
            Number(priceValue) < 0 ||
            Number(priceValue) > 999999
        ) {
            errors.push('Harga mesti nombor antara 0 hingga 999999.');
        }

        if (errors.length > 0) {
            event.preventDefault();

            const errorBox = document.getElementById('clientErrors');

            if (errorBox) {
                errorBox.classList.remove('d-none');
                errorBox.innerHTML = '';

                const list = document.createElement('ul');
                list.className = 'mb-0';

                errors.forEach(function (message) {
                    const item = document.createElement('li');
                    item.textContent = message;
                    list.appendChild(item);
                });

                errorBox.appendChild(list);
                errorBox.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            } else {
                alert(errors.join('\n'));
            }
        }
    });
});
