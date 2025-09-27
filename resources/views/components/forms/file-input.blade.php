@props([
    'id' => null,
    'name' => '',
    'label' => '',
    'accept' => '',
    'multiple' => false,
    'required' => false,
    'maxSize' => '10MB',
    'allowedTypes' => 'PDF, JPEG, JPG, PNG',
    'helpText' => null
])

@php
    $inputId = $id ?? 'file_input_' . uniqid();
    $isMultiple = $multiple ? 'multiple' : '';
    $isRequired = $required ? 'required' : '';
@endphp

<div class="file-upload-container">
    @if($label)
        <x-forms.input-label for="{{ $inputId }}" :value="$label" />
    @endif
    
    <div class="file-upload-wrapper mt-1">
        <!-- Hidden File Input -->
        <input 
            type="file" 
            id="{{ $inputId }}"
            name="{{ $name }}"
            accept="{{ $accept }}"
            {{ $isMultiple }}
            {{ $isRequired }}
            class="file-upload-input"
            style="display: none;"
        />
        
        <!-- Custom Upload Area -->
        <div class="file-upload-area" onclick="document.getElementById('{{ $inputId }}').click()">
            <div class="file-upload-content">
                <!-- Upload Icon -->
                <div class="file-upload-icon">
                    <span class="material-symbols-outlined">cloud_upload</span>
                </div>
                
                <!-- Upload Text -->
                <div class="file-upload-text">
                    <p class="file-upload-primary">
                        <span class="file-upload-button">Pilih Fail</span>
                        <span class="file-upload-or">atau seret dan lepas di sini</span>
                    </p>
                    <p class="file-upload-secondary">
                        Format yang diterima: {{ $allowedTypes }}
                    </p>
                    @if($maxSize)
                        <p class="file-upload-size">
                            Saiz maksimum: {{ $maxSize }} setiap fail
                        </p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Selected Files Display -->
        <div class="file-upload-selected" id="{{ $inputId }}_selected" style="display: none;">
            <div class="file-upload-files"></div>
        </div>
    </div>
    
    @if($helpText)
        <p class="file-upload-help">{{ $helpText }}</p>
    @endif
    
    <x-forms.input-error class="mt-2" :messages="$errors->get($name)" />
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('{{ $inputId }}');
    const uploadArea = fileInput.parentElement.querySelector('.file-upload-area');
    const selectedArea = document.getElementById('{{ $inputId }}_selected');
    const filesContainer = selectedArea.querySelector('.file-upload-files');
    
    // File input change handler
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });
    
    // Drag and drop handlers
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('file-upload-dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('file-upload-dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('file-upload-dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFiles(files);
        }
    });
    
    function handleFiles(files) {
        if (files.length === 0) {
            selectedArea.style.display = 'none';
            return;
        }
        
        filesContainer.innerHTML = '';
        
        Array.from(files).forEach(function(file, index) {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-upload-item';
            
            const fileIcon = getFileIcon(file.type);
            const fileSize = formatFileSize(file.size);
            
            fileItem.innerHTML = `
                <div class="file-upload-item-content">
                    <div class="file-upload-item-icon">
                        <span class="material-symbols-outlined">${fileIcon}</span>
                    </div>
                    <div class="file-upload-item-info">
                        <p class="file-upload-item-name">${file.name}</p>
                        <p class="file-upload-item-size">${fileSize}</p>
                    </div>
                    <div class="file-upload-item-remove" onclick="removeFile(${index})">
                        <span class="material-symbols-outlined">close</span>
                    </div>
                </div>
            `;
            
            filesContainer.appendChild(fileItem);
        });
        
        selectedArea.style.display = 'block';
    }
    
    function getFileIcon(fileType) {
        if (fileType.includes('pdf')) return 'picture_as_pdf';
        if (fileType.includes('image')) return 'image';
        if (fileType.includes('document') || fileType.includes('word')) return 'description';
        if (fileType.includes('spreadsheet') || fileType.includes('excel')) return 'table_chart';
        return 'insert_drive_file';
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Global function for remove file
    window.removeFile = function(index) {
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        
        files.forEach(function(file, i) {
            if (i !== index) {
                dt.items.add(file);
            }
        });
        
        fileInput.files = dt.files;
        handleFiles(fileInput.files);
    };
});
</script>
