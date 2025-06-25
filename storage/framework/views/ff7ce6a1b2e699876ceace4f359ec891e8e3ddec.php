<section style="min-height: 450px; height: auto;">
    <div class="form-group mt-3">
        <label for="owner_name">Owner Name</label>
        <input type="text" name="owner_name" id="owner_name" class="form-control"
            value="<?php echo e(old('owner_name', $hb837->owner_name)); ?>"
            placeholder="Enter owner's name" autocomplete="name">
        <?php $__errorArgs = ['owner_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group mt-3">
        <label for="property_manager_name">Property Manager Name</label>
        <input type="text" name="property_manager_name" id="property_manager_name" class="form-control"
            value="<?php echo e(old('property_manager_name', $hb837->property_manager_name)); ?>"
            placeholder="Enter property manager's name" autocomplete="name">
        <?php $__errorArgs = ['property_manager_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group mt-3">
        <label for="property_manager_email">Property Manager Email</label>
        <div class="input-group">
            <input type="email" name="property_manager_email" id="property_manager_email" class="form-control"
                value="<?php echo e(old('property_manager_email', $hb837->property_manager_email)); ?>"
                placeholder="Enter property manager's email" autocomplete="email">
            <button type="button" class="btn btn-outline-secondary"
                onclick="window.location.href='mailto:<?php echo e($hb837->property_manager_email); ?>'">
                <i class="fa fa-envelope"></i>
            </button>
        </div>
        <?php $__errorArgs = ['property_manager_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group mt-3">
        <label for="regional_manager_name">Regional Manager Name</label>
        <input type="text" name="regional_manager_name" id="regional_manager_name" class="form-control"
            value="<?php echo e(old('regional_manager_name', $hb837->regional_manager_name)); ?>"
            placeholder="Enter regional manager's name" autocomplete="name">
        <?php $__errorArgs = ['regional_manager_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>


    <div class="form-group mt-3">
        <label for="regional_manager_email">Regional Manager Email</label>
        <div class="input-group">
            <input type="email" name="regional_manager_email" id="regional_manager_email" class="form-control"
                value="<?php echo e(old('regional_manager_email', $hb837->regional_manager_email)); ?>"
                placeholder="Enter regional manager's email" autocomplete="email">
            <button type="button" class="btn btn-outline-secondary"
                onclick="window.location.href='mailto:<?php echo e($hb837->regional_manager_email); ?>'">
                <i class="fa fa-envelope"></i>
            </button>
        </div>
        <?php $__errorArgs = ['regional_manager_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group mt-3">
        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control"
            value="<?php echo e(old('phone', $hb837->phone)); ?>"
            placeholder="Enter phone number" autocomplete="tel">
        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</section>
<?php /**PATH /var/www/projecttracker/resources/views/admin/hb837/partials/contact.blade.php ENDPATH**/ ?>