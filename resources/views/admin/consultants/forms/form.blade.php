<style>
    .custom-form {
      background-color: #f8f9fa;
      color: #212529;
      padding: 30px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .custom-form label {
      font-weight: 600;
      color: #495057;
    }
    .custom-form input,
    .custom-form textarea,
    .custom-form select {
      background-color: #fff;
      border: 1px solid #ced4da;
      color: #495057;
    }
    .uploaded-files-container {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    .file-item {
      display: flex;
      align-items: center;
      gap: 5px;
      padding: 5px;
      border: 1px solid #dee2e6;
      border-radius: 3px;
    }
  </style>

  @php
    function getFileIcon($type)
    {
        switch ($type) {
            case 'pdf':
                return '<i class="fa fa-file-pdf-o fa-2x text-danger"></i>';
            case 'doc':
            case 'docx':
                return '<i class="fa fa-file-word-o fa-2x text-primary"></i>';
            case 'xls':
            case 'xlsx':
                return '<i class="fa fa-file-excel-o fa-2x text-success"></i>';
            case 'ppt':
            case 'pptx':
                return '<i class="fa fa-file-powerpoint-o fa-2x text-warning"></i>';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return '<i class="fa fa-file-image-o fa-2x text-info"></i>';
            case 'zip':
            case 'rar':
                return '<i class="fa fa-file-archive-o fa-2x text-secondary"></i>';
            default:
                return '<i class="fa fa-file-o fa-2x"></i>';
        }
    }
    $url = route('admin.consultants.update', $consultant->id);
  @endphp

  @include('partials.messages')

  <form action="{{ $url }}" method="POST" enctype="multipart/form-data" class="custom-form">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <div class="mb-3">
          <label for="first_name" class="form-label">First Name:</label>
          <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" value="{{ old('first_name', $consultant->first_name ?? '') }}">
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="mb-3">
          <label for="last_name" class="form-label">Last Name:</label>
          <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" value="{{ old('last_name', $consultant->last_name ?? '') }}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="{{ old('email', $consultant->email ?? '') }}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <label for="dba_company_name" class="form-label">Company Name:</label>
          <input type="text" name="dba_company_name" id="dba_company_name" class="form-control" placeholder="Company Name" value="{{ old('dba_company_name', $consultant->dba_company_name ?? '') }}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <label for="mailing_address" class="form-label">Mailing Address:</label>
          <input type="text" name="mailing_address" id="mailing_address" class="form-control" placeholder="Mailing Address" value="{{ old('mailing_address', $consultant->mailing_address ?? '') }}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <label for="fcp_expiration_date" class="form-label">FCP Expiration Date:</label>
          <input type="date" name="fcp_expiration_date" id="fcp_expiration_date" class="form-control" value="{{ old('fcp_expiration_date', $consultant->fcp_expiration_date ? $consultant->fcp_expiration_date->format('Y-m-d') : '') }}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <label for="assigned_light_meter" class="form-label">Assigned Light Meter:</label>
          <input type="text" name="assigned_light_meter" id="assigned_light_meter" class="form-control" placeholder="Assigned Light Meter" value="{{ old('assigned_light_meter', $consultant->assigned_light_meter ?? '') }}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <label for="lm_nist_expiration_date" class="form-label">LM NIST Expiration Date:</label>
          <input type="date" name="lm_nist_expiration_date" id="lm_nist_expiration_date" class="form-control" value="{{ old('lm_nist_expiration_date', $consultant->lm_nist_expiration_date ? $consultant->lm_nist_expiration_date->format('Y-m-d') : '') }}">
        </div>
      </div>

      <div class="col-md-6">
        <div class="mb-3">
          <label for="files" class="form-label">Upload:</label>
          <input type="file" name="files[]" id="files" class="form-control" multiple>
        </div>
      </div>

      @if (isset($consultant))
        <div class="col-12">
          <div class="mb-3">
            <label class="form-label">Uploaded Files:</label>
            <div class="uploaded-files-container">
              @foreach ($consultant->files as $file)
                <div class="file-item">
                  <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">
                    {!! getFileIcon($file->type) !!}
                  </a>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="delete_files[]" id="delete_file_{{ $file->id }}" value="{{ $file->id }}">
                    <label class="form-check-label" for="delete_file_{{ $file->id }}">Delete</label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endif

      <div class="col-12">
        <div class="mb-3">
          <label for="subcontractor_bonus_rate" class="form-label">Subcontractor Bonus Rate:</label>
          <textarea class="form-control" name="subcontractor_bonus_rate" id="subcontractor_bonus_rate" placeholder="Subcontractor Bonus Rate" style="height:150px;">{{ old('subcontractor_bonus_rate', $consultant->subcontractor_bonus_rate ?? '') }}</textarea>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label for="notes" class="form-label">Notes:</label>
          <textarea class="form-control" name="notes" id="notes" placeholder="Notes" style="height:150px;">{{ old('notes', $consultant->notes ?? '') }}</textarea>
        </div>
      </div>

      <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </form>
