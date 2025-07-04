<!-- Financial Metrics Tab Content -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Financial Analytics - Coming Soon
                </h3>
            </div>
            <div class="card-body text-center">
                <div class="py-5">
                    <i class="fas fa-dollar-sign fa-5x text-muted mb-4"></i>
                    <h3 class="text-muted">Financial Analytics Dashboard</h3>
                    <p class="lead text-muted">
                        Comprehensive financial reporting and analytics will be implemented here.
                    </p>
                    <p class="text-muted">
                        This section will include:
                    </p>
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-2x text-primary mb-2"></i>
                                    <h6>Revenue Analytics</h6>
                                    <small class="text-muted">Monthly/quarterly revenue trends and forecasting</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                                    <h6>Client Value Analysis</h6>
                                    <small class="text-muted">Top clients by revenue and lifetime value</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                                    <h6>Cost Analysis</h6>
                                    <small class="text-muted">Operational costs and profitability metrics</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-calculator fa-2x text-info mb-2"></i>
                                    <h6>Billing Efficiency</h6>
                                    <small class="text-muted">Invoice processing and payment tracking</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sample KPI Cards (Static for now) -->
                    <div class="row mt-5">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Monthly Revenue</span>
                                    <span class="info-box-number">Coming Soon</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Profit Margin</span>
                                    <span class="info-box-number">Coming Soon</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Avg Invoice Time</span>
                                    <span class="info-box-number">Coming Soon</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-chart-pie"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">ROI</span>
                                    <span class="info-box-number">Coming Soon</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-lightbulb mr-2"></i>
                        <strong>Implementation Note:</strong> Financial analytics require integration with billing and accounting systems. 
                        This will be developed in Phase 3 of the analytics implementation.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Future Financial Charts Placeholder -->
<div class="row" style="display: none;" id="financial-charts">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Revenue Trends</h3>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" class="small-chart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Client Value Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="clientValueChart" class="small-chart"></canvas>
            </div>
        </div>
    </div>
</div>
