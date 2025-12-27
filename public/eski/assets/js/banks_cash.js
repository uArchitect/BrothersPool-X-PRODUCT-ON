// Telefon numarası girdisi için formatlama işlevi
function formatPhoneNumberInput(input) {
    input.addEventListener("input", function (e) {
        var value = e.target.value.replace(/\D/g, ""); // Sadece rakamları al

        // Uygun formatı kontrol et ve formatla
        if (value.length === 10) {
            var formattedValue =
                value.substring(0, 3) +
                "-" +
                value.substring(3, 6) +
                "-" +
                value.substring(6);
            e.target.value = formattedValue;
        }
    });
}




// Telefon numarası girdisi için formatlama işlevini uygula
var phoneNumberInputs = document.querySelectorAll("#phone_number, #fax_number");
phoneNumberInputs.forEach(function (input) {
    formatPhoneNumberInput(input);
});

// IBAN girdisini formatlamak için işlev
function formatIBANInput(input) {
    input.addEventListener("input", function (e) {
        var value = e.target.value.replace(/[^a-zA-Z0-9]/g, ""); // Alfanümerik olmayan tüm karakterleri kaldır
        var formattedValue = value.match(/.{1,4}/g); // Her dört karakterde bir boşluk ekleyerek grupla
        if (formattedValue !== null) {
            e.target.value = formattedValue.join(" "); // Boşluklarla birleştir
        }
    });
}

// IBAN girdisi için formatlama işlevini uygula
var ibanInput = document.getElementById("iban");
formatIBANInput(ibanInput); // IBAN girdisi için formatlama uygula
