// payment.js - Code complet optimisé
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded");

    // 1. Récupération des éléments du formulaire
    const paymentForm = document.getElementById("payment-form");
    const cardNumberInput = document.getElementById("card-number");
    const expiryDateInput = document.getElementById("expiry-date");
    const securityCodeInput = document.getElementById("security-code");
    const cardTypeSelect = document.getElementById("card-type");
    const phoneNumberInput = document.getElementById("phone-number");
    const mobileProviderSelect = document.getElementById("mobile-provider");

    // 2. Gestion du numéro de carte
    if (cardNumberInput) {
        cardNumberInput.addEventListener("input", function(e) {
            const cursorPos = this.selectionStart;
            const oldValue = this.value;
            
            let value = this.value.replace(/\D/g, "");
            if (value.length > 16) value = value.substring(0, 16);
            
            const formattedValue = value.replace(/(\d{4})(?=\d)/g, "$1 ");
            this.value = formattedValue;
            
            this.setSelectionRange(cursorPos + (formattedValue.length - oldValue.length), 
                                cursorPos + (formattedValue.length - oldValue.length));
        });
    }

    // 3. Gestion de la date d'expiration (JJ MM AA)
    if (expiryDateInput) {
        expiryDateInput.addEventListener("input", function(e) {
            const cursorPos = this.selectionStart;
            const oldValue = this.value;
            
            let value = this.value.replace(/\D/g, "");
            if (value.length > 6) value = value.substring(0, 6);
            
            let formatted = "";
            for (let i = 0; i < value.length; i++) {
                if (i === 2 || i === 4) formatted += " ";
                formatted += value[i];
            }
            
            this.value = formatted;
            this.setSelectionRange(cursorPos + (formatted.length - oldValue.length), 
                                cursorPos + (formatted.length - oldValue.length));
        });

        expiryDateInput.addEventListener("blur", function() {
            validateExpiryDate(this);
        });
    }

    // 4. Validation de la date (JJ MM AA)
    function isValidExpiryDate(expiryDate) {
        if (!/^\d{2} \d{2} \d{2}$/.test(expiryDate)) return false;

        const [day, month, year] = expiryDate.split(" ").map(Number);
        
        // Validation des plages
        if (day < 1 || day > 31) return false;
        if (month < 1 || month > 12) return false;
        if (year < 24 || year > 99) return false;

        // Validation de la date réelle
        const fullYear = 2000 + year;
        const testDate = new Date(fullYear, month - 1, day);
        
        return (testDate.getFullYear() === fullYear &&
                testDate.getMonth() + 1 === month &&
                testDate.getDate() === day);
    }

    // 5. Affichage des erreurs de date
    function validateExpiryDate(input) {
        const errorElement = document.getElementById("expiry-date-error");
        if (!errorElement) return;

        if (!isValidExpiryDate(input.value)) {
            errorElement.textContent = "La date doit être au format JJ MM AA et valide (ex: 15 06 24)";
            errorElement.style.display = "block";
            return false;
        } else {
            errorElement.style.display = "none";
            return true;
        }
    }

    // 6. Gestion du code de sécurité
    if (securityCodeInput) {
        securityCodeInput.addEventListener("input", function() {
            let value = this.value.replace(/\D/g, "");
            if (value.length > 3) value = value.substring(0, 3);
            this.value = value;
        });
    }

    // 7. Gestion du type de carte
    if (cardTypeSelect) {
        cardTypeSelect.addEventListener("change", function() {
            const errorElement = document.getElementById("card-type-error");
            if (errorElement) {
                errorElement.style.display = this.value ? "none" : "block";
            }
        });
    }

    // 8. Gestion du numéro de téléphone
    if (phoneNumberInput) {
        phoneNumberInput.addEventListener("input", function() {
            let value = this.value.replace(/\D/g, "");
            if (value.length > 8) value = value.substring(0, 8);
            this.value = value;
        });

        phoneNumberInput.addEventListener("blur", function() {
            const errorElement = document.getElementById("phone-number-error");
            if (errorElement) {
                errorElement.style.display = (this.value.length === 8) ? "none" : "block";
            }
        });
    }

    // 9. Gestion du fournisseur mobile
    if (mobileProviderSelect) {
        mobileProviderSelect.addEventListener("change", function() {
            const errorElement = document.getElementById("mobile-provider-error");
            if (errorElement) {
                errorElement.style.display = this.value ? "none" : "block";
            }
        });
    }

    // 10. Validation du formulaire
    if (paymentForm) {
        paymentForm.addEventListener("submit", function(event) {
            event.preventDefault();
            const errorElements = document.querySelectorAll("[id$='-error']");
            errorElements.forEach(el => el.style.display = "none");

            let valid = true;
            const paymentMethod = document.querySelector('[name="payment-method"]:checked')?.value;

            if (!paymentMethod) {
                showError("payment", "Veuillez choisir un mode de paiement");
                valid = false;
            }

            if (paymentMethod === "card") {
                if (!validateCard()) valid = false;
            } else if (paymentMethod === "mobile") {
                if (!validateMobile()) valid = false;
            }

            if (valid) this.submit();
        });
    }

    // 11. Validation de la carte
    function validateCard() {
        let valid = true;
        const fields = {
            "card-number": isValidCardNumber(cardNumberInput.value),
            "expiry-date": isValidExpiryDate(expiryDateInput.value),
            "security-code": /^\d{3}$/.test(securityCodeInput.value),
            "card-type": !!cardTypeSelect.value
        };

        Object.entries(fields).forEach(([id, isValid]) => {
            if (!isValid) {
                showError(id, "Champ invalide");
                valid = false;
            }
        });

        return valid;
    }

    // 12. Validation du mobile
    function validateMobile() {
        let valid = true;
        const fields = {
            "phone-number": /^\d{8}$/.test(phoneNumberInput.value),
            "mobile-provider": !!mobileProviderSelect.value
        };

        Object.entries(fields).forEach(([id, isValid]) => {
            if (!isValid) {
                showError(id, "Champ invalide");
                valid = false;
            }
        });

        return valid;
    }

    // 13. Fonction d'affichage des erreurs
    function showError(fieldId, message) {
        const errorElement = document.getElementById(`${fieldId}-error`) || document.getElementById("payment-error");
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        }
    }

    // 14. Validation du numéro de carte (optionnel)
    function isValidCardNumber(cardNumber) {
        const cleaned = cardNumber.replace(/\s+/g, '');
        return /^\d{16}$/.test(cleaned);
    }

    console.log("JavaScript initialization complete");
});