<?php

namespace App\Http\Requests\AuthRequest;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RegisterRequest
 *
 * This class handles the validation of user registration requests.
 * It ensures that the necessary data is provided and meets the specified criteria before passing the request to the controller.
 *
 * @package App\Http\Requests\AuthRequest
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always returns true, allowing everyone to make this request.
     */
    public function authorize()
    {
        return true; // Allow everyone to make this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * These rules ensure that the request contains a valid name, email, and password.
     *
     * @return array The validation rules for the registration request.
     */
    public function rules()
    {
        return [
            'email' => 'nullable|string|email|max:255|unique:users', // Make email required
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => [
                'required',
                'min:6',
                'regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\W_]).{6,}$/', // تأكد من وجود المحارف
                'confirmed'
            ],


    'phone' => ['required', 'regex:/^(9|3|5|7|6)[0-9]{8}$/', 'unique:users,phone,'],


        ];
    }
}
