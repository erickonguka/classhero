// Teacher Course Management JavaScript
document.addEventListener('alpine:init', () => {
    Alpine.data('courseForm', () => ({
        isLoading: false,
        previewImage: null,
        formData: {
            title: '',
            category_id: '',
            short_description: '',
            description: '',
            difficulty: 'beginner',
            duration_hours: '',
            is_free: '1',
            price: '',
            what_you_learn: [''],
            requirements: [''],
            tags: [],
            thumbnail: null
        },
        tagsInput: '',
        errors: {},

        init() {
            // Initialize with old values if available
            this.initializeFormData();
        },

        initializeFormData() {
            // Get old values from Laravel's old() helper if available
            const oldData = window.oldFormData || {};
            Object.keys(oldData).forEach(key => {
                if (this.formData.hasOwnProperty(key)) {
                    this.formData[key] = oldData[key];
                }
            });

            // Ensure arrays have at least one empty field
            ['what_you_learn', 'requirements'].forEach(field => {
                if (!this.formData[field] || this.formData[field].length === 0) {
                    this.formData[field] = [''];
                }
            });
            
            // Initialize tags as empty array
            if (!this.formData.tags) {
                this.formData.tags = [];
            }
        },

        createFormData() {
            const formData = new FormData();
            
            // Add basic fields (CSRF token will be handled globally)
            Object.keys(this.formData).forEach(key => {
                if (key === 'thumbnail' && this.formData[key]) {
                    formData.append(key, this.formData[key]);
                } else if (Array.isArray(this.formData[key])) {
                    // Handle arrays
                    if (key === 'tags') {
                        this.formData[key].forEach(item => formData.append(`${key}[]`, item));
                    } else {
                        this.formData[key]
                            .filter(item => item && item.toString().trim())
                            .forEach(item => formData.append(`${key}[]`, item));
                    }
                } else if (key !== 'thumbnail') {
                    formData.append(key, this.formData[key] || '');
                }
            });
            
            return formData;
        },

        validateForm() {
            this.errors = {};
            let isValid = true;

            // Title validation
            const titleError = AjaxUtils.validateRequired(this.formData.title, 'Course title') ||
                              AjaxUtils.validateMaxLength(this.formData.title, 255, 'Course title');
            if (titleError) {
                this.errors.title = titleError;
                isValid = false;
            }

            // Category validation
            if (!this.formData.category_id) {
                this.errors.category_id = 'Please select a category';
                isValid = false;
            }

            // Short description validation
            const shortDescError = AjaxUtils.validateRequired(this.formData.short_description, 'Short description') ||
                                  AjaxUtils.validateMaxLength(this.formData.short_description, 500, 'Short description');
            if (shortDescError) {
                this.errors.short_description = shortDescError;
                isValid = false;
            }

            // Full description validation
            const descError = AjaxUtils.validateRequired(this.formData.description, 'Full description');
            if (descError) {
                this.errors.description = descError;
                isValid = false;
            }

            // Duration validation
            if (!this.formData.duration_hours || this.formData.duration_hours < 1) {
                this.errors.duration_hours = 'Duration must be at least 1 hour';
                isValid = false;
            }

            // Price validation for paid courses
            if (this.formData.is_free == '0' && (!this.formData.price || this.formData.price < 0)) {
                this.errors.price = 'Please enter a valid price';
                isValid = false;
            }

            // Learning outcomes validation
            const validLearningOutcomes = this.formData.what_you_learn.filter(item => item?.trim());
            if (validLearningOutcomes.length === 0) {
                this.errors.what_you_learn = 'At least one learning outcome is required';
                isValid = false;
            }

            // Requirements validation
            const validRequirements = this.formData.requirements.filter(item => item?.trim());
            if (validRequirements.length === 0) {
                this.errors.requirements = 'At least one requirement is required';
                isValid = false;
            }

            // Tags validation
            if (this.formData.tags.length === 0) {
                this.errors.tags = 'At least one tag is required';
                isValid = false;
            }

            // Thumbnail validation
            if (this.formData.thumbnail) {
                if (this.formData.thumbnail.size > 2 * 1024 * 1024) {
                    this.errors.thumbnail = 'Thumbnail must be less than 2MB';
                    isValid = false;
                } else if (!['image/jpeg', 'image/png', 'image/gif'].includes(this.formData.thumbnail.type)) {
                    this.errors.thumbnail = 'Thumbnail must be JPEG, PNG, or GIF';
                    isValid = false;
                }
            }

            return isValid;
        },

        scrollToFirstError() {
            this.$nextTick(() => {
                const firstErrorElement = this.$el.querySelector('.border-red-500');
                if (firstErrorElement) {
                    firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        },

        async submitForm() {
            if (!this.validateForm()) {
                this.scrollToFirstError();
                return;
            }

            try {
                const formData = this.createFormData();
                
                const response = await AjaxUtils.makeRequest(
                    'POST',
                    window.courseRoutes.store,
                    formData,
                    {
                        headers: { 'Accept': 'application/json' },
                        successMessage: 'Course created successfully!',
                        errorMessage: 'Failed to create course',
                        setLoading: (loading) => { this.isLoading = loading; }
                    }
                );

                if (response.redirect_url) {
                    setTimeout(() => {
                        window.location.href = response.redirect_url;
                    }, 1500);
                }
            } catch (error) {
                console.error('Submission error:', error);
                
                // Handle CSRF token mismatch specifically
                if (error.message && error.message.includes('CSRF token mismatch')) {
                    toastr.error('Security token expired. Please refresh the page and try again.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    return;
                }
                
                if (error.status === 422 && error.response?.errors) {
                    this.errors = error.response.errors;
                    this.scrollToFirstError();
                } else {
                    toastr.error(error.message || 'An unexpected error occurred. Please try again.');
                }
            }
        },

        // Array field management
        addField(fieldName) {
            this.formData[fieldName].push('');
        },

        removeField(fieldName, index) {
            this.formData[fieldName].splice(index, 1);
            // Ensure at least one empty field exists
            if (this.formData[fieldName].length === 0) {
                this.formData[fieldName].push('');
            }
        },

        // File handling
        previewThumbnail(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                this.formData.thumbnail = input.files[0];
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                this.previewImage = null;
                this.formData.thumbnail = null;
            }
        },
        
        // Tags management
        updateTagsFromInput() {
            if (this.tagsInput) {
                this.formData.tags = this.tagsInput.split(',').map(tag => tag.trim()).filter(tag => tag);
            } else {
                this.formData.tags = [];
            }
        },
        
        removeTag(index) {
            this.formData.tags.splice(index, 1);
            this.tagsInput = this.formData.tags.join(', ');
        }
    }));

    // Course list management
    Alpine.data('courseList', () => ({
        isLoading: false,

        async deleteCourse(courseId, courseName) {
            try {
                await AjaxUtils.confirmAndRequest({
                    title: 'Delete Course?',
                    text: `Are you sure you want to delete "${courseName}"? This action cannot be undone.`,
                    confirmButtonText: 'Yes, delete it!',
                    method: 'DELETE',
                    url: `/teacher/courses/${courseId}`,
                    successMessage: 'Course deleted successfully!',
                    onSuccess: () => {
                        // Remove the course row from the table
                        const courseRow = document.querySelector(`[data-course-id="${courseId}"]`);
                        if (courseRow) {
                            courseRow.remove();
                        }
                    }
                });
            } catch (error) {
                console.error('Delete error:', error);
            }
        },

        async publishCourse(courseId) {
            try {
                const response = await AjaxUtils.makeRequest(
                    'POST',
                    `/teacher/courses/${courseId}/publish`,
                    {},
                    {
                        successMessage: 'Course submitted for approval!',
                        setLoading: (loading) => { this.isLoading = loading; }
                    }
                );

                // Update the status badge
                const statusBadge = document.querySelector(`[data-course-id="${courseId}"] .status-badge`);
                if (statusBadge) {
                    statusBadge.textContent = 'Pending';
                    statusBadge.className = 'status-badge px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800';
                }
            } catch (error) {
                console.error('Publish error:', error);
            }
        }
    }));
});