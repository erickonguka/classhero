document.addEventListener('alpine:init', () => {
    Alpine.data('ajaxUtils', () => ({
        async makeRequest(method, url, data = {}, options = {}) {
            if (options.setLoading) options.setLoading(true);
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                const headers = {
                    'Accept': 'application/json',
                    ...options.headers
                };

                // Handle CSRF token
                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }

                // Handle different data types
                let body;
                if (data instanceof FormData) {
                    // For FormData, ensure CSRF token is included in the form data
                    if (csrfToken && !data.has('_token')) {
                        data.append('_token', csrfToken);
                    }
                    body = data;
                } else if (method !== 'GET') {
                    headers['Content-Type'] = 'application/json';
                    // For JSON data, include CSRF token
                    if (csrfToken && typeof data === 'object') {
                        data._token = csrfToken;
                    }
                    body = JSON.stringify(data);
                }

                const response = await fetch(url, {
                    method: method,
                    headers: headers,
                    body: body,
                    ...options
                });

                let result;
                try {
                    result = await response.json();
                } catch (e) {
                    const text = await response.text();
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
        }
    }));
});