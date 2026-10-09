document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.querySelector('form[action="search.php"]');

    if (!searchForm) {
        return;
    }

    searchForm.addEventListener('submit', function (event) {
        const keywordInput = searchForm.querySelector('input[name="keyword"]');

        if (!keywordInput || keywordInput.value.trim() === '') {
            event.preventDefault();
            alert('Sila masukkan kata kunci untuk mencari service.');
            keywordInput?.focus();
        }
    });
});
