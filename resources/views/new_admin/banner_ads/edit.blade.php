@extends('layouts.admin')

@section('title', tr('edit_banner_ad'))

@section('content-header') 

{{tr('edit_banner_ad')}}
<br>
<!--small class="header-note">** {{tr('banner_ads_note')}} <a target="_blank" href="http://prntscr.com/hx6e61">http://prntscr.com/hx6e61</a>**</small-->
@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.banner_ads.index')}}"><i class="fa fa-bullhorn"></i>{{tr('banner_ads')}}</a></li>
    <li class="active"><i class="fa fa-bullhorn"></i> {{tr('edit_banner_ad')}}</li>
@endsection

@section('content')

@include('new_admin.banner_ads._form')
    
@endsection




