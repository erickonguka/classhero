// General form handling utilities
document.addEventListener('DOMContentLoaded', function() {
    // Handle all forms with data-ajax attribute
    document.querySelectorAll('form[data-ajax]').forEach(form => {
        let isSubmitting = false;
        
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Prevent double submission
            if (isSubmitting) return;
            isSubmitting = true;
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            const loadingText = submitButton.dataset.loadingText || 'Processing...';
            
            // Set loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                ${loadingText}
            `;
            
            try {
                const formData = new FormData(form);
                const method = form.method || 'POST';
                const url = form.action;
                
                const response = await AjaxUtils.makeRequest(method, url, formData, {
                    successMessage: form.dataset.successMessage,
                    errorMessage: form.dataset.errorMessage
                });
                
                // Handle success
                if (response.redirect_url) {
                    setTimeout(() => {
                        window.location.href = response.redirect_url;
                    }, 1500);
                } else if (form.dataset.redirectUrl) {
                    setTimeout(() => {
                        window.location.href = form.dataset.redirectUrl;
                    }, 1500);
                } else if (response.reload) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
                
            } catch (error) {
                console.error('Form submission error:', error);
                
                // Handle validation errors
                if (error.status === 422 && error.response?.errors) {
                    AjaxUtils.displayErrors(error.response.errors, form);
                }
                
            } finally {
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                isSubmitting = false;
            }
        });
    });
    
    // Handle delete buttons with confirmation
    document.querySelectorAll('[data-confirm-delete]').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const url = this.href || this.dataset.url;
            const itemName = this.dataset.itemName || 'this item';
            const method = this.dataset.method || 'DELETE';
            
            try {
                await AjaxUtils.confirmAndRequest({
                    title: 'Delete Confirmation',
                    text: `Are you sure you want to delete ${itemName}? This action cannot be undone.`,
                    confirmButtonText: 'Yes, delete it!',
                    method: method,
                    url: url,
                    successMessage: `${itemName} deleted successfully!`,
                    onSuccess: () => {
                        // Remove the item from DOM if it has a data-item-id
                        const itemId = this.dataset.itemId;
                        if (itemId) {
                            const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                            if (itemElement) {
                                itemElement.remove();
                            }
                        } else {
                            // Reload page if no specific item to remove
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    }
                });
            } catch (error) {
                console.error('Delete error:', error);
            }
        });
    });
    
    // Handle status toggle buttons
    document.querySelectorAll('[data-toggle-status]').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const url = this.dataset.url;
            const currentStatus = this.dataset.currentStatus;
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            
            try {
                const response = await AjaxUtils.makeRequest('POST', url, {
                    status: newStatus
                }, {
                    successMessage: `Status updated successfully!`
                });
                
                // Update button appearance
                this.dataset.currentStatus = newStatus;
                this.textContent = newStatus === 'active' ? 'Deactivate' : 'Activate';
                this.className = newStatus === 'active' 
                    ? 'px-3 py-1 text-xs bg-red-100 text-red-800 rounded-full hover:bg-red-200'
                    : 'px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full hover:bg-green-200';
                    
            } catch (error) {
                console.error('Status toggle error:', error);
            }
        });
    });
    
    // Auto-clear form errors on input
    document.querySelectorAll('input, textarea, select').forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('border-red-500')) {
                this.classList.remove('border-red-500');
                const errorMessage = this.parentNode.querySelector('.error-message');
                if (errorMessage) {
                    errorMessage.remove();
                }
            }
        });
    });
    
    // Handle file upload previews
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', function(e) {
            const previewContainer = document.querySelector(this.dataset.preview);
            if (!previewContainer) return;
            
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="w-32 h-32 object-cover rounded-lg border">
                    `;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    });
});

// Utility functions for dynamic form fields
window.FormUtils = {
    addArrayField(containerSelector, fieldName, placeholder = '') {
        const container = document.querySelector(containerSelector);
        if (!container) return;
        
        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'flex items-center space-x-2 mb-2';
        fieldDiv.innerHTML = `
            <input type="text" name="${fieldName}[]" 
                   class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                   placeholder="${placeholder}">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        `;
        container.appendChild(fieldDiv);
    },
    
    removeArrayField(button) {
        const container = button.closest('.array-field-container');
        button.parentElement.remove();
        
        // Ensure at least one field remains
        if (container && container.children.length === 0) {
            this.addArrayField(`#${container.id}`, container.dataset.fieldName, container.dataset.placeholder);
        }
    }
};