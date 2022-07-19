<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('includes.favicon')
    <title> {{ ucwords($current_module).' '.ucwords($current_view) }} - {{ config("app.name") }}</title>
    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ URL::asset('public/') }}/css/vendors_css.css">
    <!-- Style-->
    <link rel="stylesheet" href="{{ URL::asset('public/') }}/css/horizontal-menu.css">
    <link rel="stylesheet" href="{{ URL::asset('public/') }}/css/style.css">
    <link rel="stylesheet" href="{{ URL::asset('public/') }}/css/skin_color.css">

    {{--@laravelPWA--}}
</head>

<body class="layout-top-nav light-skin theme-primary">




<div class="wrapper">
    @include("includes.header")
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="content-wrapper-before"></div>
        @yield("content")
    </div>
    @include("includes.footer")
</div>
<!-- ./wrapper -->



<!-- Vendor JS -->
<script src="{{ URL::asset('public/') }}/js/vendors.min.js"></script>
<script src="{{ URL::asset('public/') }}/assets/icons/feather-icons/feather.min.js"></script>

@yield("pagescripts")

<!-- Adminto App -->
<script src="{{ URL::asset('public/') }}/js/jquery.smartmenus.min.js"></script>
<script src="{{ URL::asset('public/') }}/js/menus.min.js"></script>
<script src="{{ URL::asset('public/') }}/js/template.min.js"></script>
<script src="{{ URL::asset('public/') }}/js/pages/dashboard3.js"></script>
<script src="{{ URL::asset('public/') }}/assets/vendor_components/sweetalert/sweetalert.min.js"></script>
<script src="{{ URL::asset('public/') }}/assets/vendor_components/jquery-toast-plugin-master/src/jquery.toast.js"></script>
<script src="{{ URL::asset('public/') }}/js/jquery.form.js"></script>
<script src="{{ URL::asset('public/') }}/js/jaxform.js"></script>

</body>
</html>
