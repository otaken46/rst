@section('menu')
<link rel="stylesheet" href="{{ asset('css/menu.css') }}">
<script type="text/javascript">
jQuery (function ()
{   
    $("#menu dt").on("click", function() {
        $(this).next().slideToggle();
    });
    $("#top").on("click", function() {
        window.location.href = "<?php echo url('') . "/top"; ?>";
    });
    $("#inquiry").on("click", function() {
        window.location.href = "<?php echo url('') . "/getinquiry"; ?>";
    });
    $("#logout").on("click", function() {
        window.location.href = "<?php echo url('') . "/logout"; ?>";
    });
    $("#notice").on("click", function() {
        window.location.href = "<?php echo url('') . "/notice"; ?>";
    });
    $("#reload").on("click", function() {
        window.location.href = "<?php echo url()->full(); ?>";
    });
})
</script>
<div style="position: absolute; right: 5px; top: -15px">
    <dl id="menu">
        <dt><img src="{{ asset('img/menu.png') }}"></dt>
        <dd>
          @if ($menu == Config::get('const.menu_all'))
            <input type="button" class="wd95" id="top" value="TOP"><br>
            <input type="button" class="wd95" id="logout" value="ログアウト"><br>
          @endif
          @if ($menu == Config::get('const.menu_only_inquiry') || $menu == Config::get('const.menu_all') )
            <input type="button" class="wd95" id="inquiry" value="お問合せ"><br>
          @endif
          @if ($menu == Config::get('const.menu_all'))
            <input type="button" class="wd95" id="notice" value="お知らせ"><br>
          @endif
          @if ($menu == Config::get('const.menu_mng'))
            <input type="button" class="wd95" id="reload" value="更新"><br>
            <input type="button" class="wd95" id="logout" value="ログアウト"><br>
          @endif
        </dd>
    </dl>
</div>
@endsection