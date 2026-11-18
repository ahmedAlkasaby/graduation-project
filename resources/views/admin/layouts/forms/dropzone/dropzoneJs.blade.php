
<style>
    .dz-file-size {
        display: block;
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 4px;
        text-align: center;
    }
</style>

<script>
    Dropzone.autoDiscover = false;

    // Track how many images are currently being compressed
    var _compressingCount = 0;

    function _disableSubmit() {
        _compressingCount++;
        document.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function(btn) {
            btn.disabled = true;
            if (!btn.dataset.originalText) {
                btn.dataset.originalText = btn.innerHTML;
            }
            btn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i> {{ __("Processing image...") }}';
        });
    }

    function _enableSubmit() {
        _compressingCount--;
        if (_compressingCount <= 0) {
            _compressingCount = 0;
            document.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function(btn) {
                btn.disabled = false;
                if (btn.dataset.originalText) {
                    btn.innerHTML = btn.dataset.originalText;
                }
            });
        }
    }

    /**
     * Compress image to target ~2MB if it exceeds 2MB.
     * - If file ≤ 2MB: no change
     * - If file > 2MB: compress to 1.8-2MB range
     * - Keeps dimensions as large as possible
     * - For very large images (>5MB), scales down proportionally just enough
     */
    function compressImage(file, options = {}) {
        const maxSize = options.maxSize || 2 * 1024 * 1024; // 2MB

        return new Promise(function(resolve) {
            // Skip non-image or already small enough
            if (!file || !file.size || file.size <= maxSize) {
                return resolve(file);
            }
            // Check type - skip non-images, SVG, GIF
            var type = (file.type || '').toLowerCase();
            if (!type.startsWith('image/') || type === 'image/svg+xml' || type === 'image/gif') {
                return resolve(file);
            }

            // Use createObjectURL instead of readAsDataURL (handles large files better)
            var objectUrl = URL.createObjectURL(file);
            var img = new Image();

            img.onload = function() {
                var width = img.width;
                var height = img.height;

                console.log('[Compress] ' + file.name + ': ' + (file.size / 1024 / 1024).toFixed(2) + 'MB, ' + width + 'x' + height);

                // Only resize for very large files (>8MB) where quality alone can't reach 2MB
                // For files 2-8MB, keep full dimensions and just reduce quality
                if (file.size > maxSize * 4) {
                    // JPEG at q=0.85 is roughly 2 bytes/pixel
                    var targetPixels = maxSize / 2.0;
                    var currentPixels = width * height;
                    if (currentPixels > targetPixels) {
                        var scale = Math.sqrt(targetPixels / currentPixels);
                        scale = Math.max(0.3, scale);
                        width = Math.round(width * scale);
                        height = Math.round(height * scale);
                        console.log('[Compress] Scaled to: ' + width + 'x' + height + ' (scale: ' + scale.toFixed(2) + ')');
                    }
                }

                var canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                URL.revokeObjectURL(objectUrl);

                // Binary search: find highest quality that produces 1.8-2MB
                var lo = 0.1;
                var hi = 1.0;
                var bestBlob = null;
                var targetMin = 1.8 * 1024 * 1024;

                function search(iterations) {
                    if (iterations <= 0) {
                        var result = bestBlob || file;
                        if (result instanceof Blob && !(result instanceof File)) {
                            result = new File([result], file.name, { type: 'image/jpeg', lastModified: Date.now() });
                        }
                        console.log('[Compress] Done: ' + (result.size / 1024 / 1024).toFixed(2) + 'MB');
                        return resolve(result);
                    }

                    var mid = (lo + hi) / 2;
                    canvas.toBlob(function(blob) {
                        if (!blob) {
                            console.log('[Compress] toBlob failed at quality ' + mid.toFixed(3));
                            var result = bestBlob || file;
                            if (result instanceof Blob && !(result instanceof File)) {
                                result = new File([result], file.name, { type: 'image/jpeg', lastModified: Date.now() });
                            }
                            return resolve(result);
                        }

                        if (blob.size >= targetMin && blob.size <= maxSize) {
                            console.log('[Compress] Hit target at quality ' + mid.toFixed(3) + ': ' + (blob.size / 1024 / 1024).toFixed(2) + 'MB');
                            return resolve(new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }));
                        }

                        if (blob.size <= maxSize) {
                            bestBlob = blob;
                            lo = mid;
                        } else {
                            hi = mid;
                        }

                        search(iterations - 1);
                    }, 'image/jpeg', mid);
                }

                search(15);
            };

            img.onerror = function() {
                console.log('[Compress] Image failed to load:', file.name);
                URL.revokeObjectURL(objectUrl);
                resolve(file);
            };

            img.src = objectUrl;
        });
    }

    const previewTemplate = `
<div class="dz-preview dz-file-preview">
    <div class="dz-photo">
        <img class="dz-thumbnail" data-dz-thumbnail />
    </div>
    <span class="dz-file-size" data-dz-size></span>
    <button class="dz-delete border-0 p-0" type="button" data-dz-remove>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path fill="#FFFFFF"
                d="M13.41,12l4.3-4.29a1,1,0,1,0-1.42-1.42L12,10.59,7.71,6.29A1,1,0,0,0,6.29,7.71L10.59,12l-4.3,4.29a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0L12,13.41l4.29,4.3a1,1,0,0,0,1.42,0,1,1,0,0,0,0-1.42Z">
            </path>
        </svg>
    </button>
</div>`;

    // ─── Single-image Dropzone ────────────────────────────────────
    document.querySelectorAll('.dropzone[data-dropzone-name]').forEach(function(dropzoneEl) {

        const name = dropzoneEl.dataset.dropzoneName;

        const fileInput = document.getElementById('input-' + name);
        const removedInput = document.getElementById('removed-' + name);

        const dz = new Dropzone(dropzoneEl, {
            url: "/",
            autoProcessQueue: false,
            uploadMultiple: false,
            maxFiles: 1,
            acceptedFiles: "image/*",
            previewTemplate: previewTemplate,
            addRemoveLinks: true,

            init: function() {

                const dzInstance = this;

                // existing image
                const existingImage = dropzoneEl.dataset.existingImageUrl;
                const existingSize = dropzoneEl.dataset.existingImageSize;
                if (existingImage && existingImage !== '' && existingImage !== 'null' && !existingImage.endsWith('/')) {

                    const mockFile = {
                        name: 'current-image',
                        size: parseInt(existingSize || 0),
                        accepted: true
                    };

                    dzInstance.emit("addedfile", mockFile);
                    dzInstance.emit("thumbnail", mockFile, existingImage);
                    dzInstance.emit("complete", mockFile);
                    dzInstance.files.push(mockFile);
                }

                dzInstance.on("addedfile", function(file) {

                    const existingFiles = dzInstance.files.filter(f => f !== file);
                    existingFiles.forEach(f => { dzInstance.removeFile(f); });

                    removedInput.value = "0";

                    // Compress image before assigning to input
                    if (file instanceof File) {
                        _disableSubmit();
                        compressImage(file).then(function(compressedFile) {
                            console.log('[Dropzone] ' + name + ': ' + (file.size / 1024 / 1024).toFixed(2) + 'MB → ' + (compressedFile.size / 1024 / 1024).toFixed(2) + 'MB');
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(compressedFile);
                            fileInput.files = dataTransfer.files;
                            _enableSubmit();
                        }).catch(function() {
                            // Fallback: use original
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            fileInput.files = dataTransfer.files;
                            _enableSubmit();
                        });
                    }
                });

                dzInstance.on("removedfile", function() {
                    removedInput.value = "1";
                    const dataTransfer = new DataTransfer();
                    fileInput.files = dataTransfer.files;
                });

                dzInstance.on("maxfilesexceeded", function(file) {
                    dzInstance.removeAllFiles();
                    dzInstance.addFile(file);
                });
            }
        });
    });

    // ─── Multi-image Dropzone ─────────────────────────────────────
    document.querySelectorAll('.dropzone[data-dropzone-multi]').forEach(function(dropzoneEl) {

        const name = dropzoneEl.dataset.dropzoneMulti;
        const fileInput = document.getElementById('input-multi-' + name);
        const existingInput = document.getElementById('existing-' + name);

        const dz = new Dropzone(dropzoneEl, {
            url: "/",
            autoProcessQueue: false,
            uploadMultiple: true,
            maxFiles: 20,
            acceptedFiles: "image/*",
            previewTemplate: previewTemplate,
            addRemoveLinks: true,

            init: function() {
                const dzInstance = this;

                // Load existing images
                const existingImages = dropzoneEl.dataset.existingImages;
                const existingSizes = dropzoneEl.dataset.existingSizes;

                if (existingImages && existingImages !== '') {
                    const images = existingImages.split(',');
                    const sizes = existingSizes ? existingSizes.split(',') : [];

                    images.forEach(function(img, index) {
                        if (!img || img === 'null') return;

                        const mockFile = {
                            name: 'image-' + index,
                            size: parseInt(sizes[index] || 0),
                            accepted: true,
                            existingPath: img
                        };

                        dzInstance.emit("addedfile", mockFile);
                        dzInstance.emit("thumbnail", mockFile, img);
                        dzInstance.emit("complete", mockFile);
                        dzInstance.files.push(mockFile);
                    });
                }

                function syncFiles() {
                    var newFiles = dzInstance.files.filter(function(f) {
                        return f instanceof File;
                    });

                    // Sync existing paths immediately
                    var remaining = dzInstance.files
                        .filter(f => f.existingPath)
                        .map(f => f.existingPath);
                    existingInput.value = remaining.join(',');

                    if (newFiles.length === 0) {
                        const dataTransfer = new DataTransfer();
                        fileInput.files = dataTransfer.files;
                        return;
                    }

                    _disableSubmit();
                    Promise.all(newFiles.map(function(f) {
                        return compressImage(f);
                    })).then(function(compressedFiles) {
                        compressedFiles.forEach(function(cf, i) {
                            console.log('[Dropzone Multi] ' + name + '[' + i + ']: ' + (newFiles[i].size / 1024 / 1024).toFixed(2) + 'MB → ' + (cf.size / 1024 / 1024).toFixed(2) + 'MB');
                        });
                        const dataTransfer = new DataTransfer();
                        compressedFiles.forEach(function(cf) {
                            dataTransfer.items.add(cf);
                        });
                        fileInput.files = dataTransfer.files;
                        _enableSubmit();
                    }).catch(function() {
                        // Fallback: use originals
                        const dataTransfer = new DataTransfer();
                        newFiles.forEach(function(f) {
                            dataTransfer.items.add(f);
                        });
                        fileInput.files = dataTransfer.files;
                        _enableSubmit();
                    });
                }

                dzInstance.on("addedfile", function(file) {
                    syncFiles();
                });

                dzInstance.on("removedfile", function(file) {
                    syncFiles();
                });
            }
        });
    });
</script>
