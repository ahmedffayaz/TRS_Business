<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeBaseQuestionRequest extends FormRequest
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
        $id = $this->route()->parameters() ? $this->route()->parameters()['knowledge_base_question'] : null;
        switch ($this->method()) {
            case 'POST':
                return [
                    'knowledge_base_topic_id' => 'required|integer',
                    'question' => 'required|string|max:191|unique:knowledge_base_qas',
                    'description' => 'required|string',
                    'keywords' => 'nullable|string'
                ];
                break;
            case 'PUT':
            case 'PATCH':
                return [
                    'knowledge_base_topic_id' => 'required|integer',
                    'question' => 'required|string|max:191|unique:knowledge_base_qas,question,' . $id . ',id',
                    'description' => 'required|string',
                    'keywords' => 'nullable|string'
                ];
                break;
            default:
                return [];
                break;
        }
    }
}
