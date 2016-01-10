$(function() {

    $('#side-menu').metisMenu();

});



//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    "use strict";   
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
        }
    });

    /********************************
                Scrolling
    *********************************/
    if ( ($(window).height() + 100) < $(document).height() ) {
        $('#top-link-block').removeClass('hidden').affix({
            // how far to scroll down before link "slides" into view
            offset: {top:100}
        });
    }
    /********************************
                End global vars
    *********************************/
    (function addXhrProgressEvent($) {
        var originalXhr = $.ajaxSettings.xhr;
        $.ajaxSetup({
            progress: function() { },
            xhr: function() {
                var req = originalXhr(), that = this;
                if (req) {
                    if (typeof req.addEventListener == "function") {
                        req.addEventListener("progress", function(evt) {
                            that.progress(evt);
                        },false);
                    }
                }
                return req;
            }
        });
    })(jQuery);

    var clipboard = new Clipboard('.btncopy');
    clipboard.on('success', function(e) {
        //console.log(e);
    });
    clipboard.on('error', function(e) {
        console.log(e);
    });

    $(document).on('change', '#files', function(event) {        
        $.ajax({
            async: true,
            url: '/dashboard/translations',
            type: "POST",
            dataType:'json',
            data : { files: $(this).val() , _token : $('input[name=_token]').val() },
            cache: !1,
            beforeSend: function () {
                //$('#loading').show();
            },
            success: function (e) {                
                $('#formIndexTranslation').replaceWith(e.data);
            },
            error: function () {
                //$('#loading').hide();   
                //window.location = url; 
                //alert('Có lỗi xẩy ra');
            }
        })
    });


    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }

    $(document).on('click', '#addLine', function(event) {      
        if($('tr[data-id="0"]').is(':visible')) {
            var clones = $('<div/>').append($('.items').last().clone()).html();  
            var new_id = 0;
            $('.items:last .keys').each(function(index, el) {
                var attr = $(this).attr('name').match(/([\w\_]+)(\[(\d{1,})\])/);
                new_id = parseInt(attr[3])+1;         
                clones = clones.replace($(this).attr('name'),attr[1]+"["+(new_id)+"]");                              
            });
            clones = clones.replace($('.items:last').attr('data-id') , new_id);
            $(clones).insertAfter('.items:last');
        }else{
            $('tr[data-id="0"]').removeAttr('style');
        }
    });

    $(document).on('click', '#addFile', function(event) {      
        if($('input[name="addFile"]').is(':visible')){
            $('input[name="addFile"]').hide(400);
        }else{
            $('input[name="addFile"]').show(400);
        }
    });

    $(document).on('click', '.removeLine', function(event) {
        $(this).each(function(index, el) {
            var length = $('.items').length;
            if(length === 1){
                $('.items:last').hide();
            }else{
                var index = parseInt($('.items').length) - 1;
                $('tr[data-id="'+index+'"]').remove();
            }
        });
    });

    $(document).on('change', '#images', function(event) {
        if($(this).length > 0){
            uploads('multiple');    
        }
    });

    $(document).on('click', '.btn-danger', function(event) {
        var ids = $(this).closest('.marginimage').attr('data-id');
            $.ajax({
                async: true,
                url: '/dashboard/image/remove',
                type: "POST",
                dataType:'json',
                data : { id : $(this).prev().attr('data-clipboard-text')},
                cache: !1,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (e) {             
                    $('#loading').hide(); 
                    $('.marginimage[data-id="'+ids+'"]').remove();   
                    
                },
                error: function () {
                    $('#loading').hide();
                }
            })
    });

    $(document).on('click', '.marginimage .btn-success', function(event) {
        $(this).each(function(index, el) {
            var src = $(this).next().attr('src');
            $('.modal-content img').attr('src',src.replace(/thumbs\//,""));
            $('button[data-toggle="modal"]').click();
        });

    });

    $(document).on('submit', '#formIndexImage', function(event) {
        return false;
    });
});



function GetAllFormData(n) {
    var t = {}, i;
    return n.find("input[type=text], input[type=password], input[type=radio]:checked, input[type=hidden], textarea").each(function () {
        t[$(this).attr("name")] = $(this).val().trim()
    }), n.find("input[type=checkbox]").each(function () {
        t[$(this).attr("name")] = ($(this).attr("checked") == "checked" || $(this).attr("checked") == "") ? !0 : !1
    }), n.find("select").each(function () {
        t[$(this).attr("name")] = $(this).val(), t[$(this).attr("name") + "text"] = $(this).find("option:selected").text()
    }), i = {}, n.find("input[type=text], input[type=password], input[type=radio]:checked, input[type=hidden], textarea, select option:selected").each(function () {
        i = $.extend({}, i, $(this).data())
    }), $.extend({}, t, i)
}

function get_list_item(){
    var item    = new Array;    
    var oc      = {};   
    $('input.style-margin').each(function(index, el) {
        item[index] = $(this).attr('data-name');
        if(oc[item[index]] != null){
            oc[item[index]]++;
        }else{
            oc[item[index]] = 1;
        }           
    });
    return arrayKeys(oc);
}

function arrayKeys(input) {
    var output = new Array();
    var counter = 0;
    for (i in input) {
        output[counter++] = i;
    } 
    return output; 
}


function uploads(t) {
    var file = new FormData();
    if(t == 'multiple'){
        jQuery.each($('#images')[0].files, function(i, filez) {
            file.append('file['+i+']', filez);
        });    
    }else{        
        file.append( 'file', $('#image')[0].files[0] );
    }
    $.ajax({
        url: 'image',
        data: file,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST',
        async: true,
        progress: function(evt) {
        },
        beforeSend: function (e) {
            $('#loading').show();                
        },
        success: function(e){
            $('#loading').hide();
            if(e.status == 500){
                handle_error(e.status , e.message);
            }else{                
                //console.log(e.message);
                handle_error(e.status , e.message);
                var j = $('.marginimage').length;                
                for(i = 0 ; i < e.data.length ; i++){
                    temp = template.replace(/http\:\/\/placehold\.it\/230x182/g, e.data[i]);
                    temp = temp.replace(/data\-id\=\"0\"/,'data-id="'+j+'"');
                    $(temp).appendTo('#template-upload');                    
                    j++;
                }
                $('.btncopy').each(function(index, el) {
                    $(this).attr('data-clipboard-text',$(this).attr('data-clipboard-text').replace(/thumbs\//,""));
                });
            }
        } ,
        error:function(){
            $('#loading').hide();
        }  
    }); 
};

setInterval(function(){ 
    var array = ['.alert-success' , '.alert-danger'];
    for(var i = 0 ; i< array.length ; i++){
        $(array[i]).html("");
        $(array[i]).slideUp(500);
    }
}, 6000);

function handle_error(status , message){
    var cl = 'alert-success';
    var re = 'alert-danger';
    if(status == 500){
        cl = 'alert-danger';
        re = 'alert-success';        
    }else{
        message = "Upload success.";
    }
    if($("."+cl).length > 0){
        $('.'+cl).html("<strong>"+message+"</strong>").show();
    }else{
        $('.'+re).removeClass(re).addClass(cl).html("<strong>"+message+"</strong>").show();
    }
    return ;
}
