<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ClassificationRequest extends FormRequest
{
    public function rules(): array{
        return [
            'name' => ['required','string','max:255'],
            'description' => ['required','string','max:255'],
            'revenue_threshold' => ['required','integer'],
            'revenue_max' => ['required','integer'],
            'employee_threshold' => ['required','integer'],
            'employee_max' => ['required','integer'],
            'wz_codes' => ['required','array']
        ];
    }
}