    <div class="y-menu video-y-menu hidden col-xs-6">
    <ul class="y-home menu1">

        <li><a href="{{route('user.dashboard')}}">
            <img src="{{asset('images/home.png')}}">{{tr('home')}}</a>
        </li>

        <li><a href="{{route('user.trending')}}">
            <img src="{{asset('images/trending.png')}}">{{tr('trending')}}</a>
        </li>
        
    </ul>

    <?php  $categories = []; ?>

    @if(count($categories) > 0)
        
        <ul class="y-home">
            <h3>Best of Streamtube</h3>
            @foreach($categories as $category)
                <li>
                    <a href="{{route('user.category',$category->id)}}"><img src="{{$category->picture}}">{{$category->name}}</a>
                </li>
            @endforeach              
        </ul>

    @endif

    @if(Auth::check())

    @else
        <div class="menu4">
            <p>{{tr('signin_nav_content')}}!</p>

            <form method="get" action="{{route('user.login.form')}}">
                <button type="submit">{{tr('login')}}</button>
            </form>
        </div>   
    @endif                
</div><!--y-menu end-->