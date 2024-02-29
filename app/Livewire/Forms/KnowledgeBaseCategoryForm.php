<?php

namespace App\Livewire\Forms;

use App\Models\KnowledgeBaseCategory;
use Livewire\Attributes\Validate;
use Livewire\Form;

class KnowledgeBaseCategoryForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public $business_id;
    public $created_by;
    public $updated_by;
    public ?string $name;
    public $roles = [];

    public function rules(): array
    {
        if ($this->isUpdate) {
            return [
                'roles' => 'nullable',
                'business_id' => 'required|integer',
                'created_by' => 'required|integer',
                'updated_by' => 'required|integer',
                'name' => 'required|string|max:191|unique:knowledge_base_categories,name,' . $this->id . ',id',
            ];
        } else {
            return [
                'roles' => 'nullable',
                'business_id' => 'required|integer',
                'created_by' => 'required|integer',
                'updated_by' => 'required|integer',
                'name' => 'required|string|max:191|unique:knowledge_base_categories',
            ];
        }
    }

    public function validationAttributes(): array
    {
        return [
            'roles' => 'roles',
            'name' => 'name',
        ];
    }

    public function setKnowledgeBaseCategory(KnowledgeBaseCategory $knowledgeBaseCategory)
    {
        $this->id = $knowledgeBaseCategory?->id;
        $this->roles = $knowledgeBaseCategory?->roles;
        $this->created_by = $knowledgeBaseCategory?->created_by;
        $this->name = $knowledgeBaseCategory?->name;
    }
}
