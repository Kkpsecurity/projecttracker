@extends('adminlte::page')

@section('title', 'Site Settings - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Site Settings</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Admin Center</a></li>
                <li class="breadcrumb-item active">Site Settings</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Settings Form -->
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Company Information -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building mr-2"></i>
                        Company Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_name">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" id="company_name"
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       value="{{ old('company_name', $settings->company_name) }}" required>
                                @error('company_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_email">Company Email</label>
                                <input type="email" name="company_email" id="company_email"
                                       class="form-control @error('company_email') is-invalid @enderror"
                                       value="{{ old('company_email', $settings->company_email) }}">
                                @error('company_email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_phone">Company Phone</label>
                                <input type="text" name="company_phone" id="company_phone"
                                       class="form-control @error('company_phone') is-invalid @enderror"
                                       value="{{ old('company_phone', $settings->company_phone) }}">
                                @error('company_phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_address">Company Address</label>
                                <textarea name="company_address" id="company_address"
                                          class="form-control @error('company_address') is-invalid @enderror"
                                          rows="3">{{ old('company_address', $settings->company_address) }}</textarea>
                                @error('company_address')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branding -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-palette mr-2"></i>
                        Branding & Appearance
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="site_logo_url">Site Logo URL</label>
                                <input type="url" name="site_logo_url" id="site_logo_url"
                                       class="form-control @error('site_logo_url') is-invalid @enderror"
                                       value="{{ old('site_logo_url', $settings->site_logo_url) }}">
                                @error('site_logo_url')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Or upload a file below</small>
                            </div>
                            <div class="form-group">
                                <label for="logo_file">Upload Logo</label>
                                <input type="file" name="logo_file" id="logo_file"
                                       class="form-control-file @error('logo_file') is-invalid @enderror"
                                       accept="image/*">
                                @error('logo_file')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="favicon_url">Favicon URL</label>
                                <input type="url" name="favicon_url" id="favicon_url"
                                       class="form-control @error('favicon_url') is-invalid @enderror"
                                       value="{{ old('favicon_url', $settings->favicon_url) }}">
                                @error('favicon_url')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Or upload a file below</small>
                            </div>
                            <div class="form-group">
                                <label for="favicon_file">Upload Favicon</label>
                                <input type="file" name="favicon_file" id="favicon_file"
                                       class="form-control-file @error('favicon_file') is-invalid @enderror"
                                       accept="image/*">
                                @error('favicon_file')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="primary_color">Primary Color <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="color" name="primary_color" id="primary_color"
                                           class="form-control @error('primary_color') is-invalid @enderror"
                                           value="{{ old('primary_color', $settings->primary_color) }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ old('primary_color', $settings->primary_color) }}</span>
                                    </div>
                                </div>
                                @error('primary_color')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="secondary_color">Secondary Color <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="color" name="secondary_color" id="secondary_color"
                                           class="form-control @error('secondary_color') is-invalid @enderror"
                                           value="{{ old('secondary_color', $settings->secondary_color) }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ old('secondary_color', $settings->secondary_color) }}</span>
                                    </div>
                                </div>
                                @error('secondary_color')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Keys -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key mr-2"></i>
                        API Keys & Integrations
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> API keys are stored securely and only visible to administrators.
                    </div>

                    <!-- Mailgun -->
                    <h5>Mailgun Settings</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mailgun_domain">Mailgun Domain</label>
                                <input type="text" name="api_keys[mailgun][domain]" id="mailgun_domain"
                                       class="form-control"
                                       value="{{ old('api_keys.mailgun.domain', $settings->api_keys['mailgun']['domain'] ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mailgun_secret">Mailgun Secret</label>
                                <input type="password" name="api_keys[mailgun][secret]" id="mailgun_secret"
                                       class="form-control"
                                       value="{{ old('api_keys.mailgun.secret', $settings->api_keys['mailgun']['secret'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Stripe -->
                    <h5>Stripe Settings</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stripe_public">Stripe Public Key</label>
                                <input type="text" name="api_keys[stripe][public]" id="stripe_public"
                                       class="form-control"
                                       value="{{ old('api_keys.stripe.public', $settings->api_keys['stripe']['public'] ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stripe_secret">Stripe Secret Key</label>
                                <input type="password" name="api_keys[stripe][secret]" id="stripe_secret"
                                       class="form-control"
                                       value="{{ old('api_keys.stripe.secret', $settings->api_keys['stripe']['secret'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Other APIs -->
                    <h5>Other APIs</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="google_maps_key">Google Maps API Key</label>
                                <input type="password" name="api_keys[google_maps]" id="google_maps_key"
                                       class="form-control"
                                       value="{{ old('api_keys.google_maps', $settings->api_keys['google_maps'] ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="backup_webhook">Backup Webhook URL</label>
                                <input type="url" name="api_keys[backup_webhook]" id="backup_webhook"
                                       class="form-control"
                                       value="{{ old('api_keys.backup_webhook', $settings->api_keys['backup_webhook'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-2"></i>
                        System Settings
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="maintenance_mode" id="maintenance_mode"
                                   class="custom-control-input" value="1"
                                   {{ old('maintenance_mode', $settings->maintenance_mode) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="maintenance_mode">
                                <strong>Maintenance Mode</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            When enabled, the site will display a maintenance page to all non-admin users.
                        </small>
                    </div>

                    @if($settings->maintenance_mode)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Warning:</strong> Maintenance mode is currently enabled. Regular users cannot access the site.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-2"></i>
                                Save Settings
                            </button>
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.settings.toggle-maintenance') }}"
                               class="btn btn-warning btn-lg mr-2"
                               onclick="return confirm('Are you sure you want to toggle maintenance mode?')">
                                <i class="fas fa-tools mr-2"></i>
                                {{ $settings->maintenance_mode ? 'Disable' : 'Enable' }} Maintenance
                            </a>
                            <a href="{{ route('admin.settings.reset') }}"
                               class="btn btn-danger btn-lg"
                               onclick="return confirm('Are you sure you want to reset all settings to defaults? This cannot be undone.')">
                                <i class="fas fa-undo mr-2"></i>
                                Reset to Defaults
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
.card-primary.card-outline {
    border-top: 3px solid #007bff;
}
.card-info.card-outline {
    border-top: 3px solid #17a2b8;
}
.card-warning.card-outline {
    border-top: 3px solid #ffc107;
}
.card-danger.card-outline {
    border-top: 3px solid #dc3545;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Update color preview on input change
    $('#primary_color, #secondary_color').on('input', function() {
        var colorValue = $(this).val();
        $(this).next('.input-group-append').find('.input-group-text').text(colorValue);
    });

    // Toggle password visibility
    $('.btn-toggle-password').click(function() {
        var input = $(this).prev('input');
        var type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });
});
</script>
@stop
