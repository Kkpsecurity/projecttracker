@extends('layouts.app')

<?php
$profile = $content['profile'];
?>
@section('content')
    <div class="container">
        <div class=zc;"row">
            <div class="col-md-12">
                <?php
                $tips = [
                    __('Make Your Password Long at lease 8 characters.'),
                    __('Make Your Password a Non-Sense Phrase.'),
                    __('Include Numbers, Symbols, Uppercase and Lowercase Letters.'),
                    __('Avoid Using Obvious Personal Information.'),
                    __('Do Not Reuse Passwords Less then 3 Years old.'),
                    __('Start Using a Password Manager.')
                ];
                ?>
                <section class="container-fluid bg-white p-5">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>{{ __('Edit Profile Data') }}</h2>
                            <div class="alert alert-info">
                                {{ __('Profile section allows you to update your basic personal information. i.e Name, Email etc...') }}
                            </div>

                            <div class="progress">
                                <div id="pstrength" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar"
                                     aria-valuenow="0"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-lg-5">
                            @include('flash::message')
                            <div class="password-console"></div>
                            <form action="{{ route('profile.password.process') }}" method="POST" class="form" role="form">
                                @csrf
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-success pull-right">Update Password</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-lg-7">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-lg-2 text-center" style="font-size: 24px;">
                                        <i class="fa fa-lock fa-5x"></i>
                                    </div>
                                    <div class="col-lg-10">
                                        <h4>{{ __('Password Change Screen.') }}</h4>
                                        <p style="font-size: 14px;">{{ __('Here are some tips for ensuring your passwords are as strong as possible.') }}</p>
                                    </div>
                                </div>
                            </div>
                            <ul class="list-group">
                                <?php $cnt = 1 ?>
                                @foreach($tips as $tip)
                                    <li class="list-group-item bg-dark d-flex justify-content-between m-0 text-light" style=" font-size: 12px; font-weight: bold; text-align: left !important;">
                                        <b class="justify">{{ $cnt }} - {{ $tip }}</b>
                                        <span><i class="fa fa-check" style="color: lightgreen"></i></span>
                                    </li>
                                    <?php $cnt++ ?>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>


@endsection



@section('scripts')
    <script>
        // =========================================================================
        // PASSWORD STRENGTH
        // =========================================================================
        function passwordStrength(password) {
            var desc = [
                {'width':'0px'},
                {'width':'20%'},
                {'width':'40%'},
                {'width':'60%'},
                {'width':'80%'},
                {'width':'100%'}
            ];
            var descClass = [
                '',
                'progress-bar-danger',
                'progress-bar-danger',
                'progress-bar-warning',
                'progress-bar-success',
                'progress-bar-success'
            ];
            var score = 0;
            // if password bigger than 8 give 1 point
            if (password.length > 8) score++;
            // if password has both lower and uppercase characters give 1 point
            if ((password.match(/[a-z]/)) && (password.match(/[A-Z]/))) score++;
            // if password has at least one number give 1 point
            if ( password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) ) score++;
            // if password has at least one special caracther give 1 point
            if ( password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )	score++;
            // if password bigger than 12 give another 1 point
            if (password.length > 10) score++;
            // display indicator
            $("#jak_pstrength").removeClass(descClass[score-1])
                .addClass(descClass[score])
                .css(desc[score]);
        }
        $(document).ready(function() {
            $("input#old-password").focus();
            $("input#password").keyup(function() {
                passwordStrength($(this).val());
            });
        })
    </script>
@endsection















