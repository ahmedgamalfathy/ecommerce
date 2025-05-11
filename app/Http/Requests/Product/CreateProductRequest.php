<?php

namespace App\Http\Requests\Product;

use App\Enums\Product\LimitedQuantity;
use App\Helpers\ApiResponse;
use App\Enums\Product\ProductStatus;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ResponseCode\HttpStatusCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateProductRequest extends FormRequest
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
            "productMedia" => ["required", "array"],
            "name" => ["required", "string", "max:255"],
            "price" => ["required"],
            "status" => ["required", new Enum(ProductStatus::class)],
            "description" => ["nullable", "string"],
            "categoryId" => ["nullable"],
            "subCategoryId" => ["nullable"],
            'cost' => ['required'],
            "isLimitedQuantity" => ["required", new Enum(LimitedQuantity::class)],
            'quantity' => ['required_if:isLimitedQuantity,' . LimitedQuantity::LIMITED->value],
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error('', $validator->errors(), HttpStatusCode::UNPROCESSABLE_ENTITY)
        );
    }
    public function messages()
    {
        return [
            'name.required'=> __('validation.custom.required'),
            'price.required' => __('validation.custom.required'),
            'cost.required' => __('validation.custom.required'),
            'isLimitedQuantity.required' => __('validation.custom.required'),
            'quantity.required' => __('validation.custom.required'),
        ];
    }
}
