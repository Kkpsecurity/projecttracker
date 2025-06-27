@extends('layouts.app')

@section('content')
<div class="container bg-light p-4 rounded shadow">
     <div class="row">
         <div class="col-lg-12 margin-tb">
             <div class="pull-left">
                 <h2>Add New User</h2>
             </div>
             <div class="pull-right">
                 <a class="btn btn-primary" href="{{ url('admin/users') }}"> Back</a>
             </div>
         </div>
     </div>

     @if ($errors->any())
     <div class="alert alert-danger">
         There were some problems with your input.<br><br>
         <ul>
             @foreach ($errors->all() as $error)
             <li>{{ $error }}</li>
             @endforeach
         </ul>
     </div>
     @endif

     <form action="{{ url('admin/users/store') }}" method="POST">
         @csrf

         <div class="row">
             <div class="col-xs-12 col-sm-12 col-md-12">
                 <div class="form-group">
                     <strong>Name:</strong>
                     <input type="text" name="name" class="form-control" placeholder="Name">
                 </div>
             </div>
             <div class="col-xs-12 col-sm-12 col-md-12">
               <div class="form-group">
                   <strong>Email:</strong>
                   <input type="email" class="form-control" name="email" placeholder="Email" />
               </div>
           </div>
             <div class="col-xs-12 col-sm-12 col-md-12">
                 <div class="form-group">
                     <strong>Password:</strong>
                     <input type="password" class="form-control" name="password" id="password" placeholder="Password" />
                 </div>
             </div>
             <div class="col-xs-12 col-sm-12 col-md-12">
                 <div class="form-group">
                     <strong>Confirm Password:</strong>
                     <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" />
                 </div>
             </div>
             <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                 <button type="submit" class="btn btn-primary">Submit</button>
             </div>
         </div>

     </form>
 </div>
@endsection
