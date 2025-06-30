{{-- Financial Information Tab Content --}}
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="quoted_price">Quoted Price</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                <input type="number" step="0.01" class="form-control @error('quoted_price') is-invalid @enderror" 
                       id="quoted_price" name="quoted_price" 
                       value="{{ old('quoted_price', $hb837->quoted_price ?? '') }}" 
                       placeholder="0.00">
                @error('quoted_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="sub_fees_estimated_expenses">Estimated Expenses</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                <input type="number" step="0.01" class="form-control @error('sub_fees_estimated_expenses') is-invalid @enderror" 
                       id="sub_fees_estimated_expenses" name="sub_fees_estimated_expenses" 
                       value="{{ old('sub_fees_estimated_expenses', $hb837->sub_fees_estimated_expenses ?? '') }}" 
                       placeholder="0.00">
                @error('sub_fees_estimated_expenses')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="project_net_profit">Net Profit</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                <input type="number" step="0.01" class="form-control @error('project_net_profit') is-invalid @enderror" 
                       id="project_net_profit" name="project_net_profit" 
                       value="{{ old('project_net_profit', $hb837->project_net_profit ?? '') }}" 
                       placeholder="0.00" readonly>
                @error('project_net_profit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <small class="form-text text-muted">Automatically calculated: Quoted Price - Expenses</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="billing_req_sent">Billing Request Sent</label>
            <input type="date" class="form-control @error('billing_req_sent') is-invalid @enderror" 
                   id="billing_req_sent" name="billing_req_sent" 
                   value="{{ old('billing_req_sent', $hb837->billing_req_sent ? \Carbon\Carbon::parse($hb837->billing_req_sent)->format('Y-m-d') : '') }}">
            @error('billing_req_sent')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="report_submitted">Report Submitted</label>
            <input type="date" class="form-control @error('report_submitted') is-invalid @enderror" 
                   id="report_submitted" name="report_submitted" 
                   value="{{ old('report_submitted', $hb837->report_submitted ? \Carbon\Carbon::parse($hb837->report_submitted)->format('Y-m-d') : '') }}">
            @error('report_submitted')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="agreement_submitted">Agreement Submitted</label>
            <input type="date" class="form-control @error('agreement_submitted') is-invalid @enderror" 
                   id="agreement_submitted" name="agreement_submitted" 
                   value="{{ old('agreement_submitted', $hb837->agreement_submitted ? \Carbon\Carbon::parse($hb837->agreement_submitted)->format('Y-m-d') : '') }}">
            @error('agreement_submitted')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<script>
// Auto-calculate net profit
document.addEventListener('DOMContentLoaded', function() {
    const quotedPrice = document.getElementById('quoted_price');
    const expenses = document.getElementById('sub_fees_estimated_expenses');
    const netProfit = document.getElementById('project_net_profit');
    
    function calculateProfit() {
        const price = parseFloat(quotedPrice.value) || 0;
        const exp = parseFloat(expenses.value) || 0;
        netProfit.value = (price - exp).toFixed(2);
    }
    
    quotedPrice.addEventListener('input', calculateProfit);
    expenses.addEventListener('input', calculateProfit);
});
</script>
