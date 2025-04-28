document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("reclamationForm")
    const fullnameInput = document.getElementById("fullname")
    const emailInput = document.getElementById("email")
    const causeInput = document.getElementById("cause")
    const descriptionInput = document.getElementById("description")
    const fileInput = document.getElementById("fileInput")
    const screenshotBox = document.getElementById("screenshotBox")
    const previewContainer = document.getElementById("previewContainer")
    const imagePreview = document.getElementById("imagePreview")
    const removeBtn = document.getElementById("removeBtn")
    const submitBtn = document.getElementById("submitBtn")
    const spinner = document.getElementById("spinner")
    const btnText = document.getElementById("btnText")
    const confirmationMsg = document.getElementById("confirmationMsg")
  
    // Regular expressions for validation
    const namePattern = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}$/
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  
    // Clear all error messages
    function clearErrors() {
      document.querySelectorAll(".error-message").forEach((el) => el.remove())
    }
  
    // Show error message for an input
    function showError(input, message) {
      clearErrorForInput(input)
  
      const error = document.createElement("div")
      error.className = "error-message"
      error.style.color = "#ff4d4d"
      error.style.fontSize = "0.85rem"
      error.style.marginTop = "0.5rem"
      error.style.fontWeight = "500"
      error.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`
  
      input.parentElement.appendChild(error)
      input.style.borderColor = "#ff4d4d"
    }
  
    // Clear error for a specific input
    function clearErrorForInput(input) {
      const existingError = input.parentElement.querySelector(".error-message")
      if (existingError) {
        existingError.remove()
      }
      input.style.borderColor = ""
    }
  
    // Handle file selection
    screenshotBox.addEventListener("click", () => {
      fileInput.click()
    })
  
    // Handle drag and drop
    screenshotBox.addEventListener("dragover", (e) => {
      e.preventDefault()
      screenshotBox.style.borderColor = "var(--primary)"
      screenshotBox.style.backgroundColor = "var(--primary-light)"
    })
  
    screenshotBox.addEventListener("dragleave", () => {
      screenshotBox.style.borderColor = ""
      screenshotBox.style.backgroundColor = ""
    })
  
    screenshotBox.addEventListener("drop", (e) => {
      e.preventDefault()
      screenshotBox.style.borderColor = ""
      screenshotBox.style.backgroundColor = ""
  
      if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files
        handleFileSelect()
      }
    })
  
    // Handle file input change
    fileInput.addEventListener("change", handleFileSelect)
  
    function handleFileSelect() {
      const file = fileInput.files[0]
  
      if (file) {
        // Validate file type
        const allowedTypes = ["image/jpeg", "image/png", "image/gif"]
        if (!allowedTypes.includes(file.type)) {
          showError(fileInput, "Invalid file type. Please upload JPG, PNG, or GIF.")
          fileInput.value = ""
          return
        }
  
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
          showError(fileInput, "File is too large. Maximum size is 5MB.")
          fileInput.value = ""
          return
        }
  
        // Show preview
        const reader = new FileReader()
        reader.onload = (e) => {
          imagePreview.src = e.target.result
          previewContainer.style.display = "block"
          screenshotBox.style.display = "none"
          clearErrorForInput(fileInput)
        }
        reader.readAsDataURL(file)
      }
    }
  
    // Remove selected file
    removeBtn.addEventListener("click", () => {
      fileInput.value = ""
      previewContainer.style.display = "none"
      screenshotBox.style.display = "block"
    })
  
    // Input validation on blur
    fullnameInput.addEventListener("blur", function () {
      if (this.value.trim() !== "" && !namePattern.test(this.value.trim())) {
        showError(this, "Please enter a valid name (at least 2 letters, letters only).")
      } else {
        clearErrorForInput(this)
      }
    })
  
    emailInput.addEventListener("blur", function () {
      if (this.value.trim() !== "" && !emailPattern.test(this.value.trim())) {
        showError(this, "Please enter a valid email address.")
      } else {
        clearErrorForInput(this)
      }
    })
  
    causeInput.addEventListener("blur", function () {
      if (this.value === "") {
        showError(this, "Please select a reclamation cause.")
      } else {
        clearErrorForInput(this)
      }
    })
  
    descriptionInput.addEventListener("blur", function () {
      if (this.value.trim() !== "" && this.value.trim().length < 30) {
        showError(this, "Description must be at least 30 characters long.")
      } else {
        clearErrorForInput(this)
      }
    })
  
    // Form submission
    form.addEventListener("submit", (e) => {
      e.preventDefault()
      clearErrors()
  
      let hasError = false
  
      // Validate full name
      const fullname = fullnameInput.value.trim()
      if (fullname === "" || !namePattern.test(fullname)) {
        showError(fullnameInput, "Please enter a valid name (at least 2 letters, letters only).")
        hasError = true
      }
  
      // Validate email
      const email = emailInput.value.trim()
      if (email === "" || !emailPattern.test(email)) {
        showError(emailInput, "Please enter a valid email address.")
        hasError = true
      }
  
      // Validate cause
      const cause = causeInput.value
      if (cause === "") {
        showError(causeInput, "Please select a reclamation cause.")
        hasError = true
      }
  
      // Validate description
      const description = descriptionInput.value.trim()
      if (description === "" || description.length < 30) {
        showError(descriptionInput, "Description must be at least 30 characters long.")
        hasError = true
      }
  
      // Validate file if one is selected
      const file = fileInput.files[0]
      if (file) {
        const allowedTypes = ["image/jpeg", "image/png", "image/gif"]
        if (!allowedTypes.includes(file.type)) {
          showError(fileInput, "Invalid file type. Please upload JPG, PNG, or GIF.")
          hasError = true
        }
  
        if (file.size > 5 * 1024 * 1024) {
          showError(fileInput, "File is too large. Maximum size is 5MB.")
          hasError = true
        }
      }
  
      if (hasError) {
        return false
      }
  
      // Show loading state
      submitBtn.disabled = true
      spinner.style.display = "inline-block"
      btnText.textContent = "Submitting..."
  
      // Create FormData object
      const formData = new FormData()
      formData.append("full_name", fullname);
      formData.append("email", email);
      formData.append("cause", cause);
      formData.append("description", description);
  
      if (file) {
        formData.append("screenshot", file)
      }
  
      // Log the form data for debugging
      console.log("Submitting form data:", {
        nom: fullname,
        email: email,
        type_id: getCauseTypeId(cause),
        status: cause, // Log the status
        message: description,
        hasFile: !!file,
      })
  
      // Determine the correct path to the controller
      // Try different paths if you're unsure about the correct one
      const controllerPath = "controller/ReclamationController.php"
  
      // Send AJAX request
      fetch(controllerPath, {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          console.log("Response status:", response.status);
          return response.text().then((text) => {
            if (!response.ok) {
              console.error("Error response text:", text);
              throw new Error(text || "Network response was not ok");
            }
            return text;    
          });
        })
        .then((data) => {
          console.log("Success response:", data);

          // Reset form
          form.reset();
          previewContainer.style.display = "none";
          screenshotBox.style.display = "block";

          // Show success message
          confirmationMsg.style.display = "block";

          // Scroll to confirmation message
          confirmationMsg.scrollIntoView({ behavior: "smooth" });

          // Reset button state
          submitBtn.disabled = false;
          spinner.style.display = "none";
          btnText.textContent = "Submit Reclamation";

          // Hide confirmation message after 5 seconds
          setTimeout(() => {
            confirmationMsg.style.display = "none";
          }, 5000);
        })
        .catch((error) => {
          console.error("Error:", error)
  
          // Show error message
          const errorMsg = document.createElement("div")
          errorMsg.className = "error-message"
          errorMsg.style.backgroundColor = "rgba(255, 0, 0, 0.2)"
          errorMsg.style.color = "#ff4d4d"
          errorMsg.style.padding = "1.25rem"
          errorMsg.style.borderRadius = "var(--border-radius)"
          errorMsg.style.marginTop = "1.5rem"
          errorMsg.style.textAlign = "center"
          errorMsg.style.border = "1px solid rgba(255, 77, 77, 0.3)"
          errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> An error occurred. Please try again later.'
  
          form.appendChild(errorMsg)
  
          // Reset button state
          submitBtn.disabled = false
          spinner.style.display = "none"
          btnText.textContent = "Submit Reclamation"
  
          // Hide error message after 5 seconds
          setTimeout(() => {
            errorMsg.remove()
          }, 5000)
        })
    })
  
    // Helper function to convert cause value to type_id
    function getCauseTypeId(cause) {
      const causeMap = {
        technical: 1,
        billing: 2,
        account: 3,
        content: 4,
        other: 5,
      }
      return causeMap[cause] || 1
    }
  })