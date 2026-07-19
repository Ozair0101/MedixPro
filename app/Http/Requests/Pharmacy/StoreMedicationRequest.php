<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hospitalId = (int) $this->header('X-Hospital-Id', 1);

        return [
            // Unique per hospital, not globally: two facilities may legitimately
            // stock the same drug name.
            'name' => [
                'required', 'string', 'max:191',
                Rule::unique('medications', 'name')->where('hospital_id', $hospitalId),
            ],
            'generic_name' => ['nullable', 'string', 'max:191'],
            'strength' => ['nullable', 'string', 'max:50'],
            'form' => ['required', 'string', 'max:50'],
            'standard_dose' => ['nullable', 'numeric', 'min:0'],
            'controlled' => ['nullable', 'boolean'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // hospital_id and created_by are set by the controller from the request
        // context, never from client input.
        $this->request->remove('hospital_id');
        $this->request->remove('created_by');
    }
}
