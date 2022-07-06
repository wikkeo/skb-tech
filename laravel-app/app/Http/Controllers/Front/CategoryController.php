<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\System\Sort;
use Illuminate\Http\Request;
use App\Models\Category;
use DB;
use Validator;

class CategoryController extends Controller
{
    /**
     * Возвращает список всех категорий
     *
     * @param $request Параметры запроса в json
     * @return json
     */
    public function list(Request $request) {
        $input = $request->all();
        // сортировка
        if (!empty($input['sort'])) {
            $sort = (new Sort($input['sort']))
                ->parce()
                ->getRaw();
        } else {
            $sort = 'createdDate desc';
        }
        
        $categories = Category::orderByRaw($sort);

        if (!empty($input['search'])) {
            $search_string = '%' . trim($input['search']) . '%';
            $categories = $categories->whereRaw("name like :name or description like :description", [
                'name' => $search_string,
                'description' => $search_string
            ]);
        } else {
            // фильтрация
            if (!empty($input['filter'])) {
                // собираем из key:value
                foreach ($input['filter'] as $filter_item) {
                    $categories->whereRaw($filter_item['name'] . ' like :' . $filter_item['name'], [
                        $filter_item['name'] => '%' . trim($filter_item['value']) . '%'
                    ]);
                } 
            }
        }
        return response()->json($categories->get());
    }

    /**
     * Создает модель категории, заполняет и сохраняет
     *
     * @param $request Параметры запроса в json
     * @return json
     */
    public function create(Request $request) {
        $data = $request->all();
        $this->validate($request, [
            'slug' => 'required|string',
            'name' => 'required|string',
            'description' => 'string|nullable'
        ]);
        $category = new Category();
        $category->fill($data)->save();
    }

    /**
     * Обновляет модель категории, заполняет и сохраняет
     *
     * @param $request Параметры запроса в json
     * @return json
     */
    public function update(Request $request) {
        $input = $request->all();
        $this->validate($request, [
            'id' => 'required|integer|gt:0',
            'slug' => 'string|nullable',
            'name' => 'string|nullable',
            'description' => 'string|nullable',
            'active' => 'integer'
        ]);
        $data = Category::findOrFail((int)$input['id']);
        $data->update($input);
    }

    /**
     * Удаляет модель категории
     *
     * @param $request Параметры запроса в json
     * @return json
     */
    public function delete(Request $request) {
        $in = $request->all();
        $this->validate($request, [
            'id' => 'required|integer|gt:0'
        ]);
        Category::destroy((int)$in['id']);
    }
}
