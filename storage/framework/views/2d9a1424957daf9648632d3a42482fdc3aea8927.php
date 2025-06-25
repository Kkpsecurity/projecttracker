<div class="row">
    <div class="col-md-12">
        <!-- Notes -->
        <div class="form-group mt-3">
            <label for="notes">Notes</label>
            <textarea
                class="form-control"
                id="notes"
                name="notes"
                rows="8"><?php echo e(old('notes', $hb837->notes)); ?></textarea>
            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="text-danger small"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Consultant Notes -->
        <div class="form-group mt-3">
            <label for="consultant_notes">Notes to Consultant</label>
            <textarea
                class="form-control"
                id="consultant_notes"
                name="consultant_notes"
                rows="8"><?php echo e(old('consultant_notes', $hb837->consultant_notes)); ?></textarea>
            <?php $__errorArgs = ['consultant_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="text-danger small"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
</div>
<?php /**PATH /var/www/projecttracker/resources/views/admin/hb837/partials/notes.blade.php ENDPATH**/ ?>