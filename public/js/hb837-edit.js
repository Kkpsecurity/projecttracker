/**
 * HB837 Edit Page JavaScript
 * Handles dynamic tab functionality and form management
 */

class HB837EditManager {
    constructor() {
        this.currentTab = 'general';
        this.formData = {};
        this.isInitialized = false;
        
        this.init();
    }

    init() {
        if (this.isInitialized) return;
        
        this.bindEvents();
        this.initializeComponents();
        this.setupFormValidation();
        this.isInitialized = true;
        
        console.log('HB837 Edit Manager initialized');
    }

    bindEvents() {
        // Tab switching
        $('#editTabs a[data-toggle="tab"]').on('shown.bs.tab', (e) => {
            const tabId = $(e.target).attr('href').substring(1);
            this.handleTabChange(tabId);
        });

        // Form submissions
        $('form[id$="-form"]').on('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmit($(e.target));
        });

        // Auto-save functionality (optional)
        $('form[id$="-form"] input, form[id$="-form"] textarea, form[id$="-form"] select').on('change', (e) => {
            this.scheduleAutoSave($(e.target));
        });

        // Keyboard shortcuts
        $(document).on('keydown', (e) => {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                this.saveCurrentTab();
            }
        });
    }

    setupFormValidation() {
        // Disable HTML5 validation to use our custom validation
        $('form[id$="-form"]').attr('novalidate', true);
        
        // Custom validation will be handled by validateForm method
        console.log('Form validation setup completed');
    }

    initializeComponents() {
        // Initialize date pickers
        $('input[type="date"]').each(function() {
            if ($(this).hasClass('daterangepicker-applied')) return;
            
            $(this).daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });

        // Initialize Select2
        $('select').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) return;
            
            $(this).select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: $(this).find('option:first').text()
            });
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialize popovers
        $('[data-toggle="popover"]').popover();
    }

    handleTabChange(tabId) {
        this.currentTab = tabId;
        
        // Update URL without page reload
        const url = window.location.pathname + '/' + tabId;
        history.pushState({}, '', url);
        
        // Re-initialize components for the new tab
        setTimeout(() => {
            this.initializeComponents();
        }, 100);
        
        // Track tab usage
        this.trackTabUsage(tabId);
    }

    handleFormSubmit(form) {
        const formId = form.attr('id');
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Validate form before submission
        if (!this.validateForm(form)) {
            return;
        }

        // Show loading state
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        submitBtn.prop('disabled', true);

        // Remove existing error states
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        // Submit via AJAX
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: (response) => {
                this.handleFormSuccess(response, form);
            },
            error: (xhr) => {
                this.handleFormError(xhr, form);
            },
            complete: () => {
                // Reset button state
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    }

    handleFormSuccess(response, form) {
        // Show success message
        this.showNotification('Changes saved successfully!', 'success');
        
        // Clear any previous errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // Update form data cache
        this.updateFormDataCache(form);
        
        // Show saved indicator
        this.showSavedIndicator(form);
    }

    handleFormError(xhr, form) {
        if (xhr.status === 422) {
            // Validation errors
            const errors = xhr.responseJSON.errors;
            
            $.each(errors, (field, messages) => {
                const input = form.find(`[name="${field}"]`);
                input.addClass('is-invalid');
                input.after(`<div class="invalid-feedback">${messages[0]}</div>`);
            });
            
            this.showNotification('Please correct the errors and try again.', 'error');
        } else {
            this.showNotification('An error occurred while saving changes.', 'error');
        }
    }

    validateForm(form) {
        let isValid = true;
        
        // Check required fields
        form.find('[required]').each(function() {
            const field = $(this);
            const value = field.val();
            
            if (!value || value.trim() === '') {
                field.addClass('is-invalid');
                if (field.next('.invalid-feedback').length === 0) {
                    field.after('<div class="invalid-feedback">This field is required.</div>');
                }
                isValid = false;
            } else {
                field.removeClass('is-invalid');
                field.next('.invalid-feedback').remove();
            }
        });
        
        // Email validation
        form.find('input[type="email"]').each(function() {
            const field = $(this);
            const value = field.val();
            
            if (value && !this.isValidEmail(value)) {
                field.addClass('is-invalid');
                if (field.next('.invalid-feedback').length === 0) {
                    field.after('<div class="invalid-feedback">Please enter a valid email address.</div>');
                }
                isValid = false;
            }
        });
        
        return isValid;
    }

    isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    scheduleAutoSave(field) {
        // Clear any existing timeout
        if (this.autoSaveTimeout) {
            clearTimeout(this.autoSaveTimeout);
        }
        
        // Schedule auto-save after 2 seconds of inactivity
        this.autoSaveTimeout = setTimeout(() => {
            this.performAutoSave(field);
        }, 2000);
    }

    performAutoSave(field) {
        const form = field.closest('form');
        const tabId = form.attr('id').replace('-form', '');
        
        // Only auto-save if form is valid
        if (this.validateForm(form)) {
            this.showAutoSaveIndicator(form);
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: () => {
                    this.hideAutoSaveIndicator(form);
                    this.showSavedIndicator(form);
                },
                error: () => {
                    this.hideAutoSaveIndicator(form);
                }
            });
        }
    }

    saveCurrentTab() {
        const currentForm = $(`#${this.currentTab}-form`);
        if (currentForm.length) {
            currentForm.submit();
        }
    }

    showNotification(message, type) {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

    showSavedIndicator(form) {
        const tabId = form.attr('id').replace('-form', '');
        const tabLink = $(`#${tabId}-tab`);
        
        // Add saved indicator
        if (tabLink.find('.saved-indicator').length === 0) {
            tabLink.append('<span class="saved-indicator badge badge-success badge-sm ml-1">Saved</span>');
        }
        
        // Remove after 3 seconds
        setTimeout(() => {
            tabLink.find('.saved-indicator').fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    showAutoSaveIndicator(form) {
        const tabId = form.attr('id').replace('-form', '');
        const tabLink = $(`#${tabId}-tab`);
        
        if (tabLink.find('.autosave-indicator').length === 0) {
            tabLink.append('<span class="autosave-indicator badge badge-info badge-sm ml-1">Saving...</span>');
        }
    }

    hideAutoSaveIndicator(form) {
        const tabId = form.attr('id').replace('-form', '');
        const tabLink = $(`#${tabId}-tab`);
        
        tabLink.find('.autosave-indicator').remove();
    }

    updateFormDataCache(form) {
        const tabId = form.attr('id').replace('-form', '');
        this.formData[tabId] = form.serialize();
    }

    trackTabUsage(tabId) {
        // Track which tabs are being used most
        if (!this.tabUsage) {
            this.tabUsage = {};
        }
        
        this.tabUsage[tabId] = (this.tabUsage[tabId] || 0) + 1;
    }
}

// Initialize when document is ready
$(document).ready(function() {
    if (typeof window.hb837EditManager === 'undefined') {
        window.hb837EditManager = new HB837EditManager();
    }
});

// Additional utilities
window.HB837Utils = {
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },
    
    formatDate: function(date) {
        return new Intl.DateTimeFormat('en-US').format(new Date(date));
    },
    
    validateRequired: function(form) {
        return window.hb837EditManager.validateForm(form);
    }
};
