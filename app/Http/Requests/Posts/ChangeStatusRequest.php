<?php

namespace App\Http\Requests\posts;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "id"=> "required|exists:posts,id",
            "status" => "required|in:approved,rejected",
            "rejected_reason" => "required_if:status,rejected|string|min:5|max:1000"
        ];
    }
}
