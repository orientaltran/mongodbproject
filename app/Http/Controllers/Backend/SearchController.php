<?php

namespace App\Http\Controllers\Backend;

use App\Model\PostElastic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Elasticsearch\ClientBuilder;
use App\Helpers\Envato\Ulities;
use Illuminate\Support\Facades\Config;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $currentPage = 'bookIndex';

        $searchValue = !is_null($request->get('q'))? $request->get('q'): null;

        $page = $request->get('page');

        $rowPage = Config::get('constants.rowPage');

        if (!isset($page)) {
            $page = 1;
        }

        $post = new PostElastic();
        $client = ClientBuilder::create()->build();

        $matchAll = [
            'match_all' => new \stdClass()
        ];

        $matchPrefix = [
            'match_phrase_prefix'   => [
                'title' => $searchValue
            ]
        ];

        $param = [
            'index' => $post->getIndexName(),
            'type'  => $post->getTypeName(),
            'body'  => [
                'from'  => ($page - 1) * $rowPage,
                'size'  => $rowPage,
                'query' => is_null($searchValue) ? $matchAll : $matchPrefix

            ]
        ];

        $data = $client->search($param);

        $paginate = Ulities::calculatorPage($searchValue, $page, $data['hits']['total'], $rowPage);

        $q = $searchValue;

        return view('Backend.Search.index' , compact(['currentPage', 'data', 'q', 'paginate']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
