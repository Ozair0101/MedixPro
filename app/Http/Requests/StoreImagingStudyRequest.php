<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for `imaging_study`.
 *
 * Rules are derived from the database: nullability, lengths, numeric
 * precision, foreign keys and CHECK enumerations all come from the catalog,
 * so they cannot drift from the constraints that actually apply.
 *
 * facility_id is deliberately absent — it is server-side tenancy state set
 * from the authenticated user, never accepted from a client.
 */
class StoreImagingStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accession_number' => ['required', 'string'],
            'patient_id' => ['required', 'uuid'],
            'encounter_id' => ['nullable', 'uuid'],
            'order_id' => ['nullable', 'uuid'],
            'modality_id' => ['required', 'uuid'],
            'procedure_concept_id' => ['nullable', 'uuid'],
            'body_site_concept_id' => ['nullable', 'uuid'],
            'laterality' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'scheduled_at' => ['nullable', 'date'],
            'performed_at' => ['nullable', 'date'],
            'performed_by' => ['nullable', 'uuid'],
            'contrast_used' => ['nullable', 'boolean'],
            'contrast_agent' => ['nullable', 'string'],
            'contrast_volume_ml' => ['nullable', 'numeric'],
            'radiation_dose_mgy' => ['nullable', 'numeric'],
            'is_repeat' => ['nullable', 'boolean'],
            'repeat_reason' => ['nullable', 'string'],
            'dicom_study_uid' => ['nullable', 'string'],
        ];
    }
}
