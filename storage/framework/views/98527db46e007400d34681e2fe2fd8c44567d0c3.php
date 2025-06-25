<div class="row">
    <!-- Left Column -->
    <div class="col-md-6">
        <!-- Property Name -->
        <div class="form-group mt-3">
            <label for="property_name">Property Name</label>
            <input type="text" name="property_name" id="property_name" class="form-control"
                value="<?php echo e(old('property_name', $hb837->property_name)); ?>" placeholder="Enter property name">
            <?php $__errorArgs = ['property_name'];
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

        <!-- Property Type (required) -->
        <div class="form-group mt-3">
            <label for="property_type">Property Type</label>
            <?php
                $property_types = config('hb837.property_types');
            ?>
            <select name="property_type" id="property_type" class="form-control" required>
                <option value="">Select property type</option>
                <?php $__currentLoopData = $property_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type); ?>"
                        <?php echo e(old('property_type', $hb837->property_type) == $type ? 'selected' : ''); ?>>
                        <?php echo e(Str::ucfirst($type)); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['property_type'];
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

        <!-- Units (changed to number input) -->
        <div class="form-group mt-3">
            <label for="units">Units</label>
            <input type="number" name="units" id="units" class="form-control"
                value="<?php echo e(old('units', $hb837->units)); ?>" placeholder="Enter number of units" min="0">
            <?php $__errorArgs = ['units'];
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

        <!-- Management Company -->
        <div class="form-group mt-3">
            <label for="management_company">Management Company</label>
            <input type="text" name="management_company" id="management_company" class="form-control"
                value="<?php echo e(old('management_company', $hb837->management_company)); ?>"
                placeholder="Enter management company">
            <?php $__errorArgs = ['management_company'];
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

        <!-- SecurityGauge Crime Risk Dropdown -->
        <div class="form-group mt-3">
            <label for="securitygauge_crime_risk">SecurityGauge Crime Risk</label>
            <?php
                $securitygauge_crime_risks = config('hb837.security_gauge');
            ?>
            <select name="securitygauge_crime_risk" id="securitygauge_crime_risk" class="form-control">
                <option value="">Select a Crime Risk</option>
                <?php $__currentLoopData = $securitygauge_crime_risks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $risk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($risk); ?>"
                        <?php echo e(old('securitygauge_crime_risk', $hb837->securitygauge_crime_risk) == $risk ? 'selected' : ''); ?>>
                        <?php echo e(Str::ucfirst($risk)); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['securitygauge_crime_risk'];
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

        <!-- Consultant Selection / Info -->
        <div class="form-group mt-3">
            <?php
                $consultants = App\Models\Consultant::all();
            ?>
            <label for="assigned_consultant_id">Select Consultant</label>
            <select name="assigned_consultant_id" id="assigned_consultant_id" class="form-control">
                <option value="-1">Select a Consultant</option>
                <?php $__currentLoopData = $consultants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $consultant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($consultant->id); ?>"
                        <?php echo e((old('assigned_consultant_id') ?? $hb837->assigned_consultant_id) == $consultant->id ? 'selected' : ''); ?>>
                        <?php echo e($consultant->first_name); ?> <?php echo e($consultant->last_name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['assigned_consultant_id'];
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
    </div>

    <!-- Right Column -->
    <div class="col-md-6">
        <div class="form-group mt-3">
            <label for="scheduled_date_of_inspection">Date of Scheduled Inspection</label>
            <input type="date" name="scheduled_date_of_inspection" id="scheduled_inspection_date"
                class="form-control"
                value="<?php echo e(old('scheduled_date_of_inspection', $hb837->scheduled_date_of_inspection)); ?>">
            <?php $__errorArgs = ['scheduled_date_of_inspection'];
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

        <!-- Report Status (required) -->
        <div class="form-group mt-3">
            <label for="report_status">Report Status</label>
            <?php
                $report_statuses = config('hb837.report_statuses');
            ?>
            <select name="report_status" id="report_status" class="form-control" required>
                <option value="">Select report status</option>
                <?php $__currentLoopData = $report_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status); ?>"
                        <?php echo e(old('report_status', $hb837->report_status) == $status ? 'selected' : ''); ?>>
                        <?php echo e(Str::ucfirst($status)); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['report_status'];
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
            <div>
                <label for="report_submitted" class="form-label">Report Submitted:</label>
                <input type="date" name="report_submitted" id="report_submitted" class="form-control"
                    value="<?php echo e(old('report_submitted',  $hb837->report_submitted)); ?>">
            </div>
        </div>

        <!-- Contracting Status (required) -->
        <div class="form-group mt-3">
            <label for="contracting_status">Contracting Status</label>
            <?php
                $contracting_statuses = config('hb837.contracting_statuses');
            ?>
            <select name="contracting_status" id="contracting_status" class="form-control" required>
                <option value="">Select contracting status</option>
                <?php $__currentLoopData = $contracting_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status); ?>"
                        <?php echo e(old('contracting_status', $hb837->contracting_status) == $status ? 'selected' : ''); ?>>
                        <?php echo e(Str::ucfirst($status)); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['contracting_status'];
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


        <div class="col-md-6">
            <div class="mb-3">
                <label for="agreement_submitted" class="form-label">Agreement Submitted:</label>
                <input type="date" name="agreement_submitted" id="agreement_submitted" class="form-control"
                    value="<?php echo e(old('agreement_submitted', isset($consultant->agreement_submitted) && $consultant->agreement_submitted ? $consultant->agreement_submitted->format('Y-m-d') : '')); ?>">
            </div>
        </div>

        <!-- Macro Client -->
        <div class="form-group mt-3">
            <label for="macro_client">Macro Client</label>
            <input type="text" name="macro_client" id="macro_client" class="form-control"
                value="<?php echo e(old('macro_client', $hb837->macro_client)); ?>" placeholder="Enter macro client">
            <?php $__errorArgs = ['macro_client'];
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

        <!-- Macro Contact -->
        <div class="form-group mt-3">
            <label for="macro_contact">Macro Contact</label>
            <input type="text" name="macro_contact" id="macro_contact" class="form-control"
                value="<?php echo e(old('macro_contact', $hb837->macro_contact)); ?>" placeholder="Enter macro contact">
            <?php $__errorArgs = ['macro_contact'];
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

        <!-- Macro Email -->
        <div class="form-group mt-3">
            <label for="macro_email">Macro Email</label>
            <input type="email" name="macro_email" id="macro_email" class="form-control"
                value="<?php echo e(old('macro_email', $hb837->macro_email)); ?>" placeholder="Enter macro email"
                autocomplete="email">
            <?php $__errorArgs = ['macro_email'];
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
    </div>
</div>
<?php /**PATH /var/www/projecttracker/resources/views/admin/hb837/partials/general.blade.php ENDPATH**/ ?>