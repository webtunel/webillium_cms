<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
    <label class='control-label'>{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! cbLang('this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="custom-file-container">
        @if($value)
            <?php
            if(Storage::exists($value) || file_exists($value)):
            $url = asset($value);
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $images_type = array('jpg', 'png', 'gif', 'jpeg', 'bmp', 'tiff');
            if(in_array(strtolower($ext), $images_type)):
            ?>
            <div class="file-preview mb-3">
                <div class="position-relative">
                    <a data-lightbox='roadtrip' href='{{$url}}'>
                        <img class="img-fluid rounded shadow-sm" style='max-width:200px; max-height:200px; object-fit:cover;' title="Image For {{$form['label']}}" src='{{$url}}'/>
                    </a>
                    @if(!$readonly || !$disabled)
                        <a class='btn btn-sm btn-danger position-absolute' style="top: 5px; right: 5px; border-radius: 50%; padding: 0.25rem 0.5rem;"
                           onclick="if(!confirm('{{cbLang("delete_title_confirm")}}')) return false"
                           href='{{url(CRUDBooster::mainpath("delete-image?image=".$value."&id=".$row->id."&column=".$name))}}'>
                            <i class='fa fa-times'></i>
                        </a>
                    @endif
                </div>
            </div>
            <?php else:?>
            <div class="file-preview mb-3">
                <div class="card p-3 d-inline-block">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-file-o fa-2x mr-2"></i>
                        <div>
                            <span class="d-block">{{basename($url)}}</span>
                            <a href='{{$url}}' class="btn btn-sm btn-primary mt-1">
                                <i class='fa fa-download'></i> {{cbLang("button_download_file")}}
                            </a>
                            @if(!$readonly || !$disabled)
                                <a class='btn btn-sm btn-danger mt-1'
                                   onclick="if(!confirm('{{cbLang("delete_title_confirm")}}')) return false"
                                   href='{{url(CRUDBooster::mainpath("delete-image?image=".$value."&id=".$row->id."&column=".$name))}}'>
                                    <i class='fa fa-trash'></i> {{cbLang('text_delete')}}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <?php endif;
            echo "<input type='hidden' name='_$name' value='$value'/>";
            else:
                echo "<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> ".cbLang("file_broken")."</div>";
            endif;
            ?>
            <p class='text-muted'><em>{{cbLang("notice_delete_file_upload")}}</em></p>
        @endif

        @if(!$value || (!$readonly && !$disabled))
            <div class="input-group">
                <div class="custom-file">
                    <input type='file' id="{{$name}}" title="{{$form['label']}}" {{$required}} {{$readonly}} {{$disabled}} class='custom-file-input' name="{{$name}}">
                    <label class="custom-file-label" for="{{$name}}">{{ @$form['help'] ?: cbLang('file_browse') }}</label>
                </div>
                @if(!$value)
                <div class="input-group-append">
                    <span class="input-group-text" id="{{$name}}_upload"><i class="fas fa-upload"></i></span>
                </div>
                @endif
            </div>
            <small class="text-muted">{{ @$form['help_block'] ?: '' }}</small>
        @endif

        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
    </div>
</div>

<script>
// Initialize the custom file input for better display
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('#{{$name}}').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : '{{ @$form['help'] ?: cbLang('file_browse') }}';
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
});
</script>
