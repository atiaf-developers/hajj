<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\News;
use App\Models\NewsTranslation;
use Validator;
use DB;


class NewsController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:news,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:news,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:news,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:news,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('news/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('news/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if ($request->file('image')) {
            $this->rules['image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
        }
        $columns_arr = array(
            'description' => 'required'
        );
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $news = new News;
            $news->active = $request->input('active');
            $news->this_order = $request->input('this_order');
            if ($request->file('image')) {
               $news->image = News::upload($request->file('image'), 'news', true);
            }
            $news->save();
            
            $news_translations = array();

            foreach ($request->input('description') as $key => $value) {
                $news_translations[] = array(
                    'locale' => $key,
                    'description' => $value,
                    'news_id' => $news->id
                );
            }
            NewsTranslation::insert($news_translations);
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
             DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = News::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $news = News::find($id);

        if (!$news) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $news_translations = NewsTranslation::where('news_id',$id)->pluck('description','locale');

        $this->data['news'] = $news;
        $this->data['news_translations'] = $news_translations;

        return $this->_view('news/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $news = News::find($id);

        if (!$news) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        if ($request->file('image')) {
            $this->rules['image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
        }
        $columns_arr = array(
            'description' => 'required'
        );
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $news->active = $request->input('active');
            $news->this_order = $request->input('this_order');
            if ($request->file('image')) {
                if ($news->image) {
                    $old_image = $news->image;
                    News::deleteUploaded('news', $old_image);
                }
                $news->image = News::upload($request->file('image'), 'news', true);
            }
            $news->save();
            
            $news_translations = array();

            NewsTranslation::where('news_id', $news->id)->delete();

            foreach ($request->input('description') as $key => $value) {
                $news_translations[] = array(
                    'locale' => $key,
                    'description' => $value,
                    'news_id' => $news->id
                );
            }
            NewsTranslation::insert($news_translations);

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            dd($ex);
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $news = News::find($id);
        if (!$news) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $news->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {

        $news = News::Join('news_translations', 'news.id', '=', 'news_translations.news_id')
                ->where('news_translations.locale', $this->lang_code)
                ->select([
            'news.id', "news_translations.description", "news.this_order", 'news.active',
        ]);

        return \Datatables::eloquent($news)
        ->addColumn('options', function ($item) {

            $back = "";
            if (\Permissions::check('news', 'edit') || \Permissions::check('news', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('news', 'edit')) {
                    $back .= '<li>';
                    $back .= '<a href="' . route('news.edit', $item->id) . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('news', 'delete')) {
                    $back .= '<li>';
                    $back .= '<a href="" data-toggle="confirmation" onclick = "News.delete(this);return false;" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                $back .= '</ul>';
                $back .= ' </div>';
            }
            return $back;
        })
        ->editColumn('description', function ($item) {
            $back = str_limit($item->description, $limit = 150, $end = '...');
            return $back;
        })
        ->addColumn('active', function ($item) {
                          if ($item->active == 1) {
                          $message = _lang('app.active');
                          $class = 'label-success';
                          } else {
                          $message = _lang('app.not_active');
                          $class = 'label-danger';
                          }
                          $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                          return $back;
                      }) 
                      ->escapeColumns([])
                      ->make(true);
    }

              
}
