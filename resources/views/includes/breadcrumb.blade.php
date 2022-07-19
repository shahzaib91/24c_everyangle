<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="w-p100 d-md-flex align-items-center justify-content-between">
            <h3 class="page-title">{{ strtoupper($current_module) }}</h3>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{URL::to("/dashboard")}}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">{{ ucwords($current_module) }}</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ucwords(str_replace('-',' ',$current_view)) }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>