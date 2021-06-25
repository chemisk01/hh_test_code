<?php

namespace Cms\FormRequests\News;

use Cms\Rules\News\IsPinnedRule;
use EduShare\Rest\Http\Requests\StoreRequest;

class NewsStoreRequest extends StoreRequest
{
    public function rules()
    {
        return [
            'data.attributes.title' => 'required|string|max:255',
            'data.attributes.author' => 'nullable|string|max:255',
            'data.attributes.description_short' => 'nullable|string',
            'data.attributes.description' => 'nullable|string',
            'data.attributes.iri' => 'nullable|string',
            'data.attributes.image_uuid' => 'nullable|uuid|exists:lms_files_storage,file_id',
            'data.attributes.source' => 'nullable|string',
            'data.attributes.is_active' => 'nullable|bool',
            'data.attributes.status' => 'nullable|int',
            'data.attributes.is_pinned' => [
                'nullable',
                'bool',
                new IsPinnedRule($this->input('data.attributes.status'))
            ],
            'data.attributes.tags' => 'nullable|array',
            'data.attributes.created_at' => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'data.attributes.title' => trans('news.form.data.attributes.title'),
            'data.attributes.author' => trans('news.form.data.attributes.author'),
            'data.attributes.description_short' => trans('news.form.data.attributes.description_short'),
            'data.attributes.description' => trans('news.form.data.attributes.description'),
            'data.attributes.iri' => trans('news.form.data.attributes.iri'),
            'data.attributes.image_uuid' => trans('news.form.data.attributes.image_uuid'),
            'data.attributes.source' => trans('news.form.data.attributes.source'),
            'data.attributes.is_active' => trans('news.form.data.attributes.is_active'),
            'data.attributes.status' => trans('news.form.data.attributes.status'),
            'data.attributes.is_pinned' => trans('news.form.data.attributes.is_pinned'),
            'data.attributes.tags' => trans('news.form.data.attributes.tags'),
            'data.attributes.created_at' => trans('news.form.data.attributes.created_at'),
        ];
    }
}
