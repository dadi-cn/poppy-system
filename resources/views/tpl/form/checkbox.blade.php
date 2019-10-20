<div class="{{$viewClass['form-group']}} {!! !$errors->has($column) ?: 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}" id="{{$id}}">

        @if($canCheckAll)
            <span class="icheck">
                <label class="checkbox-inline">
                    <input type="checkbox" class="{{ $checkAllClass }}"/>&nbsp;{{ __('admin.all') }}
                </label>
            </span>
            <hr style="margin-top: 10px;margin-bottom: 0;">
        @endif

        @include('admin::form.error')

        @foreach($options as $option => $label)

            {!! $inline ? '<span class="icheck">' : '<div class="checkbox icheck">' !!}

            <label @if($inline)class="checkbox-inline"@endif>
                {!! Form::checkbox(
                    $name.'[]',
                    $option,
                    in_array($option, (array)old($column, $value), true) || ($value === null && in_array($option, $checked, true)),
                    $attributes) !!}
                &nbsp; {{$label}} &nbsp; &nbsp;
            </label>

            {!! $inline ? '</span>' :  '</div>' !!}

        @endforeach

        <input type="hidden" name="{{$name}}[]">

        @include('admin::form.help-block')

    </div>
</div>
