<<<<<<< HEAD
// Attendre que le DOM soit complètement chargé
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded"); // Pour le débogage
    
    // Récupération des éléments du formulaire
=======
// payment.js - Code complet optimisé
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded");

    // 1. Récupération des éléments du formulaire
>>>>>>> 628366a (cruuud)
    const paymentForm = document.getElementById("payment-form");
    const cardNumberInput = document.getElementById("card-number");
    const expiryDateInput = document.getElementById("expiry-date");
    const securityCodeInput = document.getElementById("security-code");
    const cardTypeSelect = document.getElementById("card-type");
    const phoneNumberInput = document.getElementById("phone-number");
    const mobileProviderSelect = document.getElementById("mobile-provider");
<<<<<<< HEAD
    
    // Vérifier que les éléments existent avant d'ajouter des écouteurs d'événements
    if (cardNumberInput) {
        console.log("Card number input found");
        cardNumberInput.addEventListener("input", function(e) {
            // Stocker la position du curseur
            const cursorPos = this.selectionStart;
            const oldLength = this.value.length;
            
            // Formater le numéro de carte
            let value = this.value.replace(/\D/g, ""); // Supprimer les caractères non numériques
            if (value.length > 16) value = value.substring(0, 16); // Limiter à 16 chiffres
            
            // Ajouter des espaces tous les 4 chiffres
            const formattedValue = value.replace(/(\d{4})(?=\d)/g, "$1 ");
            this.value = formattedValue;
            
            // Ajuster la position du curseur si nécessaire
            const newLength = this.value.length;
            if (cursorPos < oldLength) {
                this.setSelectionRange(cursorPos, cursorPos);
            }
        });
    }
    
    if (expiryDateInput) {
        console.log("Expiry date input found");
=======

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
>>>>>>> 628366a (cruuud)
        expiryDateInput.addEventListener("input", function(e) {
            const cursorPos = this.selectionStart;
            const oldValue = this.value;
            
<<<<<<< HEAD
            // Nettoyer la valeur (ne garder que les chiffres et /)
            let value = this.value.replace(/[^\d\/]/g, "");
            
            // Gestion du format MM/AA
            if (value.length === 1) {
                // Si le premier chiffre est > 1, préfixe avec 0
                if (parseInt(value) > 1) {
                    value = "0" + value;
                }
            } else if (value.length === 2) {
                // Vérifier que le mois est valide (01-12)
                const month = parseInt(value);
                if (month < 1) value = "01";
                else if (month > 12) value = "12";
                
                // Ajouter le / automatiquement
                if (!value.includes("/")) {
                    value = value + "/";
                }
            } else if (value.length > 2 && !value.includes("/")) {
                // Insérer le / si l'utilisateur ne l'a pas saisi
                value = value.substring(0, 2) + "/" + value.substring(2);
            }
            
            // Limiter à 5 caractères (MM/AA)
            if (value.length > 5) {
                value = value.substring(0, 5);
            }
            
            // Mettre à jour la valeur
            if (oldValue !== value) {
                this.value = value;
                
                // Gérer la position du curseur pour une meilleure expérience utilisateur
                if (oldValue.length === 2 && value.length === 3) {
                    this.setSelectionRange(3, 3); // Placer après le /
                } else {
                    // Conserver la position du curseur si possible
                    this.setSelectionRange(cursorPos, cursorPos);
                }
            }
        });
        
        // Validation à la perte de focus
=======
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

>>>>>>> 628366a (cruuud)
        expiryDateInput.addEventListener("blur", function() {
            validateExpiryDate(this);
        });
    }
<<<<<<< HEAD
    
    if (securityCodeInput) {
        console.log("Security code input found");
        securityCodeInput.addEventListener("input", function() {
            // Ne garder que les chiffres et limiter à 3
=======

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
>>>>>>> 628366a (cruuud)
            let value = this.value.replace(/\D/g, "");
            if (value.length > 3) value = value.substring(0, 3);
            this.value = value;
        });
    }
