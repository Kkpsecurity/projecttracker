  <div class="col-lg-3 col-6">
      <div class="small-box bg-info">
          <div class="inner">
              <h3>{{ $statistics['active'] }}</h3>
              <p>Active Projects</p>
          </div>
          <div class="icon">
              <i class="fas fa-tasks"></i>
          </div>
      </div>
  </div>
  <div class="col-lg-3 col-6">
      <div class="small-box bg-success">
          <div class="inner">
              <h3>{{ $statistics['completed'] }}</h3>
              <p>Completed</p>
          </div>
          <div class="icon">
              <i class="fas fa-check-circle"></i>
          </div>
      </div>
  </div>
  <div class="col-lg-3 col-6">
      <div class="small-box bg-warning">
          <div class="inner">
              <h3>{{ $statistics['quoted'] }}</h3>
              <p>Quoted</p>
          </div>
          <div class="icon">
              <i class="fas fa-calculator"></i>
          </div>
      </div>
  </div>
  <div class="col-lg-3 col-6">
      <div class="small-box bg-danger">
          <div class="inner">
              <h3 class="text-shadow-sm">{{ $statistics['overdue'] ?? 0 }}</h3>
              <p>Overdue</p>
          </div>
          <div class="icon">
              <i class="fas fa-exclamation-triangle"></i>
          </div>
      </div>
  </div>
