<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description }}">
    <meta name="author" content="">
    <title>{{ $title }}</title>
    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.6/flatly/bootstrap.min.css">

    <!-- MetisMenu CSS -->
    <link href="{{ asset('template/admin/bower_components/metisMenu/dist/metisMenu.min.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('template/admin/dist/css/sb-admin-2.css') }}" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{{ asset('template/admin/bower_components/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
   
</head>

<body>

<div id="wrapper">
    @yield('content')
    <!-- /#page-wrapper -->
</div>
    <!-- /#wrapper -->

    <!-- jQuery -->   

</body>
    <script src="{{ asset('template/admin/bower_components/jquery/dist/jquery.min.js') }}"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="{{ asset('template/admin/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="{{ asset('template/admin/bower_components/metisMenu/dist/metisMenu.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/bootstrap.filestyle/1.1.0/js/bootstrap-filestyle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.5/clipboard.min.js"></script>
    <!-- Custom Theme JavaScript -->        
    <script src="{{ asset('template/admin/dist/js/sb-admin-2.js') }}"></script>
    {{--  <script type="text/javascript"> 
    var adfly_id = 12194491; 
    var popunder_frequency_delay = 0; 
</script> 
<script src="https://cdn.adf.ly/js/display.js"></script>  

http://ouo.io/api/9IUVvsd4?s=yourdestinationlink.com

http://api.adf.ly/api.php?key=a9b8aea8d5a4747ee61407804519fbe2&uid=12194491&advert_type=int&domain=adf.ly&url=http://bishido.com
--}}
</html>
