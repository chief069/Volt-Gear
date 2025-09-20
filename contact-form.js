// Contact Form Script
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const submitBtn = contactForm.querySelector('.submit-btn');
    
    // Remove any existing messages
    function removeMessages() {
        const messages = contactForm.querySelectorAll('.success-message, .error-message');
        messages.forEach(msg => msg.remove());
    }

    // Create and show message
    function showMessage(type, text) {
        removeMessages();
        const messageDiv = document.createElement('div');
        messageDiv.className = type === 'success' ? 'success-message' : 'error-message';
        messageDiv.textContent = text;
        
        // Insert before the submit button's parent element (form-group)
        submitBtn.parentElement.insertAdjacentElement('beforebegin', messageDiv);
        
        // Auto-remove after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
    }

    // Form submission handler
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Get form values
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const subject = document.getElementById('subject').value.trim();
        const message = document.getElementById('message').value.trim();
        const reason = document.querySelector('input[name="reason"]:checked')?.value || 'Not specified';
        
        // Basic validation
        if (!name || !email || !message) {
            showMessage('error', 'Please fill in all required fields.');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showMessage('error', 'Please enter a valid email address.');
            return;
        }
        
        // Set button to loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        
        // Prepare form data for Web3Forms
        const formData = new FormData();
        formData.append('access_key', '1120e367-d52a-4cb4-bc24-54258774bf81');
        formData.append('name', name);
        formData.append('email', email);
        formData.append('subject', subject || 'Contact Form Submission');
        formData.append('message', message);
        formData.append('reason', reason);
        
        try {
            // Send data to Web3Forms API
            const response = await fetch('https://api.web3forms.com/submit', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Success message
                showMessage('success', 'Thank you! Your message has been sent successfully.');
                // Reset form
                contactForm.reset();
            } else {
                // Error message
                showMessage('error', data.message || 'Something went wrong. Please try again later.');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('error', 'An error occurred. Please try again later.');
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
        }
    });
});