<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	"name",
		"email",
		"telephone",
		"country",
		"reason_for_buying",
		"comments",
		"property_link",
		"property_reference_number",
		"language_used",
        "source",
        "sub_source",
    ];

}