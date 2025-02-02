const initializedElements = new WeakSet();
let utilsScriptLoaded = false;

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const loadUtilsScript = () => {
    if (utilsScriptLoaded) return; 

    const script = document.createElement("script");
    script.src = "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js";
    script.onload = () => {
        utilsScriptLoaded = true;
    };
    document.body.appendChild(script);
};

const initializeIntlTelInput = (inputField) => {

    if (initializedElements.has(inputField)) return;

    loadUtilsScript();

    const iti = window.intlTelInput(inputField, {
        initialCountry: "ae",
        preferredCountries: ["ae"],
        separateDialCode: true,
        countrySearch: true,
    });

    initializedElements.add(inputField);

    inputField.addEventListener('countrychange', () => {
        const dialCode = iti.getSelectedCountryData().dialCode;

        inputField.value = `+${dialCode}`;
    });

    return iti;
};

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll("input[type='tel']").forEach(initializeIntlTelInput);

    jQuery(document).on("elementor/popup/show", debounce(() => {
        const popup = document.querySelector(".elementor-popup-modal");
        if (!popup) return;

        popup.querySelectorAll("input[type='tel']").forEach(initializeIntlTelInput);
    }, 100));

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

    const observer = new MutationObserver(debouncedObserverCallback);
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributeFilter: ['class'], 
    });
});