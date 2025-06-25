<!-- Quoted Price -->
<div class="form-group mt-3">
    <label for="quoted_price">Quoted Price</label>
    <input type="number" step="0.01" name="quoted_price" id="quoted_price" class="form-control"
           value="<?php echo e(old('quoted_price', $hb837->quoted_price)); ?>"
           placeholder="Enter quoted price" required>
    <?php $__errorArgs = ['quoted_price'];
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

<!-- Sub Fees & Estimated Expenses -->
<div class="form-group mt-3">
    <label for="sub_fees_estimated_expenses">Sub Fees &amp; Estimated Expenses</label>
    <input type="number" step="0.01" name="sub_fees_estimated_expenses" id="sub_fees_estimated_expenses" class="form-control"
           value="<?php echo e(old('sub_fees_estimated_expenses', $hb837->sub_fees_estimated_expenses)); ?>"
           placeholder="Enter sub fees or estimated expenses" required>
    <?php $__errorArgs = ['sub_fees_estimated_expenses'];
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

<!-- Project Net Profit -->
<div class="form-group mt-3">
    <label for="project_net_profit">Project Net Profit</label>
    <input type="number" step="0.01" name="project_net_profit" id="project_net_profit" class="form-control"
           value="<?php echo e(old('project_net_profit', $hb837->project_net_profit)); ?>"
           placeholder="Enter project net profit">
    <?php $__errorArgs = ['project_net_profit'];
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

<!-- Billing Request Submitted -->
<div class="form-group mt-3">
    <label for="billing_req_sent">Billing Request Submitted</label>
    <input type="date" name="billing_req_sent" id="billing_request_submitted" class="form-control"
           value="<?php echo e(old('billing_req_sent', $hb837->billing_req_sent)); ?>">
    <?php $__errorArgs = ['billing_req_sent'];
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

<!-- Financial Notes -->
<div class="form-group mt-3">
    <label for="financial_notes">Financial Notes</label>
    <textarea name="financial_notes" id="financial_notes" class="form-control" rows="4"
              placeholder="Enter financial notes"><?php echo e(old('financial_notes', $hb837->financial_notes)); ?></textarea>
    <?php $__errorArgs = ['financial_notes'];
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
<?php /**PATH /var/www/projecttracker/resources/views/admin/hb837/partials/financial.blade.php ENDPATH**/ ?>