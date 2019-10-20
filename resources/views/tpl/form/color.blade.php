<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}}">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('system::tpl.form.error')

        <div class="layui-input-group">

            @if ($prepend)
                <span class="layui-input-group-addon">{!! $prepend !!}</span>
            @endif
            {!! Form::text($name, $value, $attributes) !!}
            @if ($append)
                <span class="input-group-addon clearfix">{!! $append !!}</span>
            @endif

        </div>

        @include('admin::form.help-block')

    </div>
</div>
<style>

</style>