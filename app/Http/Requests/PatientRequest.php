<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $patientId = $this->route('patient') ? $this->route('patient')->id : null;

        return [
            'patient_code' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('patients', 'patient_code')->ignore($patientId, 'id')
            ],
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|max:20',
            'blood_type' => [
                'nullable',
                'string',
                'max:5',
                Rule::in(['A+','A-','B+','B-','AB+','AB-','O+','O-']),
            ],
            'marital_status' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:20',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_name' => 'nullable|string|max:100',
            'medical_file' => 'nullable|string|max:100',
            'remark' => 'nullable|string',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'address_json' => 'nullable|array',
            'is_active' => 'boolean',
        ];
    }
}
