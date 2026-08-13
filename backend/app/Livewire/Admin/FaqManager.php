<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\Faq;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class FaqManager extends Component
{
    public bool $isModalOpen = false;
    public ?int $faqId = null;
    public string $category = 'Umum';
    public string $question = '';
    public string $answer = '';
    public string $keywords = '';
    public int $sortOrder = 0;
    public bool $isActive = true;
    public string $search = '';

    public function openModal(): void
    {
        $this->resetForm();
        $this->sortOrder = ((int) Faq::max('sort_order')) + 10;
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $this->faqId = $faq->id;
        $this->category = $faq->category ?: 'Umum';
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->keywords = $faq->keywords ?? '';
        $this->sortOrder = (int) $faq->sort_order;
        $this->isActive = (bool) $faq->is_active;
        $this->isModalOpen = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'category' => ['required', 'string', 'max:100'],
            'question' => ['required', 'string', 'min:5', 'max:500'],
            'answer' => ['required', 'string', 'min:5', 'max:5000'],
            'keywords' => ['nullable', 'string', 'max:2000'],
            'sortOrder' => ['required', 'integer', 'min:0', 'max:9999'],
            'isActive' => ['boolean'],
        ]);

        $faq = $this->faqId ? Faq::findOrFail($this->faqId) : new Faq();
        $method = $faq->exists ? 'UPDATE' : 'CREATE';

        $faq->fill([
            'category' => trim($validated['category']),
            'question' => trim($validated['question']),
            'answer' => trim($validated['answer']),
            'keywords' => $this->nullableString((string) ($validated['keywords'] ?? '')),
            'sort_order' => $validated['sortOrder'],
            'is_active' => $validated['isActive'],
        ]);
        $faq->save();

        ActivityLogger::log('FAQ & MONIKS', $method, 'success', auth()->id(), $faq->question);
        session()->flash('message', 'FAQ berhasil disimpan. Perubahan otomatis menjadi basis pengetahuan MONIKS.');
        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $faq->update(['is_active' => ! $faq->is_active]);
        ActivityLogger::log('FAQ & MONIKS', 'UPDATE', 'success', auth()->id(), $faq->question.' status='.(int) $faq->is_active);
    }

    public function delete(int $id): void
    {
        $faq = Faq::findOrFail($id);
        $question = $faq->question;
        $faq->delete();
        ActivityLogger::log('FAQ & MONIKS', 'DELETE', 'success', auth()->id(), $question);
        session()->flash('message', 'FAQ berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.faq-manager', [
            'faqs' => Faq::query()
                ->when($this->search, function ($query) {
                    $term = '%'.$this->search.'%';
                    $query->where(function ($subQuery) use ($term) {
                        $subQuery->where('question', 'like', $term)
                            ->orWhere('answer', 'like', $term)
                            ->orWhere('keywords', 'like', $term)
                            ->orWhere('category', 'like', $term);
                    });
                })
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(['faqId', 'question', 'answer', 'keywords']);
        $this->category = 'Umum';
        $this->sortOrder = 0;
        $this->isActive = true;
        $this->resetValidation();
    }

    private function nullableString(string $value): ?string
    {
        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
