<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Magazine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class MagazinesController extends Controller
{
    public function magazines(Request $request)
    {
        $data = $this->validate($request, [
            'perPage' => ['required_with:page', 'integer', 'min:1'],
            'page' => ['required_with:perPage', 'integer', 'min:1'],
        ]);

        $magazines = (new Magazine())
            ->when(array_key_exists('perPage', $data), function (Builder $query) use($data) {
                $query->paginate($data['perPage']);
            })
            ->with('authors:id,firstname,surname')
            ->get();

        return $magazines->map(function (Magazine $magazine)
        {
            return [
                'id' => $magazine->id,
                'name' => $magazine->name,
                'short_description' => $magazine->short_description,
                'image' => $magazine->getImageUrl(),
                'release_date' => Carbon::make($magazine->release_date),
                'authors' => $magazine->authors->map(function (Author $author) {
                    return [
                        'id' => $author->id,
                        'firstname' => $author->firstname,
                        'surname' => $author->surname,
                    ];
                })
            ];
        });
    }

    public function addMagazine(Request $request): void
    {
        $data = $this->validateMagazineInput($request);

        $magazine = (new Magazine())
            ->create([
               'name' => $data['name'],
               'short_description' => array_key_exists('short_description', $data) ?  $data['short_description'] : null,
               'release_date' => Carbon::make($data['release_date']),
            ]);

        $image = $request->file('image');
        $this->uploadImage($image, $magazine->id);

        $magazine->authors()->sync($data['authors']);
    }

    public function updateMagazine(Request $request): void
    {
        $this->validate($request, [
            'id' => ['required', 'integer', Rule::exists(Magazine::class, 'id')],
        ]);

        $data = $this->validateMagazineInput($request);

        $magazine = (new Magazine())
            ->where('id', $request->get('id'))
            ->first();

        $magazine->update([
            'name' => $data['name'],
            'short_description' => array_key_exists('short_description', $data) ?  $data['short_description'] : null,
            'release_date' => Carbon::make($data['release_date']),
        ]);

        $image = $request->file('image');
        $this->uploadImage($image, $magazine->id);

        $magazine->authors()->sync($data['authors']);
    }

    public function deleteMagazine(Request $request): void
    {
        $this->validate($request, [
            'id' => ['required', 'integer', Rule::exists(Magazine::class, 'id')],
        ]);

        (new Magazine())
            ->where('id', $request->get('id'))
            ->delete();
    }

    public function validateMagazineInput(Request $request): array
    {
        return $this->validate($request, [
            'name' => ['required', 'string', 'max:64'],
            'short_description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'max:2048', 'mimetypes:image/png,image/jpg'],
            'release_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'authors' => ['array'],
            'authors.*' => ['required', 'integer', Rule::exists(Author::class, 'id')],
        ]);
    }

    public function uploadImage(?UploadedFile $image, int $magazineId): ?string
    {
        if ($image === null) {
            return null;
        }

        $uploadPath = 'magazine_images/' . uniqid() . '.' . $image->extension();

        if ( ! Storage::disk('public')->put($uploadPath, $image->getContent(), 'public')) {
            throw new RuntimeException('Error uploading image');
        }

        (new Magazine())
            ->where('id', $magazineId)
            ->update([
                'image' => $uploadPath,
            ]);

        return $uploadPath;
    }
}
