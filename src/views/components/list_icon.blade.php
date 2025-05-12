<select id='list-icon' class="form-control form-control-sm" name="icon" style="font-family: 'FontAwesome', Helvetica;">
    <option value="">** Select an Icon</option>
    @foreach($fontawesome as $font)
        <option value='fa fa-{{$font}}' {{ (isset($row) && $row && isset($row->icon) && ($row->icon == "fa fa-$font" || $row->icon == "fas fa-$font"))?"selected":"" }} data-label='{{$font}}'>{{$font}}</option>
    @endforeach
</select>
