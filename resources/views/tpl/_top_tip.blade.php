@if (
    sys_setting('site.top_tip_is_open') &&
    sys_setting('site.top_tip_start_datetime') &&
    sys_setting('site.top_tip_end_datetime') &&
    sys_setting('site.top_tip_start_datetime') <= Carbon\Carbon::now() &&
    Carbon\Carbon::now() <= sys_setting('site.top_tip_end_datetime')
    )
    <div class="poppy--tip">
        {!! sys_setting('site.top_tip_content') !!}
    </div>
@endif