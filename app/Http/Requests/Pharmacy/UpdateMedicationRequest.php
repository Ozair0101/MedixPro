<?php

namespace App\Http\Requests\Pharmacy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMedicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hospitalId = (int) $this->header('X-Hospital-Id', 1);
        // The route uses a plain {id} parameter, not model binding.
        $medicationId = $this->route('id');

        return [
            'name' => [
                'sometimes', 'required', 'string', 'max:191',
                Rule::unique('medications', 'name')
                    ->where('hospital_id', $hospitalId)
                    ->ignore($medicationId),
            ],
            'generic_name' => ['sometimes', 'nullable', 'string', 'max:191'],
            'strength' => ['sometimes', 'nullable', 'string', 'max:50'],
            'form' => ['sometimes', 'required', 'string', 'max:50'],
            'standard_dose' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'controlled' => ['sometimes', 'nullable', 'boolean'],
            'min_stock' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // A client must never be able to move a medication to another hospital.
        $this->request->remove('hospital_id');
        $this->request->remove('created_by');
    }
}
