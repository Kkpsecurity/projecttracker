<?php
$active_tab = Request()->segment(4) ?? 'Active';

$tabs = [
    'general' => [
        'icon' => 'fa-info-circle',
        'name' => 'General',
        'default' => true,
    ],
    'address' => [
        'icon' => 'fa-map-marker',
        'name' => 'Address',
    ],
    'contact' => [
        'icon' => 'fa-envelope',
        'name' => 'Contact',
    ],
    'consultant' => [
        'icon' => 'fa-sticky-note',
        'name' => 'Notes',
    ],
    'financial' => [
        'icon' => 'fa-dollar',
        'name' => 'Financial',
    ],
    'files' => [
        'icon' => 'fa-file',
        'name' => 'Files',
    ],
    'google_maps' => [
        'icon' => 'fa-map',
        'name' => 'Google Maps',
    ],
];
?>


<ul class="nav nav-tabs" id="editTabs" role="tablist">
    <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab_id => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(url('admin/hb837/' . $hb837->id . '/edit/' . $tab_id )); ?>"
            class="lms-tabs btn btn-default <?php echo e($tab_id == $active_tab ? 'active' : ''); ?>">
            <i class="fa <?php echo e($tab['icon']); ?>"></i> <?php echo app('translator')->get(ucwords($tab['name'])); ?>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>
<?php /**PATH /var/www/projecttracker/resources/views/admin/hb837/partials/tabs.blade.php ENDPATH**/ ?>