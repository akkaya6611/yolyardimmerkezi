(function () {
    'use strict';
    document.querySelectorAll('.mis360-member-form').forEach(function (form) {
        var field = form.querySelector('[data-tax-field]');
        if (!field) return;
        var file = field.querySelector('input[type="file"]');
        var choices = form.querySelectorAll('input[name="kind"]');
        function update() {
            var selected = form.querySelector('input[name="kind"]:checked');
            var needed = !choices.length || (selected && selected.value === 'firm');
            field.hidden = !needed; file.disabled = !needed; file.required = !!needed;
            if (!needed) { file.value = ''; file.setCustomValidity(''); }
        }
        choices.forEach(function (choice) { choice.addEventListener('change', update); });
        file.addEventListener('change', function () {
            file.setCustomValidity(file.files.length && file.files[0].size > 5 * 1024 * 1024 ? 'Dosya en fazla 5 MB olabilir.' : '');
        });
        update();
    });
}());
