

<?php $__env->startSection('title', 'Project Tracker'); ?>

<?php $__env->startSection('content_header'); ?>
    <?php echo $__env->yieldContent('content_header_content'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldContent('main_content'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom AdminLTE overrides */
        .content-wrapper, .right-side {
            min-height: calc(100vh - 50px);
        }
        
        .table th {
            border-top: none;
        }
        
        .btn {
            border-radius: 3px;
        }
        
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        
        .small-box {
            border-radius: 5px;
        }
        
        .small-box .icon {
            top: -10px;
            font-size: 70px;
        }
        
        /* Status badges */
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active { background-color: #28a745; color: white; }
        .status-quoted { background-color: #ffc107; color: #212529; }
        .status-completed { background-color: #007bff; color: white; }
        .status-closed { background-color: #dc3545; color: white; }
        
        /* Risk indicators */
        .risk-low { color: #28a745; }
        .risk-medium { color: #ffc107; }
        .risk-high { color: #dc3545; }
        
        /* Custom tabs */
        .nav-tabs .nav-link {
            border-radius: 0;
        }
        
        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .small-box .icon {
                font-size: 50px;
                top: -5px;
            }
            
            .content-header h1 {
                font-size: 1.5rem;
            }
            
            .col-lg-3 {
                margin-bottom: 15px;
            }
        }
    </style>
    <?php echo $__env->yieldContent('custom_css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    
    <script>
        // Global CSRF token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert:not(.alert-permanent)').fadeOut('slow');
        }, 5000);
        
        // Confirm deletion actions
        $(document).on('click', '.btn-delete', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
    <?php echo $__env->yieldContent('custom_js'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('adminlte_js'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('adminlte_js'); ?>
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projecttracker\resources\views/layouts/admin.blade.php ENDPATH**/ ?>