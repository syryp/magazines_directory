<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthorsController extends Controller
{
    public function authors(Request $request)
    {
        $data = $this->validate($request, [
            'perPage' => ['required_with:page', 'integer', 'min:1'],
            'page' => ['required_with:perPage', 'integer', 'min:1'],
        ]);

        $authors = (new Author())
            ->when(array_key_exists('perPage', $data), function (Builder $query) use($data) {
                $query->paginate($data['perPage']);
            })
            ->get();

        return $authors->map(function(Author $author) {
            return [
                'id' => $author->id,
                'firstname' => $author->firstname,
                'surname' => $author->surname,
                'patronymic' => $author->patronymic,
            ];
        });
    }

    public function addAuthor(Request $request): void {
        $data = $this->validateAuthorsInput($request);

        (new Author())
            ->create([
                'firstname' => $data['firstname'],
                'surname' => $data['surname'],
                'patronymic' => array_key_exists('patronymic', $data) ?  $data['patronymic'] : null,
            ]);
    }

    public function updateAuthor(Request $request): void {
        $this->validate($request, [
            'id' => ['required', 'integer', Rule::exists(Author::class, 'id')],
        ]);

        $data = $this->validateAuthorsInput($request);

        (new Author())
            ->where('id', $request->get('id'))
            ->update([
                'firstname' => $data['firstname'],
                'surname' => $data['surname'],
                'patronymic' => array_key_exists('patronymic', $data) ?  $data['patronymic'] : null,
            ]);
    }

    public function deleteAuthor(Request $request): void {
        $this->validate($request, [
            'id' => ['required', 'integer', Rule::exists(Author::class, 'id')],
        ]);

        (new Author())
            ->where('id', $request->get('id'))
            ->delete();
    }

    private function validateAuthorsInput(Request $request): array
    {
        return $this->validate($request, [
            'firstname' => ['required', 'string', 'between:3,64'],
            'surname' => ['required', 'string', 'max:64'],
            'patronymic' => ['nullable', 'string', 'max:64'],
        ]);
    }
}
