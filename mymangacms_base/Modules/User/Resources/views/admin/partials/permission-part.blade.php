<div class="row permission">
    <div class="col-sm-4">
        <div class="visible-sm-block visible-md-block visible-lg-block">
            <label class="control-label text-right" style="display: block">{{ trans($permissionLabel) }}</label>
        </div>
        <div class="visible-xs-block">
            <label class="control-label">{{ trans($permissionLabel) }}</label>
        </div>
    </div>
    <div class="col-sm-8">
        <?php if (isset($model)): ?>
            <?php $current = current_permission_value($model, $subPermissionTitle, $permissionAction); ?>
        <?php endif; ?>
        <label class="radio-inline" for="{{ $subPermissionTitle. '.' . $permissionAction }}_allow">
            <input type="radio" value="1" id="{{ $subPermissionTitle. '.' . $permissionAction }}_allow" name="permissions[{{ $subPermissionTitle. '.' . $permissionAction }}]"
                {{ isset($current) && $current === 1 ? 'checked' : '' }} class="flat-blue jsAllow">
            allow
        </label>
        <span style="margin:0 10px;"></span>
        <label class="radio-inline" for="{{ $subPermissionTitle. '.' . $permissionAction }}_deny">
            <input type="radio" value="0" id="{{ $subPermissionTitle. '.' . $permissionAction }}_deny" name="permissions[{{ $subPermissionTitle. '.' . $permissionAction }}]"
                    {{ isset($current) && $current === 0 ? 'checked' : '' }} class="flat-blue jsDeny">
            deny
        </label>
    </div>
</div>
