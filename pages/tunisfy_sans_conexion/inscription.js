document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const emailInput = form.querySelector('input[name="email"]');
    const usernameInput = form.querySelector('input[name="nom_utilisateur"]');
    const passwordInput = form.querySelector('input[name="mot_de_passe"]');
    const prenomInput = form.querySelector('input[name="prenom"]');
    const nomFamilleInput = form.querySelector('input[name="nom_famille"]');
    const dateNaissanceInput = form.querySelector('input[name="date_naissance"]');

    form.addEventListener('submit', (e) => {
        let errors = [];

        // Vérifie si les champs requis sont remplis
        if (!usernameInput.value.trim()) {
            errors.push("Le nom d'utilisateur est requis.");
        }

        if (!emailInput.value.trim()) {
            errors.push("L'email est requis.");
        } else if (!validateEmail(emailInput.value.trim())) {
            errors.push("L'email n'est pas valide.");
        }

        if (!passwordInput.value.trim()) {
            errors.push("Le mot de passe est requis.");
        } else if (passwordInput.value.length < 6) {
            errors.push("Le mot de passe doit contenir au moins 6 caractères.");
        }

        // Vérifie le prénom et le nom (facultatif mais si rempli → valider)
        if (prenomInput.value && !/^[A-Za-zÀ-ÿ\- ]+$/.test(prenomInput.value)) {
            errors.push("Le prénom contient des caractères non valides.");
        }

        if (nomFamilleInput.value && !/^[A-Za-zÀ-ÿ\- ]+$/.test(nomFamilleInput.value)) {
            errors.push("Le nom de famille contient des caractères non valides.");
        }

        // Vérifie la date de naissance si remplie
        if (dateNaissanceInput.value) {
            const birthDate = new Date(dateNaissanceInput.value);
            const today = new Date();
            if (birthDate >= today) {
                errors.push("La date de naissance doit être dans le passé.");
            }
        }

        if (errors.length > 0) {
            e.preventDefault(); // Empêche l'envoi du formulaire
            alert(errors.join("\n"));
        }
    });

    // Fonction de validation d'email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email.toLowerCase());
    }
});
