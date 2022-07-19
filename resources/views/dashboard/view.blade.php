@extends('templates.generic')
@section("content")
    <div class="content-wrapper-before"></div>
    <div class="container-full ">
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xl-12 col-12">
                    <h3>Welcome {{ Auth::user()->name }}!</h3>
                    <p>Information displayed below are your files being maintained through our system.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-12">
                    <div class="box box-body pull-up">
                        <div class="flexbox align-items-center pt-30">
                            <div>
                                <span class="font-size-30">
                                    1000
                                </span>
                                <!-- countnm add to below span to count num -->
                                <span class="font-size-30"></span>
                                <h6 class="text-uppercase text-dark-50 mb-0">Movies</h6>
                                <br/>
                            </div>
                            <span class="icon-User font-size-80 text-success">
                                <i class="fa fa-folder"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-12">
                    <div class="box box-body pull-up">
                        <div class="flexbox align-items-center pt-30">
                            <div>
                                <span class="font-size-30">
                                    2
                                </span>
                                <!-- countnm add to below span to count num -->
                                <span class="font-size-30"></span>
                                <h6 class="text-uppercase text-dark-50 mb-0">Games</h6>
                                <br/>
                            </div>
                            <span class="icon-User font-size-80 text-info">
                                <i class="fa fa-folder"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
