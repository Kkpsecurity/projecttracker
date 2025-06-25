<!-- Quoted Price -->
<div class="form-group mt-3">
    <label for="quoted_price">Quoted Price</label>
    <input type="number" step="0.01" name="quoted_price" id="quoted_price" class="form-control"
           value="{{ old('quoted_price', $hb837->quoted_price) }}"
           placeholder="Enter quoted price" required>
    @error('quoted_price')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<!-- Sub Fees & Estimated Expenses -->
<div class="form-group mt-3">
    <label for="sub_fees_estimated_expenses">Sub Fees &amp; Estimated Expenses</label>
    <input type="number" step="0.01" name="sub_fees_estimated_expenses" id="sub_fees_estimated_expenses" class="form-control"
           value="{{ old('sub_fees_estimated_expenses', $hb837->sub_fees_estimated_expenses) }}"
           placeholder="Enter sub fees or estimated expenses" required>
    @error('sub_fees_estimated_expenses')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<!-- Project Net Profit -->
<div class="form-group mt-3">
    <label for="project_net_profit">Project Net Profit</label>
    <input type="number" step="0.01" name="project_net_profit" id="project_net_profit" class="form-control"
           value="{{ old('project_net_profit', $hb837->project_net_profit) }}"
           placeholder="Enter project net profit">
    @error('project_net_profit')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<!-- Billing Request Submitted -->
<div class="form-group mt-3">
    <label for="billing_req_sent">Billing Request Submitted</label>
    <input type="date" name="billing_req_sent" id="billing_request_submitted" class="form-control"
           value="{{ old('billing_req_sent', $hb837->billing_req_sent) }}">
    @error('billing_req_sent')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<!-- Financial Notes -->
<div class="form-group mt-3">
    <label for="financial_notes">Financial Notes</label>
    <textarea name="financial_notes" id="financial_notes" class="form-control" rows="4"
              placeholder="Enter financial notes">{{ old('financial_notes', $hb837->financial_notes) }}</textarea>
    @error('financial_notes')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
