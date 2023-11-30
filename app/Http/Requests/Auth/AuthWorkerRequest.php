<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AuthWorkerRequest extends FormRequest
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
          "name" => "string|min:3|max:20|required",
          "email" => "string|email|required|unique:workers,email",
          "password" => "string|min:3|max:20|required|confirmed",
          "phone" => "min:10|max:30|string|required|unique:workers,phone",
          "location" => "string|required|unique:workers,location|max:200|min:10",
        ];
    }
}
