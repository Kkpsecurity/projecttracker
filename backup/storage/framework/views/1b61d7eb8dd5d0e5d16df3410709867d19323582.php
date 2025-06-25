<section style="min-height: 450px; height: auto;">
    <div class="form-group mt-3">
        <label for="address">Address</label>
        <input type="text" name="address" id="address" class="form-control"
               value="<?php echo e(old('address', $hb837->address)); ?>"
               placeholder="Enter your address" required autocomplete="street-address">
        <?php $__errorArgs = ['address'];
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
        <label for="city">City</label>
        <input type="text" name="city" id="city" class="form-control"
               value="<?php echo e(old('city', $hb837->city)); ?>"
               placeholder="Enter your city" required autocomplete="address-level2">
        <?php $__errorArgs = ['city'];
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
        <label for="state">State</label>
        <input type="text" name="state" id="state" class="form-control" maxlength="2"
               value="<?php echo e(old('state', $hb837->state)); ?>"
               placeholder="State (e.g., FL)" required autocomplete="address-level1">
        <?php $__errorArgs = ['state'];
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
        <label for="zip">Zip Code</label>
        <input type="text" name="zip" id="zip" class="form-control" maxlength="10"
               value="<?php echo e(old('zip', $hb837->zip)); ?>"
               placeholder="Enter zip code" required autocomplete="postal-code">
        <?php $__errorArgs = ['zip'];
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

    <div class="form-group">
        <label for="county">County</label>
        <input type="text" name="county" id="county" class="form-control"
               value="<?php echo e(old('county', $hb837->county)); ?>"
               placeholder="Enter your county" required autocomplete="address-level2">
        <?php $__errorArgs = ['county'];
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
<?php /**PATH /var/www/projecttracker/resources/views/admin/hb837/partials/address.blade.php ENDPATH**/ ?>