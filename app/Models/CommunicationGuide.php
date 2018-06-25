<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunicationGuide extends MyModel
{
    protected $table = "communication_guides";


    public function translations() {
        return $this->hasMany(CommunicationGuideTranslation::class, 'communication_guide_id');
    }

   public function supervisors()
   {
       return $this->belongsToMany(Supervisor::class,'communication_guide_supervisors','communication_guide_id','supervisor_id');
   }

    public function communication_guides_supervisors()
    {
        return $this->hasMany(CommunicationGuideSupervisor::class,'communication_guide_id');
    }


    public static function transform($item){
        
        $lang =  static::getLangCode();

        $transformer = new \stdClass();
        
        $transformer->id =  $item->id;
        $transformer->title = $item->title;
        $transformer->description = $item->description;
        $transformer->image = $item->image ? url('public/uploads/communication_guides') . '/' . $item->image : '';

        $transformer->supervisors = Supervisor::transformCollection(
            $item->supervisors()
            ->join('supervisors_jobs','supervisors_jobs.id','=','supervisors.supervisor_job_id')
            ->join('supervisors_jobs_translations','supervisors_jobs.id','=','supervisors_jobs_translations.supervisor_job_id')
            ->where('supervisors_jobs_translations.locale',$lang)
            ->select('supervisors.id','supervisors.name','supervisors.supervisor_image','supervisors.contact_numbers','supervisors_jobs_translations.title as job')
            ->get());

       return $transformer;
        
    }




    protected static function boot() {
        parent::boot();

        static::deleting(function($communication_guide) {
            foreach ($communication_guide->translations as $translation) {
                $translation->delete();
            }

            foreach ($communication_guide->communication_guides_supervisors as $value) {
                $supervisor_id = $value->supervisor_id;
                $value->delete();
                $supervisor = Supervisor::where('id',$supervisor_id)->first();
                $supervisor->delete();
            }
            if ($communication_guide->image) {
                $old_image = $communication_guide->image;
                CommunicationGuide::deleteUploaded('communication_guides', $old_image);
            }
        });
        
    }
}
