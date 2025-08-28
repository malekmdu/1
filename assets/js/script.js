/**
 * GeoPortfolio Pro - JavaScript Functions
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeComponents();
});

function initializeComponents() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // File upload preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.querySelector('#' + input.id + '-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = input.id + '-preview';
                        preview.className = 'img-thumbnail mt-2';
                        preview.style.maxHeight = '200px';
                        input.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
}

// Admin functions
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

function toggleUserStatus(userId) {
    if (confirm('Are you sure you want to toggle this user\'s status?')) {
        fetch('api/toggle-user-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ userId: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

// Rich text editor functions
function initializeRichTextEditor(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        // Simple rich text editor using contenteditable
        element.contentEditable = true;
        element.style.minHeight = '300px';
        element.style.border = '1px solid #ddd';
        element.style.padding = '15px';
        element.style.borderRadius = '5px';
        
        // Add toolbar
        const toolbar = document.createElement('div');
        toolbar.className = 'editor-toolbar';
        toolbar.innerHTML = `
            <button type="button" onclick="formatText('bold')" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-bold"></i>
            </button>
            <button type="button" onclick="formatText('italic')" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-italic"></i>
            </button>
            <button type="button" onclick="formatText('underline')" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-underline"></i>
            </button>
            <button type="button" onclick="formatText('insertUnorderedList')" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-list-ul"></i>
            </button>
            <button type="button" onclick="formatText('insertOrderedList')" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-list-ol"></i>
            </button>
        `;
        
        element.parentNode.insertBefore(toolbar, element);
    }
}

function formatText(command) {
    document.execCommand(command, false, null);
}

// Image upload and management
function uploadImage(inputElement, callback) {
    const formData = new FormData();
    formData.append('image', inputElement.files[0]);
    
    fetch('api/upload-image.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            callback(data.url);
        } else {
            alert('Upload failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Upload failed. Please try again.');
    });
}

// Dynamic form fields
function addSkillItem(categoryId) {
    const container = document.getElementById('skill-items-' + categoryId);
    const itemCount = container.children.length;
    
    const newItem = document.createElement('div');
    newItem.className = 'row mb-2 skill-item';
    newItem.innerHTML = `
        <div class="col-md-6">
            <input type="text" name="skill_items[${categoryId}][${itemCount}][name]" class="form-control" placeholder="Skill name" required>
        </div>
        <div class="col-md-4">
            <input type="number" name="skill_items[${categoryId}][${itemCount}][percentage]" class="form-control" placeholder="%" min="0" max="100" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger" onclick="removeSkillItem(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(newItem);
}

function removeSkillItem(button) {
    button.closest('.skill-item').remove();
}

// Tags management
function initializeTagsInput(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const tagsContainer = document.createElement('div');
    tagsContainer.className = 'tags-container mb-2';
    input.parentNode.insertBefore(tagsContainer, input);
    
    let tags = [];
    
    function renderTags() {
        tagsContainer.innerHTML = tags.map(tag => 
            `<span class="badge bg-secondary me-1 mb-1">
                ${tag}
                <button type="button" class="btn-close btn-close-white ms-1" onclick="removeTag('${tag}')"></button>
            </span>`
        ).join('');
        input.value = JSON.stringify(tags);
    }
    
    window.removeTag = function(tagToRemove) {
        tags = tags.filter(tag => tag !== tagToRemove);
        renderTags();
    };
    
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const tag = this.value.trim();
            if (tag && !tags.includes(tag)) {
                tags.push(tag);
                renderTags();
                this.value = '';
            }
        }
    });
    
    // Load existing tags
    try {
        const existingTags = JSON.parse(input.value || '[]');
        if (Array.isArray(existingTags)) {
            tags = existingTags;
            renderTags();
        }
    } catch (e) {
        // Invalid JSON, start with empty array
    }
}

// Form auto-save
function initializeAutoSave(formId, saveUrl) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    let saveTimeout;
    
    function autoSave() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            const formData = new FormData(form);
            formData.append('auto_save', '1');
            
            fetch(saveUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAutoSaveIndicator('Saved');
                }
            })
            .catch(error => {
                console.error('Auto-save error:', error);
            });
        }, 2000);
    }
    
    form.addEventListener('input', autoSave);
    form.addEventListener('change', autoSave);
}

function showAutoSaveIndicator(text) {
    let indicator = document.getElementById('auto-save-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'auto-save-indicator';
        indicator.className = 'position-fixed top-0 end-0 p-3';
        indicator.style.zIndex = '1060';
        document.body.appendChild(indicator);
    }
    
    indicator.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-body">
                <i class="fas fa-check text-success me-2"></i>${text}
            </div>
        </div>
    `;
    
    setTimeout(() => {
        indicator.innerHTML = '';
    }, 2000);
}

// Export functions for global use
window.confirmDelete = confirmDelete;
window.toggleUserStatus = toggleUserStatus;
window.initializeRichTextEditor = initializeRichTextEditor;
window.formatText = formatText;
window.uploadImage = uploadImage;
window.addSkillItem = addSkillItem;
window.removeSkillItem = removeSkillItem;
window.initializeTagsInput = initializeTagsInput;
window.initializeAutoSave = initializeAutoSave;