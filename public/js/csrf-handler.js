// CSRF Token Handler - Ensures all AJAX requests include proper CSRF tokens
(function() {
    'use strict';
    
    // Get CSRF token from meta tag
    function getCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            console.warn('CSRF token not found in meta tag');
        }
        return token;
    }
    
    // Refresh CSRF token if needed
    async function refreshCSRFToken() {
        try {
            const response = await fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag && data.token) {
                    metaTag.setAttribute('content', data.token);
                    return data.token;
                }
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
        }
        return null;
    }
    
    // Override fetch to automatically include CSRF tokens
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        // Only modify requests to our own domain
        if (typeof url === 'string' && (url.startsWith('/') || url.includes(window.location.hostname))) {
            const method = (options.method || 'GET').toUpperCase();
            
            // Add CSRF token for state-changing methods
            if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
                const token = getCSRFToken();
                
                if (token) {
                    // Initialize headers if not present
                    options.headers = options.headers || {};
                    
                    // Add CSRF token to headers
                    if (!options.headers['X-CSRF-TOKEN']) {
                        options.headers['X-CSRF-TOKEN'] = token;
                    }
                    
                    // If body is FormData, also add token to form data
                    if (options.body instanceof FormData && !options.body.has('_token')) {
                        options.body.append('_token', token);
                    }
                    
                    // If body is JSON and doesn't have token, add it
                    if (options.headers['Content-Type'] === 'application/json' && 
                        typeof options.body === 'string') {
                        try {
                            const jsonData = JSON.parse(options.body);
                            if (!jsonData._token) {
                                jsonData._token = token;
                                options.body = JSON.stringify(jsonData);
                            }
                        } catch (e) {
                            // Not valid JSON, skip
                        }
                    }
                }
            }
        }
        
        return originalFetch.call(this, url, options);
    };
    
    // Override XMLHttpRequest for jQuery and other libraries
    const originalOpen = XMLHttpRequest.prototype.open;
    const originalSend = XMLHttpRequest.prototype.send;
    
    XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
        this._method = method.toUpperCase();
        this._url = url;
        return originalOpen.call(this, method, url, async, user, password);
    };
    
    XMLHttpRequest.prototype.send = function(data) {
        // Only modify requests to our own domain with state-changing methods
        if (this._url && (this._url.startsWith('/') || this._url.includes(window.location.hostname)) &&
            ['POST', 'PUT', 'PATCH', 'DELETE'].includes(this._method)) {
            
            const token = getCSRFToken();
            if (token) {
                // Add CSRF token to headers
                this.setRequestHeader('X-CSRF-TOKEN', token);
                
                // If data is FormData, add token
                if (data instanceof FormData && !data.has('_token')) {
                    data.append('_token', token);
                }
            }
        }
        
        return originalSend.call(this, data);
    };
    
    // jQuery AJAX setup if jQuery is available
    if (typeof $ !== 'undefined' && $.ajaxSetup) {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                // Only add CSRF token for state-changing methods to our domain
                if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(settings.type?.toUpperCase()) &&
                    (settings.url.startsWith('/') || settings.url.includes(window.location.hostname))) {
                    
                    const token = getCSRFToken();
                    if (token) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', token);
                        
                        // Add to form data if needed
                        if (settings.data instanceof FormData && !settings.data.has('_token')) {
                            settings.data.append('_token', token);
                        } else if (typeof settings.data === 'object' && !settings.data._token) {
                            settings.data._token = token;
                        }
                    }
                }
            }
        });
    }
    
    // Handle CSRF token mismatch errors
    window.addEventListener('unhandledrejection', function(event) {
        if (event.reason && event.reason.message && 
            event.reason.message.includes('CSRF token mismatch')) {
            console.warn('CSRF token mismatch detected, attempting to refresh token...');
            refreshCSRFToken().then(newToken => {
                if (newToken) {
                    console.log('CSRF token refreshed successfully');
                }
            });
        }
    });
    
    // Expose utility functions
    window.CSRFHandler = {
        getToken: getCSRFToken,
        refreshToken: refreshCSRFToken
    };
    
})();