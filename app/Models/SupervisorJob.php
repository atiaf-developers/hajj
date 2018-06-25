<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorJob extends MyModel
{
    protected $table = "supervisors_jobs";

	public function translations() {
		return $this->hasMany(SupervisorJobTranslation::class, 'supervisor_job_id');
	}

	protected static function boot() {
		parent::boot();

		static::deleting(function($supervisor_job) {
			foreach ($supervisor_job->translations as $translation) {
				$translation->delete();
			}
		});
	}


}
