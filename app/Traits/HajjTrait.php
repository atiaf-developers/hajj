<?php

namespace App\Traits;

use App\Models\Pilgrim;
use App\Models\Location;
use DB;
use QrCode;

trait HajjTrait {

    protected function _import_csv_hajj($table, $csv, $extra_params) {
        $new_filename = time() . mt_rand(1, 1000000) . '.csv';
        $path = public_path("uploads/csv/$new_filename");
        $file = fopen($path, 'w');

        fputcsv($file, array('ssn', 'name', 'nationality', 'mobile', 'reservation_no', 'code', 'location_id', 'pilgrim_class_id', 'gender','image'));
        if (($handle = fopen($csv, 'r')) !== false) {
            // get the first row, which contains the column-titles (if necessary)
            $header = fgetcsv($handle);

            // loop through the file line-by-line
            $lastPilgrim = Pilgrim::orderBy('id', 'DESC')->first();
            $location = Location::where('id', $extra_params['location'])->first();
            //dd( $extra_params['location']);
            $id = $lastPilgrim ? $lastPilgrim->id : 0;
            //dd($id);
            while (($data = fgetcsv($handle, 0, ';')) !== false) {
                $id++;
                //$qr_image_name = time() . mt_rand(1, 1000000) . '.png';
                $pilgrim_code = $this->getNextSerialNumber($id, $location->prefix);
                //QrCode::format('png')->size(300)->generate($pilgrim_code, base_path('public/uploads/pilgrims/' . $qr_image_name));
                $new_data = array(
                    'ssn' => $data[0],
                    'name' => $data[1],
                    'nationality' => $data[2],
                    'mobile' => $data[3],
                    'reservation_no' => $data[4],
                    'code' => $pilgrim_code,
                    'location_id' => $extra_params['location'],
                    'pilgrim_class_id' => $extra_params['pilgrim_class'],
                    'gender' => $extra_params['gender'],
                    'image' => $extra_params['gender']==1?'male.png':'female.png',
                    //'qr_image' => $qr_image_name,
                );
                fputcsv($file, $new_data, ";");

                unset($data);
            }

            fclose($handle);
            unlink($csv);
        }

        $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE $table CHARACTER SET utf8 FIELDS TERMINATED BY ';' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`ssn`, `name`, `nationality`, `mobile`,`reservation_no`,`code`,`location_id`,`pilgrim_class_id`,`gender`,`image`,`created_at`,`updated_at` ) SET gender=TRIM(BOTH '\\r' FROM gender),updated_at=current_timestamp(),created_at=current_timestamp()", addslashes($path));
        $result = DB::connection()->getpdo()->exec($query);

        fclose($file);
        unlink($path);
        //$query = "LOAD DATA LOCAL INFILE '".$csv."' INTO TABLE $table FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\\n'";
        //return DB::connection()->getpdo()->exec($query);
    }

    protected function _import_csv_hajj2($table, $csv, $extra_params) {
        $new_data = [];
        if (($handle = fopen($csv, 'r')) !== false) {
            // get the first row, which contains the column-titles (if necessary)
            $header = fgetcsv($handle);

            // loop through the file line-by-line
            $lastPilgrim = Pilgrim::orderBy('created_at', 'DESC')->first();
            $location = Location::where('id', $extra_params['location'])->first();
            $id = $lastPilgrim ? $lastPilgrim->id : 0;
            $prefix = $location->prefix;
            //dd($this->getNextSerialNumber($id, $prefix));
            while (($data = fgetcsv($handle, 0, ';')) !== false) {
                $id++;
                $new_data[] = array(
                    'ssn' => $data[0],
                    'name' => $data[1],
                    'nationality' => $data[2],
                    'mobile' => $data[3],
                    'reservation_no' => $data[4],
                    'code' => $this->getNextSerialNumber($id, $prefix),
                );

                unset($data);
            }
            fclose($handle);
        }
        $gender = $extra_params['gender'];
        $location = $extra_params['location'];
        $pilgrim_class = $extra_params['pilgrim_class'];
        $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE $table CHARACTER SET utf8 FIELDS TERMINATED BY ';' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`ssn`, `name`, `nationality`, `mobile`,`reservation_no`,`code`,`location_id`,`pilgrim_class_id`,`gender` ) SET gender=$gender,location_id=$location,pilgrim_class_id=$pilgrim_class,reservation_no=TRIM(BOTH '\\r' FROM reservation_no)", addslashes($csv));
        //$query = "LOAD DATA LOCAL INFILE '".$csv."' INTO TABLE $table FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\\n'";

        return DB::connection()->getpdo()->exec($query);
    }

    public function getNextSerialNumber($id, $prefix) {

        //$number = substr($id, 2);
        return $prefix . sprintf('%07d', intval($id));
    }

}
