@extends('templates.generic')

@section("content")

    @include('includes.loader')

    <!-- MODAL APPROVE -->
    <div class="modal fade" id="modal-edit" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">

            <form id="form1" class="tdiojax-form"
                  action="{{URL::to("post/categoryEdit")}}"
                  method="post"
                  data-error-container="error_container"
                  data-after-success-redirect="{{ URL::to("/categories/list") }}"
                  data-response-type="toast"
                  data-loader="loader"
                  data-submit-button="btn_submit"
                  accept-charset="UTF-8">
                {!! csrf_field() !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Category</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="error_container" class="row" style="display: none">
                            <div class="col-lg-12 alert alert-danger" style="background: red">

                            </div>
                        </div>
                        <input type="hidden" name="id" value="" />
                        <div class="form-group row credit_section">
                            <label class="col-md-3">Category</label>
                            <p class="col-md-9">
                                <input type="text" name="cat_name" value="" placeholder="e.g. Movies" class="form-control" />
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div style="float: right;" id="loader" class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
                        <button id="btn_submit" type="submit" class="btn btn-success float-right">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->


            </form>
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="container-full dbms_list_container">

        <!-- Main content -->
        <section class="content">

            @include('includes.breadcrumb')

            <div class="box">
                <div class="box-body">
                    <div class="dbms_create_holder">
                        <a data-toggle="modal" data-target="#modal-edit" data-backdrop="static" data-keyboard="false" href="javascript:void(0)" class="waves-effect waves-light btn mb-5 bg-gradient-primary">Create New Record</a>
                    </div>

                    <div class="table-responsive">
                        <table id="dbms-list-dt" class="table table-bordered table-hover display nowrap margin-top-10 w-p100">
                            <thead>
                            <tr>
                                <th>Category ID</th>
                                <th>Name</th>
                                <th>Created</th>
                                <th>Associated Items</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="5">Loading...</td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Category ID</th>
                                <th>Name</th>
                                <th>Created</th>
                                <th>Associated Items</th>
                                <th>Actions</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>


        </section>
        <!-- /.content -->
    </div>

@endsection




@section("pagescripts")
    <script src="{{ URL::asset('public/') }}/assets/vendor_components/datatable/datatables.min.js"></script>
    <script type="text/javascript">
        var dt = null;

        $(document).ready(function()
        {
            $('#global_loader').css('display','flex');
            getDataTableData();
            $('body').on('click','.btn-edit',function()
            {
                $('input[name="id"]').val($(this).data("id"));
                $('input[name="cat_name"]').val($(this).data("name"));
            });
        });

        function getDataTableData()
        {
            if(dt!=null)
            {
                dt.destroy();
            }

            dt = $('#dbms-list-dt').DataTable
            ({
                responsive:true,
                dom: 'Bfrtip',
                buttons: [],
                ajax: '{{ URL::to('api/categoryList')  }}',
                initComplete: function()
                {
                    $('#global_loader').hide();
                    $('#modal-edit').modal('hide');
                }
            });
        }
    </script>
@endsection
