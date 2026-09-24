(function () {
    'use strict';
    document.querySelectorAll('.mis360-location-fields').forEach(function (group) {
        var city = group.querySelector('[name="city"]');
        var district = group.querySelector('[name="district"]');
        var locations;
        try { locations = JSON.parse(group.dataset.locations); } catch (error) { return; }
        city.addEventListener('change', function () {
            district.replaceChildren(new Option(city.value ? 'Tüm ilçeler' : 'Önce il seçin', ''));
            (locations[city.value] || []).forEach(function (name) {
                district.add(new Option(name, name));
            });
            district.disabled = !city.value;
            var legacy = group.querySelector('[name="location"]');
            if (legacy) legacy.value = '';
        });
    });
}());
