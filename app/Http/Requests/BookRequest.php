<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\BookUser;
use App\Models\Book;


class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    public function authorize(): bool
    {
        $book = $this->route('book');
        return $book
            ? $this->user()->can('update', $book)
            : $this->user()->can('create', Book::class);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:1',
            'author' => 'required|string|min:1',
            'status' => ['required', 'string', Rule::in(BookUser::allowedStatuses())],
        ];
    }
}
