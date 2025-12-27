$(document).ready(function() {
    // İlk yüklemede tüm illeri getir
    fetchProvinces();

    // İl seçimi değiştiğinde ilçeleri getir
    $('#il').change(function() {
        var selectedIl = $(this).val() || 'Adana';
        fetchDistricts(selectedIl);
    });

    // Tüm illeri getiren AJAX çağrısı
    function fetchProvinces() {
        $.ajax({
            type: 'GET',
            url: "https://turkiyeapi.dev/api/v1/provinces",
            success: function(response) {
                updateIlSelect(response.data);
            }
        });
    }

    // İlçeleri getiren AJAX çağrısı
    function fetchDistricts(selectedIl) {
        $.ajax({
            type: 'GET',
            url: "https://turkiyeapi.dev/api/v1/provinces?name=" + selectedIl,
            success: function(response) {
                updateIlceSelect(response.data[0].districts);
            }
        });
    }

    // İl seçeneklerini güncelle
    function updateIlSelect(provinces) {
        var ilSelect = $('#il');
        ilSelect.empty();
        $.each(provinces, function(key, value) {
            ilSelect.append('<option value="' + value.name + '">' + value.name + '</option>');
        });
    }

    // İlçe seçeneklerini güncelle
    function updateIlceSelect(districts) {
        var ilceSelect = $('#ilce');
        ilceSelect.empty();
        $.each(districts, function(key, value) {
            ilceSelect.append('<option value="' + value.name + '">' + value.name + '</option>');
        });
    }
});
