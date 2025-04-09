<!-- File Preview Modal -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filePreviewModalLabel">File Preview: <span id="previewFileName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="filePreviewContent" class="p-2"></div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File preview functionality
        const previewButtons = document.querySelectorAll('.preview-btn');
        previewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filePath = this.getAttribute('data-path');
                const fileName = filePath.split('/').pop(); // Extract file name from path
                const modal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
                
                // Set the file name in the modal title
                document.getElementById('previewFileName').textContent = fileName;
                
                // Show loading indicator
                document.getElementById('filePreviewContent').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading file content...</p></div>';
                modal.show();
                
                // Fetch file content
                fetch('/file-preview', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ path: filePath })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        document.getElementById('filePreviewContent').innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                    } else {
                        const content = data.content.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        document.getElementById('filePreviewContent').innerHTML = `<pre class="file-content">${content}</pre>`;
                    }
                })
                .catch(error => {
                    document.getElementById('filePreviewContent').innerHTML = `<div class="alert alert-danger">Error loading file: ${error.message}</div>`;
                });
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Modal dark theme styling */
    .modal-content {
        background-color: var(--card-bg);
        color: var(--text-color);
        border: none;
    }
    
    .modal-header {
        border-bottom: 1px solid var(--table-border);
        padding: 0.75rem 1rem;
    }
    
    .modal-header .btn-close {
        color: var(--text-color);
        opacity: 0.8;
        filter: invert(1) grayscale(100%) brightness(200%);
    }
    
    .modal-title {
        font-size: 1.1rem;
        font-weight: 500;
    }
    
    .modal-body {
        padding: 0;
    }
    
    pre.file-content {
        background-color: var(--bg-color);
        color: var(--text-color);
        padding: 1rem;
        margin: 0;
        border-radius: 0;
        border: none;
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 0.85rem;
        line-height: 1.5;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        max-height: 70vh;
        overflow-y: auto;
    }
    
    html[data-theme="light"] .modal-header .btn-close {
        filter: none;
    }
</style>
<?php $__env->stopPush(); ?>
<?php /**PATH /var/www/resources/views/components/file-preview.blade.php ENDPATH**/ ?>