<?php

class FormatController extends \BaseController {

    /**
     * Display a listing of the resource.
     * @return string
     * @internal param int $page
     * @internal param string $query
     */
    public function index()
	{
        $pageItems = 20;
        $page = (int) Input::get('page');
        $page = max($page, 1);
        $query = Input::get('query');
        $getAll = Input::get('getAll');
        if ($getAll) {
            return Response::json(['status' => 200, 'data' => Format::all()]);
        }
        if ($page > 0)
        {
            Paginator::setCurrentPage($page);
        }
        $orderBy = Input::get('orderBy');
        $orderDir = Input::get('orderDir');
//        protected $fillable = ['name', 'label', 'type', 'extension'];
        switch($orderBy)
        {
            case "name":
            case "label":
            case "type":
            case "position":
            case "extension":
            case "command":
                break;
            default:
                $orderBy = "name";
                $orderDir = 'asc';
        }
        if($orderDir == 'false')
        {
            $orderDir = 'desc';
        }
        else
        {
            $orderDir = 'asc';
        }
        $data = Format::where('name', 'LIKE', '%'.$query.'%')->orderBy($orderBy, $orderDir)->paginate($pageItems, ['id', 'name', 'label', 'type', 'position', 'active', 'extension']);
        $pagination = [
            'last' => $data->getLastPage(),
            'page' => $data->getCurrentPage(),
            'perPage' => $data->getPerPage(),
            'total' => $data->getTotal(),
            'orderBy' => $orderBy,
            'orderDir' => $orderDir == 'asc',
            'query' => $query,
        ];
        return Response::json(['status' => 200, 'data' => $data->getItems(), 'pagination' => $pagination]);
	}


	/**
	 * Store a newly created resource in storage.
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
        $input = Input::only('name','label','type','position','extension','command','active');
        $rules = array(
            'name' => 'required|min:2|max:128',
            'label' => 'required|min:2|max:128',
            'position' => 'required|integer|between:0,100',
            'type' => 'required|integer|between:0,3',
            'extension' => 'required|alpha_num|min:2|max:8',
            'active' => 'required|boolean',
            'command' => 'required',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        } else {
            // transformar el comando en json
            $format = Format::create($input);
            return Response::json(['status' => 200, 'flash' => Lang::get('format.formatSaved'), 'format' => $format]);
        }
	}


	/**
	 * Display the specified resource.
	 * @param  int  $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($id)
	{
        $contest = Format::find($id);
        return Response::json($contest, 200, [], JSON_NUMERIC_CHECK);
	}


	/**
	 * Update the specified resource in storage.
	 * @param  int  $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id)
	{
        $input = Input::only('name','label','type','position','extension','command','active');
        $rules = array(
            'name' => 'required|min:2|max:128',
            'label' => 'required|min:2|max:128',
            'position' => 'required|integer|between:0,100',
            'type' => 'required|integer|between:0,3',
            'extension' => 'required|alpha_num|min:2|max:8',
            'active' => 'required|boolean',
            'command' => 'required',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return Response::json(array('errors'=>$messages));
        } else {
            $format = Format::find($id)->update($input);
            return Response::json(['status' => 200, 'flash' => Lang::get('format.formatSaved'), 'format' => $format]);
        }
	}

	/**
	 * Remove the specified resource from storage.
	 * @param  int  $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function destroy($id)
	{
        $contest = Format::destroy($id);
        return Response::json($contest);
	}
}