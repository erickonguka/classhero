// Global AJAX utilities
window.AjaxUtils = {
    async makeRequest(method, url, data = {}, options = {}) {
        if (options.setLoading) options.setLoading(true);
        
        try {
            const headers = {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                ...options.headers
            };

            // Handle different data types (CSRF token handled globally)
            let body;
            if (data instanceof FormData) {
                body = data;
            } else if (method !== 'GET') {
                headers['Content-Type'] = 'application/json';
                body = JSON.stringify(data);
            }

            const response = await fetch(url, {
                method: method,
                headers: headers,
                body: body,
                ...options
            });

            let result;
            const responseClone = response.clone();
            try {
                result = await response.json();
            } catch (e) {
                const text = await responseClone.text();
                throw Object.assign(new Error(`Unexpected response: ${text.substring(0, 100)}...`), { status: response.status });
            }

            if (!response.ok) {
                throw Object.assign(new Error(result.message || 'Request failed'), { response: result, status: response.status });
            }

            if (options.successMessage) {
                toastr.success(options.successMessage);
            }

            return result;
        } catch (error) {
            if (error.status === 422 && error.response && error.response.errors) {
                throw error;
            }
            toastr.error(options.errorMessage || error.message || 'An error occurred');
            throw error;
        } finally {
            if (options.setLoading) options.setLoading(false);
        }
    },

    async confirmAndRequest({ title = 'Are you sure?', text = 'This action cannot be undone.', confirmButtonText = 'Yes, proceed!', 
        method, url, data = {}, successMessage, errorMessage, onSuccess, onError, setLoading }) {
        const result = await Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText,
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: async () => {
                try {
                    return await this.makeRequest(method, url, data, { successMessage, errorMessage, setLoading });
                } catch (error) {
                    Swal.showValidationMessage(error.message);
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        });

        if (result.isConfirmed) {
            if (onSuccess) onSuccess(result.value);
            return result.value;
        }
        if (result.dismiss === Swal.DismissReason.cancel && onError) {
            onError();
        }
    },

    // Form validation helpers
    validateRequired(value, fieldName) {
        if (!value || (typeof value === 'string' && !value.trim())) {
            return `${fieldName} is required`;
        }
        return null;
    },

    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return 'Please enter a valid email address';
        }
        return null;
    },

    validateMinLength(value, minLength, fieldName) {
        if (value && value.length < minLength) {
            return `${fieldName} must be at least ${minLength} characters`;
        }
        return null;
    },

    validateMaxLength(value, maxLength, fieldName) {
        if (value && value.length > maxLength) {
            return `${fieldName} must be less than ${maxLength} characters`;
        }
        return null;
    },

    // Display validation errors
    displayErrors(errors, formElement) {
        // Clear previous errors
        formElement.querySelectorAll('.error-message').forEach(el => el.remove());
        formElement.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
        });

        // Display new errors
        Object.keys(errors).forEach(field => {
            const input = formElement.querySelector(`[name="${field}"], [name="${field}[]"]`);
            if (input) {
                input.classList.add('border-red-500');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message mt-1 text-sm text-red-600';
                errorDiv.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                input.parentNode.appendChild(errorDiv);
            }
        });

        // Scroll to first error
        const firstError = formElement.querySelector('.border-red-500');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    },

    // Clear form errors
    clearErrors(formElement) {
        formElement.querySelectorAll('.error-message').forEach(el => el.remove());
        formElement.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
        });
    }
};