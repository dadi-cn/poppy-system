<?php $item['type'] = $item['type'] ?? 'input'; ?>
<?php $item['options'] = $item['options'] ?? []; ?>
@if ($item['type'] === 'input')
    {!! Form::text($item['name'], $item['value'] ?? '', $item['options']) !!}
@endif
@if ($item['type'] === 'code')
    {!! Form::code($item['name'], $item['value'] ?? '', $item['options']) !!}
@endif
@if ($item['type'] === 'picture')
    <div class="system--form_thumb">
        {!! Form::thumb($item['name'], $item['value'] ?? '', $item['options'] + ['pam'=> Auth::guard(\Poppy\System\Models\PamAccount::GUARD_BACKEND)->user()]) !!}
    </div>
@endif
@if ($item['type'] === 'switch')
    {!! Form::radio($item['name'],
        \Poppy\System\Models\SysConfig::NO, (int)($item['value']??'') === 0,
        $item['options'] + ['title'=> '否']) !!}
    {!! Form::radio($item['name'],
        \Poppy\System\Models\SysConfig::YES, (int)($item['value'] ?? '') === 1,
        $item['options'] + ['title'=> '是']) !!}
@endif
@if ($item['type'] === 'radio')
    @foreach($item['opinions'] as $_value => $_label)
        {!! Form::radio($item['name'],$_value, ($item['value']?? '') == $_value,
            $item['options'] + ['title'=> $_label ]) !!}
    @endforeach
@endif
@if ($item['type'] === 'checkbox')
    @foreach($item['opinions'] as $_value => $_label)
        {!! Form::checkbox($item['name'].'[]',$_value, in_array($_value, (array) $item['value'], false),
            $item['options'] + [
                'lay-skin' => 'primary',
                'title'=> $_label
            ]) !!}
    @endforeach
@endif
@if ($item['type'] === 'textarea')
    {!! Form::textarea($item['name'], $item['value'] ?? '', $item['options']) !!}
@endif
@if ($item['type'] === 'tip')
    {!! Form::tip($item['name'], $item['value'] ?? '') !!}
@endif
@if ($item['type'] === 'datetime_picker')
    {!! Form::datetimePicker($item['name'], $item['value'] ?? '', $item['options']) !!}
@endif
@if ($item['type'] === 'date_picker')
    {!! Form::datePicker($item['name'], $item['value'] ?? '', $item['options']) !!}
@endif
@if ($item['type'] === 'date_range_picker')
    {!! Form::dateRangePicker($item['name'], $item['value'] ?? '', $item['options']) !!}
@endif
@if ($item['type'] === 'month_picker')
    {!! Form::monthPicker($item['name'], $item['value'] ?? '', $item['options']) !!}
@endif
@if ($item['type'] === 'show_thumb')
    {!! Form::showThumb($item['name'], $item['value'] ?? '', $item['options']) !!}
@endif
@if ($item['type'] === 'multi_thumb')
    {!! Form::multiThumb($item['name'], $item['value'] ?? '', $item['options']) !!}
@endif
@if ($item['type'] === 'editor')
    {!! Form::editor($item['name'], $item['value'] ?? '', $item['options'] + ['pam'=> $pam]) !!}
@endif
@if ($item['type'] === 'hook')
    {!! sys_hook($item['hook'], $item) !!}
@endif
@if (isset($item['description']) && trim($item['description']))
    <p style="margin-top: 5px;color: #b1b0b0;">
        <small>{!! $item['description'] !!}</small>
    </p>
@endif