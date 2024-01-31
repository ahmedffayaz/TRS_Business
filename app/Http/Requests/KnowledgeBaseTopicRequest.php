<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeBaseTopicRequest extends FormRequest
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
    {dd($this->route());
        $id = $this->route()->parameters() ? $this->route()->parameters()['knowledge_base'] : null;
        switch ($this->method()) {
            case 'POST':
                return [
                    'knowledge_base_id' => 'required|integer',
                    'name' => 'required|string|max:191|unique:knowledge_base_topics'
                ];
                break;
            case 'PUT':
            case 'PATCH':
                return [
                    'knowledge_base_id' => 'required|integer',
                    'name' => 'required|string|max:191|unique:knowledge_base_topics,name,' . $id . ',id'
                ];
                break;
            default:
                return [];
                break;
        }
    }

    /**
     * @return array|string[]
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The topic name field is required.',
            'name.string' => 'The topic name should be string.',
            'name.max' => 'The topic name field must not be greater than 191 characters.',
            'name.unique' => 'The topic name has already been taken.'
        ];
    }
}
