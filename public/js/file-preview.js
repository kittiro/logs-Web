/**
 * File Preview JS
 * Handles Ajax-based file preview functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize file preview functionality
    initFilePreview();
});

function initFilePreview() {
    // Add click event listeners to file preview links
    document.querySelectorAll('.file-preview-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const filePath = this.getAttribute('data-path');
            loadFilePreview(filePath);
        });
    });
}

function loadFilePreview(filePath) {
    // Show loading indicator
    const previewContainer = document.getElementById('file-preview-container');
    if (previewContainer) {
        previewContainer.innerHTML = '<div class="text-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    }

    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Make Ajax request to get file content
    fetch('/file-preview', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ path: filePath })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error loading file content. Status: ' + response.status + ' ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        displayFilePreview(data);
    })
    .catch(error => {
        showErrorMessage(error.message);
    });
}

function displayFilePreview(data) {
    const previewContainer = document.getElementById('file-preview-container');
    if (!previewContainer) return;

    // Create preview header
    let previewHtml = `
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">File Preview: ${data.filename}</h5>
                <button type="button" class="btn-close" aria-label="Close" onclick="closeFilePreview()"></button>
            </div>
            <div class="card-body">
                <pre class="file-content">${escapeHtml(data.content)}</pre>
            </div>
        </div>
    `;
    
    previewContainer.innerHTML = previewHtml;
    previewContainer.style.display = 'block';
}

function showErrorMessage(message) {
    const previewContainer = document.getElementById('file-preview-container');
    if (previewContainer) {
        previewContainer.innerHTML = `
            <div class="alert alert-danger">
                <h5>Error</h5>
                <p>${message}</p>
                <button type="button" class="btn btn-secondary mt-2" onclick="closeFilePreview()">Close</button>
            </div>
        `;
    }
}

function closeFilePreview() {
    const previewContainer = document.getElementById('file-preview-container');
    if (previewContainer) {
        previewContainer.style.display = 'none';
        previewContainer.innerHTML = '';
    }
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
