<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

abstract class ApiCrudController extends Controller
{
    protected string $modelClass;
    protected array $with = [];
    protected array $files = [];
    protected array $searchable = [];

    //Jumlah data default per halaman.
    protected int $perPage = 15;

    //Batas maksimal data per halaman.
    protected int $maxPerPage = 100;

    //Aturan validasi dari masing-masing controller turunan.
    abstract protected function rules(?Model $model = null): array;

    //Menampilkan daftar data.
    public function index(Request $request): JsonResponse
    {
        $query = $this->newQuery()
            ->with($this->with);

        $query = $this->applySearch($query, $request);
        $query = $this->applyFilters($query, $request);
        $query = $this->applySorting($query, $request);

        $perPage = $this->resolvePerPage($request);

        $items = $query
            ->paginate($perPage)
            ->withQueryString();

        return $this->successResponse(
            data: $items
        );
    }

    //Menyimpan data baru.
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(
            $this->rules()
        );

        $storedFiles = [];

        try {
            $item = DB::transaction(function () use (
                $request,
                $validated,
                &$storedFiles
            ) {
                $data = $this->prepareDataForStore(
                    $validated,
                    $request
                );

                [$data, $storedFiles] = $this->storeUploadedFiles(
                    request: $request,
                    data: $data
                );

                $item = $this->newQuery()->create($data);

                $this->afterStore(
                    $item,
                    $request
                );

                return $item;
            });

            return $this->successResponse(
                message: 'Data berhasil ditambahkan.',
                data: $item->load($this->with),
                status: 201
            );
        } catch (Throwable $exception) {
            $this->deleteStoredFiles($storedFiles);

            throw $exception;
        }
    }

    //Menampilkan satu data.
    public function show(int|string $id): JsonResponse
    {
        $item = $this->newQuery()
            ->with($this->with)
            ->findOrFail($id);

        return $this->successResponse(
            data: $item
        );
    }

    //Memperbarui data.
    public function update(
        Request $request,
        int|string $id
    ): JsonResponse {
        $item = $this->newQuery()
            ->findOrFail($id);

        $validated = $request->validate(
            $this->rules($item)
        );

        $newFiles = [];
        $oldFiles = [];

        try {
            DB::transaction(function () use (
                $request,
                $validated,
                $item,
                &$newFiles,
                &$oldFiles
            ) {
                $data = $this->prepareDataForUpdate(
                    $validated,
                    $request,
                    $item
                );

                [$data, $newFiles, $oldFiles] =
                    $this->storeUploadedFiles(
                        request: $request,
                        data: $data,
                        item: $item
                    );

                $item->update($data);

                $this->afterUpdate(
                    $item,
                    $request
                );
            });

            $this->deleteStoredFiles($oldFiles);

            return $this->successResponse(
                message: 'Data berhasil diperbarui.',
                data: $item
                    ->fresh()
                    ->load($this->with)
            );
        } catch (Throwable $exception) {
            $this->deleteStoredFiles($newFiles);

            throw $exception;
        }
    }

    //Menghapus data.
    public function destroy(int|string $id): JsonResponse
    {
        $item = $this->newQuery()
            ->findOrFail($id);

        if (!$this->canDelete($item)) {
            return $this->errorResponse(
                message: $this->deleteDeniedMessage($item),
                status: 422
            );
        }

        $filesToDelete = $this->getModelFiles($item);

        DB::transaction(function () use ($item) {
            $this->beforeDelete($item);

            $item->delete();

            $this->afterDelete($item);
        });

        $this->deleteStoredFiles($filesToDelete);

        return $this->successResponse(
            message: 'Data berhasil dihapus.'
        );
    }

    //Membuat query model.
    protected function newQuery(): Builder
    {
        /** @var Model $model */
        $model = new $this->modelClass();

        return $model->newQuery();
    }

    //Pencarian berdasarkan field yang diizinkan.
    protected function applySearch(
        Builder $query,
        Request $request
    ): Builder {
        $keyword = trim(
            (string) $request->query('search', '')
        );

        if (
            $keyword === '' ||
            empty($this->searchable)
        ) {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use (
            $keyword
        ) {
            foreach ($this->searchable as $field) {
                $subQuery->orWhere(
                    $field,
                    'like',
                    '%' . $keyword . '%'
                );
            }
        });
    }

    //Hook filter tambahan untuk controller turunan.
    protected function applyFilters(
        Builder $query,
        Request $request
    ): Builder {
        return $query;
    }

    //Pengurutan data.
    protected function applySorting(
        Builder $query,
        Request $request
    ): Builder {
        $model = $query->getModel();
        $sortBy = $request->query(
            'sort_by',
            $model->getCreatedAtColumn()
        );

        $sortDirection = strtolower(
            (string) $request->query(
                'sort_direction',
                'desc'
            )
        );
        if (!in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = 'desc';
        }
        if (!$model->usesTimestamps()) {
            $sortBy = $model->getKeyName();
        }

        $allowedSorts = [
            $model->getKeyName(),
        ];

        if ($model->usesTimestamps()) {
            $allowedSorts[] = $model->getCreatedAtColumn();
            $allowedSorts[] = $model->getUpdatedAtColumn();
        }
        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = $model->getKeyName();
        }
        return $query->orderBy(
            $sortBy,
            $sortDirection
        );
    }

    //Mengatur jumlah data per halaman.
    protected function resolvePerPage(Request $request): int
    {
        $perPage = (int) $request->query(
            'per_page',
            $this->perPage
        );

        if ($perPage < 1) {
            return $this->perPage;
        }

        return min(
            $perPage,
            $this->maxPerPage
        );
    }

    //Hook untuk memodifikasi data sebelum create.
    protected function prepareDataForStore(
        array $data,
        Request $request
    ): array {
        return $data;
    }

    //Hook untuk memodifikasi data sebelum update.
    protected function prepareDataForUpdate(
        array $data,
        Request $request,
        Model $item
    ): array {
        return $data;
    }

    //Menyimpan file upload
    protected function storeUploadedFiles(
        Request $request,
        array $data,
        ?Model $item = null
    ): array {
        $newFiles = [];
        $oldFiles = [];

        foreach ($this->files as $field => $directory) {
            if (!$request->hasFile($field)) {
                continue;
            }

            $uploadedFile = $request->file($field);
            if (!$uploadedFile || !$uploadedFile->isValid()) {
                continue;
            }

            $path = $uploadedFile->store(
                $directory,
                'public'
            );

            $data[$field] = $path;
            $newFiles[] = $path;

            if (
                $item &&
                filled($item->getAttribute($field))
            ) {
                $oldFiles[] = $item->getAttribute($field);
            }
        }
        return [
            $data,
            $newFiles,
            $oldFiles,
        ];
    }

    //Mengambil semua file yang dimiliki model.
    protected function getModelFiles(Model $item): array
    {
        $files = [];

        foreach ($this->files as $field => $directory) {
            $path = $item->getAttribute($field);
            if (filled($path)) {
                $files[] = $path;
            }
        }
        return $files;
    }

    // Menghapus kumpulan file dari disk public
    protected function deleteStoredFiles(array $files): void
    {
        $files = array_values(
            array_filter(
                array_unique($files)
            )
        );

        if (!empty($files)) {
            Storage::disk('public')->delete($files);
        }
    }

    //Apakah data boleh dihapus
    protected function canDelete(Model $item): bool
    {
        return true;
    }

    //Pesan ketika data tidak boleh dihapus
    protected function deleteDeniedMessage(Model $item): string
    {
        return 'Data tidak dapat dihapus.';
    }

    // Hook sesudah data dibuat
    protected function afterStore(
        Model $item,
        Request $request
    ): void {
        //
    }

    // Hook sesudah data diperbarui
    protected function afterUpdate(
        Model $item,
        Request $request
    ): void {
        //
    }

    //Hook sebelum data dihapus
    protected function beforeDelete(Model $item): void
    {
        //
    }

    //Hook sesudah data dihapus
    protected function afterDelete(Model $item): void
    {
        //
    }

    //Format respons berhasil
    protected function successResponse(
        mixed $data = null,
        ?string $message = null,
        int $status = 200
    ): JsonResponse {
        $response = [
            'success' => true,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json(
            $response,
            $status
        );
    }

    // Format Respon Gagal
    protected function errorResponse(
        string $message,
        int $status = 422,
        mixed $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json(
            $response,
            $status
        );
    }
}