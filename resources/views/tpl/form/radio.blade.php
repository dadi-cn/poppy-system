<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        @foreach($options as $option => $label)

            {!! $inline ? '<span class="icheck">' : '<div class="radio icheck">'  !!}

            <label @if($inline)class="radio-inline"@endif>
                {!! Form::radio(
                    $name,
                    $value,
                    ($option == old($column, $value)) || ($value === null && in_array($label, $checked, false)),
                    $attributes) !!}
                {{$label}}&nbsp;&nbsp;
            </label>

            {!! $inline ? '</span>' :  '</div>' !!}

        @endforeach

        @include('admin::form.help-block')

    </div>
</div>
