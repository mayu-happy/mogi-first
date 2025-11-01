<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'price'       => ['required', 'integer', 'min:1'], // ← 整数に統一
            // 他の項目も…
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $norm = mb_convert_kana($this->input('price'), 'n', 'UTF-8');
            $clean = preg_replace('/[^\d]/', '', $norm);
            $this->merge(['price' => $clean !== '' ? (int)$clean : null]);
        }
    }
}
