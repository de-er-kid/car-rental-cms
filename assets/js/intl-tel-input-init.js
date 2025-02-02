// Store initialized elements to prevent duplicate initialization
const initializedElements = new WeakSet();

// Debounce function to limit execution frequency
const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

// Main initialization function
const initializeIntlTelInput = (inputField) => {
    // Prevent double initialization
    if (initializedElements.has(inputField)) return;
    
    // Initialize intlTelInput
    const iti = window.intlTelInput(inputField, {
        initialCountry: "ae",
        preferredCountries: ["ae"],
        separateDialCode: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js",
        countrySearch: true,
    });

    // Mark as initialized
    initializedElements.add(inputField);
    
    // Handle country change event
    inputField.addEventListener('countrychange', () => {
        const dialCode = iti.getSelectedCountryData().dialCode;

        // Clear the input field before setting the new dial code
        inputField.value = `+${dialCode}`;
    });

    return iti;
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Initialize existing fields
    document.querySelectorAll("input[type='tel']").forEach(initializeIntlTelInput);
    
    // Handle Elementor popups more efficiently
    jQuery(document).on("elementor/popup/show", debounce(() => {
        const popup = document.querySelector(".elementor-popup-modal");
        if (!popup) return;
        
        popup.querySelectorAll("input[type='tel']").forEach(initializeIntlTelInput);
    }, 100));
    
    // Optimized mutation observer with debounce
    const debouncedObserverCallback = debounce((mutations) => {
        const newInputs = new Set();
        
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1) {
                    node.querySelectorAll?.("input[type='tel']").forEach(input => {
                        if (!initializedElements.has(input)) {
                            newInputs.add(input);
                        }
                    });
                }
            });
        });
        
        newInputs.forEach(initializeIntlTelInput);
    }, 100);

    // Start observing with optimized configuration
    const observer = new MutationObserver(debouncedObserverCallback);
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributeFilter: ['class'], // Only observe class changes
    });
});
