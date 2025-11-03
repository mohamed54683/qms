<!DOCTYPE html>
<html>
<head>
    <title>Test File Upload</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; }
        .upload-area { border: 2px dashed #ccc; padding: 40px; text-align: center; margin: 20px 0; }
        .result { margin-top: 20px; padding: 15px; background: #f0f0f0; border-radius: 5px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Test File Upload</h1>
    <p>This is a simple test to verify file uploads work</p>
    
    <div class="upload-area">
        <input type="file" id="fileInput" accept="image/*">
        <p>Selected: <span id="fileName">None</span></p>
        <button onclick="uploadFile()">Upload Test Image</button>
    </div>
    
    <div class="result" id="result" style="display:none;"></div>
    
    <script>
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'None';
            document.getElementById('fileName').textContent = fileName;
        });
        
        async function uploadFile() {
            const fileInput = document.getElementById('fileInput');
            const resultDiv = document.getElementById('result');
            
            if (!fileInput.files[0]) {
                alert('Please select a file first!');
                return;
            }
            
            const formData = new FormData();
            formData.append('test_image', fileInput.files[0]);
            formData.append('_token', '{{ csrf_token() }}');
            
            resultDiv.style.display = 'block';
            resultDiv.textContent = 'Uploading...';
            
            try {
                const response = await fetch('/test-upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <strong>✅ Success!</strong><br>
                        File uploaded to: ${data.path}<br>
                        <a href="/storage/${data.path}" target="_blank">View uploaded file</a>
                    `;
                    resultDiv.style.background = '#d4edda';
                } else {
                    resultDiv.innerHTML = `<strong>❌ Failed:</strong> ${data.message}`;
                    resultDiv.style.background = '#f8d7da';
                }
            } catch (error) {
                resultDiv.innerHTML = `<strong>❌ Error:</strong> ${error.message}`;
                resultDiv.style.background = '#f8d7da';
            }
        }
    </script>
</body>
</html>
