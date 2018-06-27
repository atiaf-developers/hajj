<?php

namespace App\Traits;

use App\Models\Setting;
use App\Models\NotiObject;
use App\Models\Noti;
use Image;
use DB;

trait Basic {

    protected $languages = array(
        'ar' => 'arabic',
        'en' => 'english',
        'ur' => 'urdu'
    );

    protected function inputs_check($model, $inputs = array(), $id = false, $return_errors = true) {
        $errors = array();
        foreach ($inputs as $key => $value) {
            $where_array = array();
            $where_array[] = array($key, '=', $value);
            if ($id) {
                $where_array[] = array('id', '!=', $id);
            }

            $find = $model::where($where_array)->get();

            if (count($find)) {

                $errors[$key] = array(_lang('app.' . $key) . ' ' . _lang("app.added_before"));
            }
        }

        return $errors;
    }

    public function _view($main_content, $type = 'front') {
        $main_content = "main_content/$type/$main_content";
        return view($main_content, $this->data);
    }

    protected function settings() {
        $settings = Setting::get();
        $settings[0]->noti_status = json_decode($settings[0]->noti_status);
        return $settings[0];
    }

    protected function slugsCreate() {
        $this->title_slug = 'title_' . $this->lang_code;
        $this->data['title_slug'] = $this->title_slug;
    }

    protected function lang_rules($columns_arr = array()) {
        $rules = array();

        if (!empty($columns_arr)) {
            foreach ($columns_arr as $column => $rule) {
                foreach ($this->languages as $lang_key => $locale) {
                    $key = $column . '.' . $lang_key;
                    $rules[$key] = $rule;
                }
            }
        }
        return $rules;
    }

    protected function create_noti($entity_id, $notifier_id, $entity_type, $notifible_type = 1) {
        $NotiObject = new NotiObject;
        $NotiObject->entity_id = $entity_id;
        $NotiObject->entity_type_id = $entity_type;
        $NotiObject->notifiable_type = $notifible_type;
        $NotiObject->save();
        $Noti = new Noti;
        $Noti->notifier_id = $notifier_id;
        $Noti->noti_object_id = $NotiObject->id;
        if ($notifier_id == null) {
            $Noti->read_status = 2;
        }
        $Noti->save();
    }

    protected function create_noti_multiple($entity_id, $notifier_id, $entity_type, $notifible_type = 1) {
        
    }

    public function updateValues($model, $data,$quote=false) {
        //dd($values);
        $table = $model::getModel()->getTable();
        //dd($table);

        $columns = array_keys($data);

        $ids = [];
        $sql_arr = [];
        $count=0;
        foreach ($data as $column => $value_arr) {
            //dd($value_arr);
            $cases = [];
            foreach ($value_arr as $one) {
                $id = (int) $one['id'];
                $value =  $one['value'];
                if($quote){
                      $cases[] = "WHEN {$id} then '{$value}'";
                }else{
                      $cases[] = "WHEN {$id} then {$value}";
                }
              
                $ids[] = $id;
            }
                
            $cases = implode(' ', $cases);
           
            if($count==0){
                 $sql_arr[] = "SET `{$column}` = CASE `id` {$cases} END";
            }else{
                 $sql_arr[] = "`{$column}` = CASE `id` {$cases} END";
            }
            $count++;
        }
     
   
        $ids = implode(',', $ids);
        $sql_str = implode(',', $sql_arr);
        //dd($sql_str);
        return DB::update("UPDATE `$table` $sql_str WHERE `id` in ({$ids})");
    }

    public function updateWithSequenceNumber($model, $column, $where_array = array()) {
        $table = $model::getModel()->getTable();
        $where_str = 'Where ';
        if (!empty($where_array)) {

            $count = 1;
            foreach ($where_array as $key => $value) {
                $where_str .= "$key=$value";
                if ($count < count($where_array)) {
                    $where_str .= ' and ';
                }

                $count++;
            }
        }
        //dd($where_str);
        return DB::update("
            UPDATE $table
            JOIN (SELECT @rank := 0) r
            SET $column=@rank:=@rank+1 $where_str;
            ");
    }
    
    public function updateWithMultipleValues($table,$data) {
        $cases = [];
        if (count($data) > 0) {
            foreach ($data as $key1 => $value1) {
                $cases[] = "WHEN name = '$key1' THEN '$value1'";
            }
        }
        //dd($cases);
        $cases = implode(' ', $cases);
        $sql = "UPDATE $table SET value = CASE $cases END";
        DB::statement($sql);
    }

}
