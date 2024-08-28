<form wire:submit.prevent="{{ $questionForm->isUpdate ? 'updateKnowledgeBase(' . $questionForm->id . ')' : 'storeKnowledgeBase' }}">
    <div class="row">
        <div class="col-md-6">
            <div class="row mb-2">
                <div class="col-md-12">
                    <span wire:ignore.>
                        <x-input-label for="category" value="Knowledge Base Category" />
                        <x-select-input id="category-select"
                            :class="$errors->has('questionForm.knowledge_base_category_id') ? 'error select2' : 'select2'"
                            wire:model="questionForm.knowledge_base_category_id">
                            @isset($knowledgeBaseCategories)
                                <option value="">Select Category ...</option>
                                @foreach ($knowledgeBaseCategories as $knowledgeBaseCategory)
                                    <option value="{{ $knowledgeBaseCategory?->id }}">{{ $knowledgeBaseCategory?->name }}</option>
                                @endforeach
                            @endisset
                        </x-select-input>
                    </span>
                    @error('questionForm.knowledge_base_category_id')
                        <x-input-error :message="$message" />
                    @enderror
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12">
                    <x-input-label for="question" class="required" value="Question" />
                    <x-input type="text" name="question" id="question"
                        :class="$errors->has('questionForm.question') ? 'error' : ''"
                        placeholder="Enter question" wire:model="questionForm.question" />
                    @error('questionForm.question')
                        <x-input-error :message="$message" />
                    @enderror
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12">
                    <span wire:ignore.>
                        <x-input-label for="answer" class="required" value="Answer" />
                        <x-textarea name="answer" id="count_text" rows="4" wire:model="questionForm.answer"
                        :class="$errors->has('questionForm.question') ? 'error' : ''" />
                    </span>
                    @error('questionForm.answer')
                        <x-input-error :message="$message" />
                    @enderror
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12">
                    <x-input-label for="keyword" class="required" value="Keywords (separated by , )" />
                    <x-input type="text" name="keywords" id="keywords"
                        :class="$errors->has('questionForm.keywords') ? 'error' : ''"
                        placeholder="A, B, C" wire:model="questionForm.keywords" />
                    @error('questionForm.keywords')
                        <x-input-error :message="$message" />
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <x-button class="btn btn-primary me-1 waves-effect waves-float waves-light" type="submit" tabindex="4"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $questionForm->isUpdate ? 'Update' : 'Add' }}</span>
                        <span wire:loading>
                            <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
                        </span>
                    </x-button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <x-input-label for="answer" value="Answer Preview" />
            <div class="form-group answer-preview">
        </div>
    </div>
</form>

@script
    <script>
        $(document).ready(function () {
            Livewire.dispatch('select-container');
            Livewire.on('resetSelectInput', () => {
                $(document).ready(function () {
                    Livewire.dispatch('select-container');
                    categorySelect();
                    markdownTextEditorInitialize();
                })
            });

            // Make function to reinitialize category select2 and bind values
            function categorySelect() {
                Livewire.on('category-select', (data) => {
                    var $select = $('#category-select');
                    // Clear existing selections
                    $select.val(null).trigger('change');
                    // Set the new selections
                    $select.val(data[0].formCategory).trigger('change');
                });

                $('#category-select').on('change', function(event) {
                    @this.set('questionForm.knowledge_base_category_id', $(this).val());
                });
            }

            categorySelect();

            // Markdown reinitialize function
            function markdownTextEditorInitialize() {
                var converter = new Showdown.Converter();
                var mte = new MTE(document.getElementsByTagName('textarea')[0]);

                $('.fa-header').addClass('fa-heading');

                $(document).on('change keyup', '[name="answer"]', function() {
                    $('.answer-preview').html(converter.makeHtml($(this).val()));
                });
            }

            markdownTextEditorInitialize();
        })
    </script>
@endscript