<<<<<<< HEAD
    
    if (cardTypeSelect) {
        console.log("Card type select found");
        cardTypeSelect.addEventListener("change", function() {
            const errorElement = document.getElementById("card-type-error");
            if (errorElement) {
                if (!this.value) {
                    errorElement.textContent = "Veuillez sélectionner un type de carte.";
                    errorElement.style.display = "block";
                } else {
                    errorElement.style.display = "none";
                }
            }
        });
    }
    
    if (phoneNumberInput) {
        console.log("Phone number input found");
        phoneNumberInput.addEventListener("input", function() {
            // Ne garder que les chiffres et limiter à 8
=======

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
>>>>>>> 628366a (cruuud)
            let value = this.value.replace(/\D/g, "");
            if (value.length > 8) value = value.substring(0, 8);
            this.value = value;
        });
<<<<<<< HEAD
        
        // Validation à la perte de focus
        phoneNumberInput.addEventListener("blur", function() {
            const errorElement = document.getElementById("phone-number-error");
            if (errorElement) {
                if (this.value && this.value.length !== 8) {
                    errorElement.textContent = "Le numéro de téléphone doit comporter exactement 8 chiffres.";
                    errorElement.style.display = "block";
                } else {
                    errorElement.style.display = "none";
                }
            }
        });
    }
    
    if (mobileProviderSelect) {
        console.log("Mobile provider select found");
        mobileProviderSelect.addEventListener("change", function() {
            const errorElement = document.getElementById("mobile-provider-error");
            if (errorElement) {
                if (!this.value) {
                    errorElement.textContent = "Veuillez choisir un fournisseur mobile.";
                    errorElement.style.display = "block";
                } else {
                    errorElement.style.display = "none";
                }
            }
        });
    }
    
    // Gestion de la soumission du formulaire
    if (paymentForm) {
        console.log("Payment form found");
        paymentForm.addEventListener("submit", function(event) {
            // Empêcher l'envoi du formulaire pour la validation
            event.preventDefault();
            
            // Réinitialiser tous les messages d'erreur
            const errorElements = document.querySelectorAll("[id$='-error']");
            errorElements.forEach(element => {
                element.style.display = "none";
                element.textContent = "";
            });
            
            let valid = true;
            const paymentError = document.getElementById("payment-error");
            
            // Vérifier si une méthode de paiement est sélectionnée
            const paymentMethodElements = document.getElementsByName("payment-method");
            let selectedPaymentMethod = "";
            
            for (let i = 0; i < paymentMethodElements.length; i++) {
                if (paymentMethodElements[i].checked) {
                    selectedPaymentMethod = paymentMethodElements[i].value;
                    break;
                }
            }
            
            if (!selectedPaymentMethod) {
                if (paymentError) {
                    paymentError.textContent = "Veuillez choisir un mode de paiement.";
                    paymentError.style.display = "block";
                }
                valid = false;
            } else {
                // Validation selon le mode de paiement
                if (selectedPaymentMethod === "card") {
                    // Validation du paiement par carte
                    const cardNumber = cardNumberInput ? cardNumberInput.value.trim() : "";
                    const expiryDate = expiryDateInput ? expiryDateInput.value.trim() : "";
                    const securityCode = securityCodeInput ? securityCodeInput.value.trim() : "";
                    const cardType = cardTypeSelect ? cardTypeSelect.value : "";
                    
                    // Numéro de carte
                    if (!cardNumber) {
                        showError("card-number", "Veuillez entrer le numéro de la carte.");
                        valid = false;
                    } else if (!isValidCardNumber(cardNumber)) {
                        showError("card-number", "Le numéro de carte doit contenir 16 chiffres.");
                        valid = false;
                    }
                    
                    // Date d'expiration
                    if (!expiryDate) {
                        showError("expiry-date", "Veuillez entrer la date d'expiration.");
                        valid = false;
                    } else if (!isValidExpiryDate(expiryDate)) {
                        showError("expiry-date", "La date d'expiration doit être au format MM/AA et être valide.");
                        valid = false;
                    }
                    
                    // Code de sécurité
                    if (!securityCode) {
                        showError("security-code", "Veuillez entrer le code de sécurité.");
                        valid = false;
                    } else if (securityCode.length !== 3 || !(/^\d+$/.test(securityCode))) {
                        showError("security-code", "Le code de sécurité doit comporter 3 chiffres.");
                        valid = false;
                    }
                    
                    // Type de carte
                    if (!cardType) {
                        showError("card-type", "Veuillez sélectionner un type de carte.");
                        valid = false;
                    }
                    
                } else if (selectedPaymentMethod === "mobile") {
                    // Validation du paiement mobile
                    const phoneNumber = phoneNumberInput ? phoneNumberInput.value.trim() : "";
                    const mobileProvider = mobileProviderSelect ? mobileProviderSelect.value : "";
                    
                    // Numéro de téléphone
                    if (!phoneNumber) {
                        showError("phone-number", "Veuillez entrer le numéro de téléphone.");
                        valid = false;
                    } else if (phoneNumber.length !== 8 || !(/^\d+$/.test(phoneNumber))) {
                        showError("phone-number", "Le numéro de téléphone doit comporter exactement 8 chiffres.");
                        valid = false;
                    }
                    
                    // Fournisseur mobile
                    if (!mobileProvider) {
                        showError("mobile-provider", "Veuillez choisir un fournisseur mobile.");
                        valid = false;
                    }
                }
            }
            
            // Si tout est valide, soumettre le formulaire
            if (valid) {
                console.log("Form validation passed, submitting...");
                this.submit();
            }
        });
    }
    
    // Fonctions auxiliaires
    
    // Vérifier si une valeur ne contient que des chiffres
    function isNumeric(value) {
        return /^\d+$/.test(value);
    }
    
    // Valider le numéro de carte (16 chiffres)
    function isValidCardNumber(cardNumber) {
        const digitsOnly = cardNumber.replace(/\D/g, '');
        return isNumeric(digitsOnly) && digitsOnly.length === 16;
    }
    
    // Valider la date d'expiration (MM/AA, doit être dans le futur)
    function isValidExpiryDate(expiryDate) {
        // Vérifier le format MM/AA
        if (!/^\d{2}\/\d{2}$/.test(expiryDate)) {
            return false;
        }
        
        const parts = expiryDate.split('/');
        const month = parseInt(parts[0], 10);
        const year = parseInt('20' + parts[1], 10); // Convertir AA en AAAA
        
        // Vérifier que le mois est entre 1 et 12
        if (month < 1 || month > 12) {
            return false;
        }
        
        // Obtenir la date actuelle
        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth() + 1; // getMonth() renvoie 0-11
        
        // Vérifier si la date est dans le futur
        if (year < currentYear || (year === currentYear && month < currentMonth)) {
            return false;
        }
        
        return true;
    }
    
    // Valider la date d'expiration avec affichage d'erreur
    function validateExpiryDate(input) {
        const errorElement = document.getElementById("expiry-date-error");
        if (errorElement && input.value) {
            if (!isValidExpiryDate(input.value)) {
                errorElement.textContent = "La date d'expiration doit être au format MM/AA et être valide.";
                errorElement.style.display = "block";
                return false;
            } else {
                errorElement.style.display = "none";
                return true;
            }
        }
        return input.value ? true : false;
    }
    
    // Afficher un message d'erreur pour un champ spécifique
    function showError(fieldId, message) {
        const errorElement = document.getElementById(fieldId + "-error");
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        } else {
            // Fallback au message d'erreur général
            const paymentError = document.getElementById("payment-error");
            if (paymentError) {
                paymentError.textContent = message;
                paymentError.style.display = "block";
            }
        }
    }
    
    // Initialiser la visibilité des formulaires au chargement
    if (typeof togglePaymentForms === 'function') {
        togglePaymentForms();
    } else {
        console.error("La fonction togglePaymentForms n'est pas définie");
    }
    
=======

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

>>>>>>> 628366a (cruuud)
    console.log("JavaScript initialization complete");
});