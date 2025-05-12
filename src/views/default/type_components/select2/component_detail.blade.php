<?php
$datatable = isset($form['datatable']) ? $form['datatable'] : '';
if ($datatable && isset($form['relationship_table']) && !$form['relationship_table']) {
    $datatable = explode(',', $datatable);
    $table = $datatable[0];
    $field = $datatable[1];
    echo CRUDBooster::first($table, ['id' => $value])->$field;
}

if ($datatable && isset($form['relationship_table']) && $form['relationship_table']) {
    $datatable_table = explode(',', $datatable)[0];
    $datatable_field = explode(',', $datatable)[1];
    if(isset($form['datatable_orig']) && $form['datatable_orig'] != ''){
        $params = explode("|", $form['datatable_orig']);
        if(!isset($params[2])) $params[2] = "id";

        // Check if $id is defined, otherwise use null
        $id_value = isset($id) ? $id : null;
        if ($id_value) {
            $row = DB::table($params[0])->where($params[2], $id_value)->first();
            if ($row && isset($row->{$params[1]})) {
                $values = explode(",", $row->{$params[1]});
                $tableData = DB::table($datatable_table)->whereIn("id", $values)->select($datatable_field)->pluck($datatable_field)->toArray();
            } else {
                $tableData = [];
            }
        } else {
            $tableData = [];
        }
    } else {
        // Check if $id is defined, otherwise use null
        $id_value = isset($id) ? $id : null;
        if ($id_value) {
            $foreignKey = CRUDBooster::getForeignKey($table, $form['relationship_table']);
            $foreignKey2 = CRUDBooster::getForeignKey($datatable_table, $form['relationship_table']);
            $ids = DB::table($form['relationship_table'])->where($foreignKey, $id_value)->pluck($foreignKey2)->toArray();
            $tableData = DB::table($datatable_table)->whereIn('id', $ids)->pluck($datatable_field)->toArray();
        } else {
            $tableData = [];
        }
    }

    echo implode(", ", $tableData);
}

if (isset($form['dataenum']) && $form['dataenum']) {
    echo $value;
}

?>
