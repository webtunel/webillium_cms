<?php
$ext = pathinfo($value, PATHINFO_EXTENSION);
$images_type = array('jpg', 'png', 'gif', 'jpeg', 'bmp', 'tiff');
if(Storage::exists($value) || file_exists($value)):
if(in_array(strtolower($ext), $images_type)):?>
<div class="file-preview mb-3">
    <a data-lightbox='roadtrip' href='{{asset($value)}}'>
        <img class="img-fluid rounded shadow-sm" style='max-width:200px; max-height:200px; object-fit:cover;' title="Image For {{$form['label']}}" src='{{asset($value)}}'/>
    </a>
</div>
<?php else:?>
<div class="file-preview mb-3">
    <div class="card p-3 d-inline-block">
        <div class="d-flex align-items-center">
            <i class="fa fa-file-o fa-2x mr-2"></i>
            <div>
                <span class="d-block">{{basename($value)}}</span>
                <a href='{{asset($value)}}?download=1' target="_blank" class="btn btn-sm btn-primary mt-1">
                    <i class='fa fa-download'></i> {{cbLang("button_download_file")}}
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif;?>
<?php else:?>
<div class='alert alert-danger'><i class='fa fa-exclamation-triangle'></i> {{cbLang("file_broken")}}</div>
<?php endif;?>
