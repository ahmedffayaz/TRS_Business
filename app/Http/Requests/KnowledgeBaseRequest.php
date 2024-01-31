<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeBaseRequest extends FormRequest
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
        $id = $this->route()->parameters() ? $this->route()->parameters()['knowledge_base'] : null;
        switch ($this->method()) {
            case 'POST':
                return [
                    'companies' => 'required|required',
                    'roles' => 'required|required',
                    'created_by' => 'required|integer',
                    'updated_by' => 'required|integer',
                    'name' => 'required|string|max:191|unique:knowledge_bases',
                    'description' => 'nullable|string|max:100',
                    'image' => 'nullable|sometimes|image|mimes:png,jpg,jpeg'
                ];
                break;
            case 'PUT':
            case 'PATCH':
                return [
                    'companies' => 'required|required',
                    'roles' => 'required|required',
                    'updated_by' => 'required|integer',
                    'name' => 'required|string|max:191|unique:knowledge_bases,name,' . $id . ',id',
                    'description' => 'nullable|string|max:100',
                    'image' => 'nullable|sometimes|image|mimes:png,jpg,jpeg'
                ];
                break;
            default:
                return [];
                break;
        }
    }
}
