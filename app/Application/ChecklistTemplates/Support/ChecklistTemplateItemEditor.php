<?php

declare(strict_types=1);

namespace App\Application\ChecklistTemplates\Support;

use App\Models\ChecklistTemplate;

class ChecklistTemplateItemEditor
{
    /**
     * @return list<array{
     *     id: int|null,
     *     title: string,
     *     description: string,
     *     group_label: string,
     *     sort_order: int,
     *     is_required: bool
     * }>
     */
    public function fromTemplate(ChecklistTemplate $template): array
    {
        return $template->items()
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description ?? '',
                'group_label' => $item->group_label ?? '',
                'sort_order' => $item->sort_order,
                'is_required' => (bool) $item->is_required,
            ])
            ->all();
    }

    /**
     * @param  list<array{
     *     id: int|null,
     *     title: string,
     *     description: string,
     *     group_label: string,
     *     sort_order: int,
     *     is_required: bool
     * }>  $items
     * @return list<array{
     *     id: int|null,
     *     title: string,
     *     description: string,
     *     group_label: string,
     *     sort_order: int,
     *     is_required: bool
     * }>
     */
    public function add(array $items): array
    {
        $items[] = $this->blankItem(count($items) + 1);

        return $items;
    }

    /**
     * @param  list<array{
     *     id: int|null,
     *     title: string,
     *     description: string,
     *     group_label: string,
     *     sort_order: int,
     *     is_required: bool
     * }>  $items
     * @return list<array{
     *     id: int|null,
     *     title: string,
     *     description: string,
     *     group_label: string,
     *     sort_order: int,
     *     is_required: bool
     * }>
     */
    public function remove(array $items, int $index): array
    {
        unset($items[$index]);

        return $this->reindex(array_values($items));
    }

    /**
     * @return array{
     *     id: int|null,
     *     title: string,
     *     description: string,
     *     group_label: string,
     *     sort_order: int,
     *     is_required: bool
     * }
     */
    private function blankItem(int $sortOrder): array
    {
        return [
            'id' => null,
            'title' => '',
            'description' => '',
            'group_label' => '',
            'sort_order' => $sortOrder,
            'is_required' => true,
        ];
    }

    /**
     * @param  list<array{
     *     id: int|null,
     *     title: string,
     *     description: string,
     *     group_label: string,
     *     sort_order: int,
     *     is_required: bool
     * }>  $items
     * @return list<array{
     *     id: int|null,
     *     title: string,
     *     description: string,
     *     group_label: string,
     *     sort_order: int,
     *     is_required: bool
     * }>
     */
    private function reindex(array $items): array
    {
        foreach ($items as $position => &$item) {
            $item['sort_order'] = $position + 1;
        }

        return $items;
    }
}
