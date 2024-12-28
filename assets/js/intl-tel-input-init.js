document.addEventListener('DOMContentLoaded', function() {
    const initializeIntlTelInput = (inputField) => {
        const iti = window.intlTelInput(inputField, {
            initialCountry: "ae",
            preferredCountries: ["ae"],
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
        });

        inputField.addEventListener('countrychange', function() {
            const selectedDialCode = iti.getSelectedCountryData().dialCode;
            inputField.value = `+${selectedDialCode}`;
        });

        // Validate on form submit
        inputField.closest('form')?.addEventListener('submit', function(e) {
            if (!iti.isValidNumber()) {
                e.preventDefault();
                inputField.classList.add('error');
                alert('Please enter a valid phone number.');
            }
        });
    };

    // Initialize for existing elements
    const phoneInputFields = document.querySelectorAll("input[type='tel']");
    phoneInputFields.forEach(initializeIntlTelInput);

    // Observe dynamically added fields (Elementor-specific)
    const observer = new MutationObserver(function(mutationsList) {
        mutationsList.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1 && node.matches("input[type='tel']")) {
                    initializeIntlTelInput(node);
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});
