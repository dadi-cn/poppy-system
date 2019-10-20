<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('system::tpl.form.error')

        <div class="input-group" style="width: 250px;">

            {!! Form::text($name, $value, $attributes) !!}

            <span class="input-group-addon clearfix" style="padding: 1px;">
                <img id="{{$column}}-captcha" src="{{ captcha_src() }}"
                     style="height:30px;cursor: pointer;"
                     title="Click to refresh"/></span>

        </div>

        @include('system::tpl.form.help-block')

    </div>
</div>