$(document).ready(function()
{
    // swal("Good Job","Data has been saved","success")
    var lView = $('#loader');
    var fCont = null;
    var form = null;

    // pass options to ajaxForm
    $('.tdiojax-form').ajaxForm
    ({
        dataType:     'json',
        beforeSubmit: function(arr, $form, options)
        {
            form = $form;

            if(typeof(form.data("validate"))!='undefined' && form.data("validate")!=""){
                var res = eval(form.data("validate"));
                if(!res){
                    return false;
                }
            }

            if(typeof(form.data("error-container"))!='undefined' && form.data("error-container")!="")
            {
                fCont = $('#'+form.data("error-container"));
                fCont.hide();
            }

            $('#'+form.data("loader")).show();
            $('#'+form.data("submit-button")).hide();

            lView.show();
        },
        error: function()
        {
            $('#'+form.data("loader")).hide();
            $('#'+form.data("submit-button")).show();
            lView.hide();
            swal("Uh Oh!","Something went wrong while trying to perform requested action. Please try again or contact developer","error")
        },
        success: function(data)
        {
            $('#'+form.data("loader")).hide();
            $('#'+form.data("submit-button")).show();
            lView.hide();
            var rtype = form.data("response-type");

            if(data.status)
            {
                if(typeof(form.data("redirect"))!='undefined' && form.data("redirect")!=""){
                    window.location = form.data("redirect");
                } else if(typeof(data.redirect)!='undefined' && data.redirect!=""){
                    window.location = data.redirect;
                } else {
                    if($('input[name="id"]').val()==""){
                        form.get(0).reset();
                    }

                    if(typeof(form.data("post"))!='undefined' && form.data("post")!=""){
                        eval(form.data("post"));
                    }

                    if(typeof(form.data("after-success-redirect"))!='undefined'){

                        swal
                        ({
                            title: "Success",
                            text: data.message,
                            type: "success"
                        },
                        function() {
                            if(form.data("after-success-redirect")!="")
                            {
                                window.location = form.data("after-success-redirect");
                            }
                        });

                    } else {

                        if(rtype=="toast")
                        {
                            $.toast
                            ({
                                heading: 'Success!',
                                text: data.message,
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'success',
                                hideAfter: 5000,
                                stack: 6
                            });
                        }
                        else
                        {
                            swal("Success",data.message,"success");
                        }
                    }
                }
            }
            else
            {
                if(typeof(data.errors)!='undefined')
                {
                    if(fCont!=null)
                    {
                        fCont.find('div').html("");
                        fCont.show();
                        fCont.find('div').append('<strong>System is unable to process your request, please resolve error(s) below to proceed:</strong><br/><br/>');
                        for(i=0; i<data.errors.length; i++)
                        {
                            fCont.find('div').append('* '+data.errors[i]+'<br/>');
                        }
                    }
                }

                if(rtype=="toast")
                {
                    $.toast
                    ({
                        heading: 'Ops!',
                        text: data.message,
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                        hideAfter: 5000
                    });
                }
                else
                {
                    swal("Error",data.message,"warning");
                }

                $('html, body').animate({
                    scrollTop: fCont.offset().top
                }, 1000);
            }

        }
    });


    $('body').on('click','.btn_remove',function()
    {
        var _href = $(this).data("href");
        var _this = $(this);
        var _post = $(this).data("post");

        swal
        ({
            title: "DELETE CONFIRMATION",
            text: "Are you sure you want to delete this record? This action can't be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes',
            cancelButtonText: "No",
        },
        function(isConfirm)
        {
            if (isConfirm)
            {
                $.ajax
                ({
                    url:_href,
                    beforeSend: function( xhr ) {
                        $('#global_loader').css('display','flex');
                    }
                })
                .done(function(data)
                {
                    if(data.status)
                    {
                        $.toast
                        ({
                            heading: 'Success!',
                            text: "Record has been deleted successfully!",
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'success',
                            hideAfter: 5000,
                            stack: 6
                        });

                        var x = eval(_post)
                        if (typeof x == 'function') {
                            x()
                        }
                    }
                    else
                    {
                        $.toast
                        ({
                            heading: 'Ops!',
                            text: data.message,
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'error',
                            hideAfter: 5000
                        });
                    }
                })
                .fail(function()
                {
                    $.toast
                    ({
                        heading: 'Ops!',
                        text: "Something went wrong...",
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                        hideAfter: 5000
                    });
                })
                .always(function()
                {
                    $('#global_loader').hide();
                });
            }
        })


    });

});