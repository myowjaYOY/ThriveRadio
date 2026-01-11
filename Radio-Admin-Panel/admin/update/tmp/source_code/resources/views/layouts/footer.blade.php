<!----- THIS FORM USE FOR DELETE  ---->
<form method="DELETE" id="form-del">
    <input name="_method" type="hidden" value="DELETE">
    {{ csrf_field() }}

</form>
<!----- THIS FORM USE FOR DELETE  ---->

<footer class="footer fixed-bottom">
    <div class="d-sm-flex text-right">
        <span class="text-muted text-right text-sm-left d-block d-sm-inline-block">{{__('Copyright')}} © <?= date('Y'); ?> <a class="text-theme" href="{{route('home')}}" target="_blank">{{env('APP_NAME')}}</a>. {{__('All Rights Reserved by WRTeam')}}.</span>
     </div>
</footer>
