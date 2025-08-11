<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CSRF Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4">CSRF Token Test</h1>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600">Current CSRF Token:</p>
            <p class="text-xs font-mono bg-gray-100 p-2 rounded" id="current-token">{{ csrf_token() }}</p>
        </div>
        
        <form id="test-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Test Data</label>
                <input type="text" name="test_data" value="Hello World" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                Test CSRF Token
            </button>
        </form>
        
        <div class="mt-4">
            <button onclick="refreshToken()" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700">
                Refresh CSRF Token
            </button>
        </div>
    </div>

    <script src="{{ asset('js/csrf-handler.js') }}"></script>
    <script src="{{ asset('js/global-ajax.js') }}"></script>
    
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };
        
        document.getElementById('test-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const formData = new FormData(this);
                
                const response = await AjaxUtils.makeRequest('POST', '/test-csrf-endpoint', formData, {
                    successMessage: 'CSRF token validation successful!',
                    errorMessage: 'CSRF token validation failed!'
                });
                
                console.log('Response:', response);
                
            } catch (error) {
                console.error('Error:', error);
                toastr.error('Error: ' + error.message);
            }
        });
        
        async function refreshToken() {
            try {
                const newToken = await CSRFHandler.refreshToken();
                if (newToken) {
                    document.getElementById('current-token').textContent = newToken;
                    toastr.success('CSRF token refreshed successfully!');
                } else {
                    toastr.error('Failed to refresh CSRF token');
                }
            } catch (error) {
                console.error('Refresh error:', error);
                toastr.error('Error refreshing token: ' + error.message);
            }
        }
    </script>
</body>
</html>