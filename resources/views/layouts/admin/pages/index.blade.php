<!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">                        
                        <h1 class="page-header">{{ $title }}</h1>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        @include('layouts.admin.partials.flash')
                        @include($content)
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>