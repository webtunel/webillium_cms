<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{isset($form['style']) ? $form['style'] : ''}}">
    <label class='control-label'>{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! cbLang('this_field_is_required') !!}'>*</span>
        @endif
    </label>
    
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fa fa-envelope"></i>
                </div>
            </div>
            <input class="form-control phone-number" type='email' title="{{$form['label']}}"
                   {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} {{isset($validation) && isset($validation['max'])?"maxlength=".$validation['max']:""}} class='form-control'
                   name="{{$name}}" id="{{$name}}" value='{{$value}}'>
        </div>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ isset($form['help']) ? $form['help'] : '' }}</p>
</div>