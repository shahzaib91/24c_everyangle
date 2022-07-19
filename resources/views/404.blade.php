<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="">

    <title> Page not found - {{ config("app.name") }}</title>
    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ URL::asset('public/') }}/css/vendors_css.css">
    <!-- Style-->
    <link rel="stylesheet" href="{{ URL::asset('public/') }}/css/style.css">
    <link rel="stylesheet" href="{{ URL::asset('public/') }}/css/skin_color.css">
</head>
<body class="hold-transition theme-primary bg-img" style="background-image: url(../images/auth-bg/bg-4.jpg)">

<section class="error-page h-p100">
    <div class="container h-p100">
        <div class="row h-p100 align-items-center justify-content-center text-center">
            <div class="col-lg-7 col-md-10 col-12">
                <div class="rounded30 p-50">
                    <img src="{{ URL::asset('public/') }}/images/auth-bg/404.jpg" class="max-w-200" alt="" />
                    <h1>Page Not Found !</h1>
                    <h3>looks like, page doesn't exist</h3>
                    <div class="my-30"><a href="{{URL::to("/dashboard/view")}}" class="btn btn-danger">Back to dashboard</a></div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Vendor JS -->
<script src="{{ URL::asset('public/') }}/js/vendors.min.js"></script>
<script src="{{ URL::asset('public/') }}/assets/icons/feather-icons/feather.min.js"></script>

</body>
</html>
