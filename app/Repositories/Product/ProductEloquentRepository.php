<?php

namespace App\Repositories\Product;

use App\Repositories\EloquentRepository;
use Elasticsearch\ClientBuilder;
use App\Model\Product;

class ProductEloquentRepository extends EloquentRepository implements ProductRepositoryInterface
{
    public function getModel()
    {
        return Product::class;
    }

    /**
	 *
	 * Search Product By Keyword
	 *
	 * @param $keyword
     * @param $page
	 * @param $options
	 * @return array
	 *
	 */
    public function searchByKeyword($q = null, $page = 1, $options = null)
    {
    	$limit = config('constants.rowPageProduct');
    	$offset = ($page > 1) ? ($page - 1) * $limit : 0;
        if (!isset($options['category'])) {
            if ($q != null) {
                $query = [
                    'match_phrase_prefix' => [
                        'title' => $q
                    ]
                ];
            } else {
                $query = ['match_all' => new \stdClass()];
            }
        } else {
            $category = explode(',', $options['category']);
            if ($q != null) {
                $query = [
                    'bool' => [
                        'must' => [
                            'match_phrase_prefix' => [
                                'title' => $q
                            ]
                        ],
                        'filter' => [
                            'terms' => [
                                'category_id' => $category
                            ]
                        ]
                    ]
                ];
            } else {
                $query = [
                    'terms' => [
                        'category_id' => $category
                    ]
                ];
            }
        }

        /*$bool = [
            'must' => [
                'match' => [
                    'is_delete' => 0
                ]
            ],
            'should' => [
                $should
            ]
        ];

        if (isset($options['start_year']) && isset($options['end_year'])) {
            $bool = [
                'must' => [
                    'match' => [
                        'is_delete' => 0
                    ]
                ],
                'filter' => [
                    'range' => [
                        'updated_at' => [
                            'gte' => $options['start_year'],
                            'lte' => $options['end_year'],
                            'format' => 'yyyy||yyyy'
                        ]
                    ],
                ],
                'should' => [
                    $should
                ]
            ];
        }

        $query = [
            'constant_score' => [
                'filter' => [
                    'bool' => $bool
                ]
            ]
        ];*/

        $sort = ['_id' => 'desc'];
        if (isset($options['sort'])) {
            $sort = ['_id' => $options['sort']];
        }

        $params = [
            'index' => config('constants.elasticsearch.product.index'),
            'type' => config('constants.elasticsearch.product.type'),
            'body' => [
                'from' => $offset,
                'size' => $limit,
                'query' => $query,
                'sort' => $sort
            ]
        ];

        $client = ClientBuilder::create()->build();
        $response = $client->search($params);

        $result = [];
        if (!empty($response)) {
            $result['total'] = $response['hits']['total'];
            $result['hits'] = $response['hits']['hits'];
        }

        return $result;
    }
}