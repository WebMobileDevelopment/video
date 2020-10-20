@extends('install.layout')

@section('content')

<section>

<div class="october-container">

    <div class="container">

        <div id="cover">
            <div id="slider_1">
                <p class="content animated slideInRight">
                @if($ngnix_config = check_nginx_configure())
                    <strong><i class="fa fa-check tick"></i></strong>
                @else
                    <strong><i class="fa fa-times incorrect"></i></strong>
                @endif
                    {{tr('ngnix_installed_configure')}}
                </p>
            </div>

            <div id="slider_2">
                <p class="content animated slideInRight">
                    @if($php_config = check_php_configure())
                        <strong><i class="fa fa-check tick"></i></strong>
                    @else
                        <strong><i class="fa fa-times incorrect"></i></strong>
                    @endif

                    {{tr('php_installation_required')}}
                </p>
            </div>

            <div id="slider_3">
                <p class="content animated slideInRight">
                    @if($mysql_config = check_mysql_configure())
                        <strong><i class="fa fa-check tick"></i></strong>
                    @else
                        <strong><i class="fa fa-times incorrect"></i></strong>
                    @endif

                    {{tr('mysql_installation_required')}}
                </p>
            </div> 

            <div id="slider_4">
                <p class="content animated slideInRight">
                    @if($database_config = check_database_configure())
                        <strong><i class="fa fa-check tick"></i></strong>
                    @else
                        <strong><i class="fa fa-times incorrect"></i></strong>
                    @endif

                    {{tr('database_connection_required')}}
                </p>
            </div>

            <div id="slider_5">
                <p class="content animated slideInRight">
                    @if($settings_config = check_settings_seeder())
                        <strong><i class="fa fa-check tick"></i></strong>
                    @else
                        <strong><i class="fa fa-times incorrect"></i></strong>
                    @endif

                    {{tr('sql_file_installation')}}
                </p>
            </div>
        </div>

        <!--end of cover-->

        <?php $overall_configure = 0; ?>

        @if($php_config && $mysql_config && $database_config && $settings_config)

            <?php $overall_configure = 1; ?>

            <div class="check-fail-outer" id="system_check_result" style="display:none">

                <div class="check-success-inner">
                    <h4 class="fail-head">{{tr('system_check')}}</h4>
                
                </div>

                <!--end of check-fail-inner-->

            </div>

        @else

            <div class="check-fail-outer" id="system_check_result" style="display:none">

                <div class="check-fail-inner">
                    <h4 class="fail-head">{{tr('system_check_failed')}}</h4>
                    <p>{{tr('basic_installation_requirment')}}</p>
                    <a style="text-decoration: none;cursor: pointer;" href="{{route('installTheme')}}" class="fail-button">{{tr('retry_system_check')}}</a>
                
                </div>

                <!--end of check-fail-inner-->

            </div>

        @endif

      <!--end of check-fail-outer-->
    
    </div>

    <!--end of container-->

</div><!--end of october-container-->

</section>


@endsection

@section('footer')
    <footer>
        <div class="container">
          <div class="row no-margin install-tree">

            <div class="col-md-6 col-md-offset-6 agree">

                @if($overall_configure == 0)

                    <button disabled>{{tr('agree')}} &amp; {{tr('continue')}}</button>
                @else
                    <a href="{{route('system-check')}}" class="btn btn-primary btn-lg" style="float:right" href="#">{{tr('agree')}} &amp; {{tr('continue')}}</a>
                @endif

            </div><!--end of agree-->

          </div><!--end of install-tree-->
        </div><!--end of container-->
    
    </footer>
@endsection