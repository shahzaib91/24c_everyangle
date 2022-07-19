<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Sign In - {{ config("app.name") }}</title>
    @include('includes.favicon')
    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ URL::asset('public/') }}/css/vendors_css.css">
    <!-- Style-->
    <link rel="stylesheet" href="{{ URL::asset('public/') }}/css/style.css">
    <link rel="stylesheet" href="{{ URL::asset('public/') }}/css/skin_color.css">

    @laravelPWA
</head>

<body class="hold-transition theme-primary bg-img" style="background-image: url({{ URL::asset('public/') }}/images/auth-bg/bg-1.jpg)">

<div class="container h-p100">
    <div class="row align-items-center justify-content-md-center h-p100">

        <div class="col-12">
            <div class="row justify-content-center no-gutters">
                <div class="col-lg-5 col-md-5 col-12">
                    <div class="bg-white rounded30 shadow-lg">
                        <div class="content-top-agile p-20 pb-0">
                            <h2 class="text-primary">{{config('app.name')}}</h2>
                            <p class="mb-0">Sign in to manage your media!</p>
                        </div>
                        <div class="p-40">
                            <!--
                            data-error-container=""
                            data-success-redirect=""
                            data-response-type="alert|toast"
                            data-loader=""
                            data-submit-button=""
                            -->
                            <form
                                    class="tdiojax-form"
                                    action="{{URL::to("/post/login")}}"
                                    method="post"
                                    data-error-container=""
                                    data-success-redirect=""
                                    data-response-type="toast"
                                    data-loader="login_loader"
                                    data-submit-button="btn_login"
                                    accept-charset="UTF-8">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent"><i class="ti-user"></i></span>
                                        </div>
                                        <input name="email" type="text" class="form-control pl-15 bg-transparent" placeholder="Work E-mail">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text  bg-transparent"><i class="ti-lock"></i></span>
                                        </div>
                                        <input name="password" type="password" class="form-control pl-15 bg-transparent" placeholder="Password">
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- /.col -->
                                    <div class="col-12 text-center">
                                        <div id="login_loader" class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                                        <button id="btn_login" type="submit" class="btn btn-danger mt-10">SIGN IN</button>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="mt-20 text-white">- Powered By -</p>
                        <p class="gap-items-2 mb-20">
                            <a style="color: #FFF; font-size: 16px; font-weight: bold" href="https://www.everyangle.ie/">Everyangle Limited</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Vendor JS -->
<script src="{{ URL::asset('public/') }}/js/vendors.min.js"></script>
<script src="{{ URL::asset('public/') }}//assets/vendor_components/sweetalert/sweetalert.min.js"></script>
<script src="{{ URL::asset('public/') }}/assets/icons/feather-icons/feather.min.js"></script>
<script src="{{ URL::asset('public/') }}/assets/vendor_components/jquery-toast-plugin-master/src/jquery.toast.js"></script>
<script src="{{ URL::asset('public/') }}/js/jquery.form.js"></script>
<script src="{{ URL::asset('public/') }}/js/jaxform.js"></script>
</body>
</html>
