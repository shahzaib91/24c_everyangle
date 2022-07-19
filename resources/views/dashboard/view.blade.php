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

                @if($data->stats && count($data->stats)>0)

                    @foreach($data->stats as $stat)

                        <div class="col-xl-3 col-12">
                            <div class="box box-body pull-up">
                                <div class="flexbox align-items-center pt-30">
                                    <div>
                                        <span class="font-size-30">
                                            {{ number_format($stat->total) }}
                                        </span>
                                        <!-- countnm add to below span to count num -->
                                        <span class="font-size-30"></span>
                                        <h6 class="text-uppercase text-dark-50 mb-0">{{ ucwords($stat->cat_name) }}</h6>
                                        <br/>
                                    </div>
                                    <span class="icon-User font-size-80">
                                        <i class="fa fa-folder"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                @endif

            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
