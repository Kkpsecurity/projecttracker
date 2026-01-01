{{-- HB837 Edit Form JavaScript --}}
<script>
(function initHb837ScriptsPartial(attempt) {
    attempt = attempt || 0;

    // Some layouts load jQuery after inline scripts; avoid "$ is not defined".
    if (!window.jQuery) {
        if (attempt < 100) {
            return window.setTimeout(function() { initHb837ScriptsPartial(attempt + 1); }, 50);
        }
        return;
    }

    var $ = window.jQuery;

    $(document).ready(function() {
    const storageKey = 'hb837_edit_active_tab_{{ $hb837->id }}';

    // Auto-calculate project net profit
    function calculateNetProfit() {
        let quoted = parseFloat($('#quoted_price').val()) || 0;
        let expenses = parseFloat($('#sub_fees_estimated_expenses').val()) || 0;
        let profit = quoted - expenses;
        
        // Update the net profit field
        $('#project_net_profit').val(profit.toFixed(2));
        
        // Add visual feedback
        const $profitField = $('#project_net_profit');
        $profitField.removeClass('text-success text-danger text-warning');
        
        if (quoted > 0 && expenses > 0) {
            if (profit > 0) {
                $profitField.addClass('text-success');
            } else if (profit < 0) {
                $profitField.addClass('text-danger');
            } else {
                $profitField.addClass('text-warning');
            }
        }
    }
    
    // Bind calculation to input events
    $('#quoted_price, #sub_fees_estimated_expenses').on('input change', calculateNetProfit);
    
    // Calculate on page load if values exist
    calculateNetProfit();

    // Initialize Bootstrap tabs
    $('#editTabs a[data-toggle="tab"]').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Handle tab switching with URL updates and localStorage
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr("href");
        const tabName = target.substring(1); // Remove the # symbol

        // Save to localStorage
        localStorage.setItem(storageKey, tabName);

        // Update URL without page reload
        const baseUrl = "{{ route('admin.hb837.edit', $hb837->id) }}";
        const newUrl = baseUrl + (tabName !== 'general' ? '/' + tabName : '');
        window.history.pushState({path: newUrl}, '', newUrl);

        console.log('Tab switched to:', tabName);
    });

    // Determine which tab to activate
    function getActiveTab() {
        // Priority: URL parameter > localStorage > default (general)
        const urlTab = "{{ $tab }}";
        const savedTab = localStorage.getItem(storageKey);

        console.log('URL tab:', urlTab);
        console.log('Saved tab:', savedTab);

        // If URL has a specific tab, use it
        if (urlTab && urlTab !== 'general') {
            return urlTab;
        }

        // If no URL tab but we have a saved tab, use it
        if (savedTab && savedTab !== 'general') {
            return savedTab;
        }

        // Default to general
        return 'general';
    }

    // Set active tab based on priority logic
    const activeTab = getActiveTab();
    console.log('Activating tab:', activeTab);

    if (activeTab && activeTab !== 'general') {
        // Use Bootstrap's tab method to properly show the tab
        const tabLink = '#' + activeTab + '-tab';
        console.log('Trying to activate tab link:', tabLink);
        if ($(tabLink).length > 0) {
            $(tabLink).tab('show');
            console.log('Tab activated:', tabLink);
        } else {
            console.error('Tab link not found:', tabLink);
            // Fallback to general tab
            $('#general-tab').tab('show');
        }
    } else {
        // Show general tab by default
        $('#general-tab').tab('show');
        console.log('Default general tab activated');
    }

    // File upload modal functionality
    $('#uploadModal').on('show.bs.modal', function (event) {
        // Clear form when modal opens
        $('#fileUploadForm')[0].reset();
    });

    // Handle file upload form submission
    $('#fileUploadForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var $submitBtn = $('#uploadModal .btn-primary');
        var originalText = $submitBtn.text();
        
        // Show loading state
        $submitBtn.prop('disabled', true).text('Uploading...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (typeof toastr !== 'undefined') {
                    toastr.success('File uploaded successfully');
                } else {
                    alert('File uploaded successfully');
                }
                $('#uploadModal').modal('hide');
                location.reload(); // Refresh to show new file
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Unknown error';
                if (typeof toastr !== 'undefined') {
                    toastr.error('Error uploading file: ' + message);
                } else {
                    alert('Error uploading file: ' + message);
                }
            },
            complete: function() {
                // Restore button state
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Delete file function
    window.deleteFile = function(fileId) {
        if (confirm('Are you sure you want to delete this file?')) {
            $.ajax({
                url: '/admin/hb837/files/' + fileId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('File deleted successfully');
                    } else {
                        alert('File deleted successfully');
                    }
                    location.reload();
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Unknown error';
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Error deleting file: ' + message);
                    } else {
                        alert('Error deleting file: ' + message);
                    }
                }
            });
        }
    };

    // Enhanced form validation and submission
    $('#hb837-edit-form').on('submit', function(e) {
        const requiredFields = ['property_name'];
        let isValid = true;

        requiredFields.forEach(function(field) {
            const $field = $('#' + field);
            if ($field.length && !$field.val().trim()) {
                $field.addClass('is-invalid');
                isValid = false;
            } else {
                $field.removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please fill in all required fields');
            } else {
                alert('Please fill in all required fields');
            }
            return false;
        }

        // Store current tab before form submission
        const currentActiveTab = $('.nav-link.active').attr('href');
        if (currentActiveTab) {
            const tabName = currentActiveTab.substring(1);
            localStorage.setItem(storageKey, tabName);
            console.log('Stored tab before form submission:', tabName);
        }
    });

    // Clear localStorage when navigating away from edit page
    window.addEventListener('beforeunload', function() {
        // Only clear if we're navigating to a different page (not submitting form)
        if (!$('#hb837-edit-form').data('submitting')) {
            // Don't clear - keep the tab preference
            // localStorage.removeItem(storageKey);
        }
    });

    // Mark form as submitting to avoid clearing localStorage
        $('#hb837-edit-form').on('submit', function() {
            $(this).data('submitting', true);
        });
    });
})(0);
</script>
