<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                            'required',
                            'unique:locations,name'
                      ],
            'longitude' => [
                                'required',
                                'numeric',
                                'between:-180.0000000,180.0000000',
                                'unique:locations,longitude,NULL,NULL,latitude,' . request()->get('latitude')
                           ],
            'latitude' => [
                                'required',
                                'numeric',
                                'between:-90.0000000,90.0000000',
                          ]
        ];
    }
}
