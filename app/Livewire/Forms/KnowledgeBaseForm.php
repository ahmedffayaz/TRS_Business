<?php

namespace App\Livewire\Forms;

use App\Models\KnowledgeBase;
use Livewire\Attributes\Validate;
use Livewire\Form;

class KnowledgeBaseForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public $knowledge_base_category_id;
    public ?string $question;
    public $answer;
    public $keywords;

    public function rules(): array
    {
        if ($this->isUpdate) {
            return [
                'knowledge_base_category_id' => 'required',
                'question' => 'required|string|max:191|unique:knowledge_bases,question,' . $this->id . ',id',
                'answer' => 'required|string',
                'keywords' => 'nullable|string'
            ];
        } else {
            return [
                'knowledge_base_category_id' => 'required',
                'question' => 'required|string|max:191|unique:knowledge_bases',
                'answer' => 'required|string',
                'keywords' => 'nullable|string'
            ];
        }
    }

    public function validationAttributes(): array
    {
        return [
            'knowledge_base_category_id' => 'knowledge base category',
            'question' => 'question',
            'answer' => 'answer',
            'keywords' => 'keywords'
        ];
    }

    public function set(KnowledgeBase $knowledgeBase)
    {
        $this->id = $knowledgeBase?->id;
        $this->knowledge_base_category_id = $knowledgeBase?->knowledge_base_category_id;
        $this->question = $knowledgeBase?->question;
        $this->answer = $knowledgeBase?->answer;
        $knowledgeBaseKeywords = $knowledgeBase?->keywords?->pluck('name')?->toArray();
        $knowledgeBaseKeywordsString = $knowledgeBaseKeywords ? implode(', ', $knowledgeBaseKeywords) : '';
        $this->keywords = $knowledgeBaseKeywordsString;
    }
}
