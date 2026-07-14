<?php

namespace Raza9798\LaravelCoreModules\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ResourceService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $model, $id, $module, $key
     * @param integer $id primary key
     * @param Model $model
     * @param array $with relationships to eager load
     * @return Object
     */
    public function getById($id, $model, $with = [])
    {
        $modelData = $model::with($with)->find($id);
        if ($modelData == null) {
            return self::DataNullResponse();
        }
        return $modelData;
    }

    /**
     * @param $model, $data
     * @param Model $model
     * @param array $data fillable data
     * @param string $key column name for render data
     * @return Object
     */
    public function insert($model, array $data, $key)
    {
        try {
            DB::beginTransaction();
            $modelData = $model::create($data);
            DB::commit();
            return $modelData;
        } catch (\Exception $error) {
            DB::rollBack();
            return self::errorHandler($error);
        }
    }

    /**
     * @param $model, $id, $data, $module, $key
     * @param Model $model
     * @param integer $id primary key
     * @param array $data fillable data
     * @param string $module module name
     * @param string $key column name for render data
     * @return Object
     */
    public function modify($model, int $id, array $data, $key)
    {
        try {
            DB::beginTransaction();
            $modelData = $model::find($id);
            if ($modelData == null) {
                return self::DataNullResponse();
            }
            $modelData->update($data);
            DB::commit();
            return $modelData;
        } catch (\Exception $error) {
            DB::rollBack();
            return self::errorHandler($error);
        }
    }

    /**
     * @param $model, $id, $module, $key
     * @param Model $model
     * @param integer $id primary key
     * @param string $module module name
     * @param string $key column name for render data
     * @return Object
     */
    public function delete($model, int $id, $key)
    {
        try {
            DB::beginTransaction();
            $modelData = $model::find($id);
            if ($modelData == null) {
                return self::DataNullResponse();
            }
            $modelData->delete();
            DB::commit();
            return $modelData;
        } catch (\Exception $error) {
            DB::rollBack();
            return self::errorHandler($error);
        }
    }

    /**
     * @return void
     */
    public function apiUpdateOrStore($controller, $data, $headerId = null, $headerKey = null)
    {
        try {
            foreach ($data as $item) {
                $item[$headerKey] = $headerId;
                if (isset($item['id'])) {
                    (new $controller)->update(new Request($item), $item['id']);
                } else {
                    (new $controller)->store(new Request($item));
                }
            }
        } catch (\Exception $error) {
            return self::errorHandler($error);
        }
    }

    public function DataNullResponse()
    {
        return [
            'message' => "Data not found",
            'status' => 406
        ];
    }

    public function DataNullResponseWithCustomMessage($message)
    {
        return [
            'message' => $message,
            'status' => 406
        ];
    }

    public function errorHandler($error)
    {
        $validation_exception_type = 'Illuminate\Validation\ValidationException';
        $http_status = get_class($error) == $validation_exception_type ? 422 : 500;

        return [
            'success' => false,
            'message' => 'SERVER ERROR FOUND, REPORT TO DEVELOPER',
            'error' => get_class($error) == $validation_exception_type ? $error->errors() : $error->getMessage(),
            'file' => $error->getFile(),
            'controller' => request()?->route()?->getAction()['controller'] ?? null,
            'line' => $error->getLine(),
            'code' => $error->getCode(),
            'trace' => $error->getTraceAsString(),
            'status' => $http_status,
            'exception_message' => $error->getCode() == 412 ? $error->getMessage() : null,
        ];
    }
}