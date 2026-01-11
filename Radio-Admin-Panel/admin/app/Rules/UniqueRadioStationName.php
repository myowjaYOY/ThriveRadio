<?php

namespace App\Rules;

use Closure;
use App\Models\RadioStation;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueRadioStationName implements ValidationRule
{
    public function __construct($city_id ,$category_id,$radiostation_id = NULL)
    {
        $this->radiostation_id = $radiostation_id;
        $this->city_id = $city_id;
        $this->category_id = $category_id;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($this->radiostation_id != NULL)
        {
            $count = RadioStation::where('name',$value)->where('id', '!=' , $this->radiostation_id)->where(['city_id' => $this->city_id, 'category_id' => $this->category_id])->count();
            if($count > 0)
            {
                $fail('The radio station name must be unique within the specified city and category.');
            }
        }

    }
}
