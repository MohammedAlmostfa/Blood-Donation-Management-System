<?php
namespace App\Http\Requests\AuthRequest;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LoginRequest
 *
 * This class handles the validation of user login requests.
 * It ensures that the necessary data is provided and meets the specified criteria before passing the request to the controller.
 *
 * @package App\Http\Requests\AuthRequest
 */
class LoginRequest extends FormRequest
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
     * These rules ensure that the request contains a valid email and password.
     *
     * @return array The validation rules for the login request.
     */
    public function rules()
    {
        return [
          'phone' => ['required','regex:/^(9|3|5|7|6)[0-9]{8}$/'],
            'password' => [
                'required',
                'min:6',
            ],

        ];
    }
}
