<?php
namespace Webkul\Admin\Helpers;
class FilterCollection{
    /**
     * @var array
     */
    protected $operators = [
        'eq'       => "=",
        'lt'       => "<",
        'gt'       => ">",
        'lte'      => "<=",
        'gte'      => ">=",
        'neqs'     => "<>",
        'neqn'     => "!=",
        'eqo'      => "<=>",
        'like'     => "like",
        'blike'    => "like binary",
        'nlike'    => "not like",
        'ilike'    => "ilike",
        'and'      => "&",
        'bor'      => "|",
        'regex'    => "regexp",
        'notregex' => "not regexp",
    ];
    public function filterCollection($query, $searchfields)
    {
        $parsedUrl = $this->parseUrl();
        foreach ($parsedUrl as $key => $value) {
            if ($key === 'locale') {
                if (! is_array($value)) {
                    unset($parsedUrl[$key]);
                }
            } elseif (! is_array($value)) {
                unset($parsedUrl[$key]);
            }
        }
        if (count($parsedUrl)) {
            
            return $this->sortOrFilterCollection($query,$parsedUrl,$searchfields);
        } else {
            return $query;
        }
        
    }

    private function parseUrl()
    {
        $parsedUrl = [];
        $unparsed = url()->full();

        $route = request()->route() ? request()->route()->getName() : "";

        if ($route == 'admin.datagrid.export') {
            $unparsed = url()->previous();
        }

        $getParametersArr = explode('?', $unparsed);
        if (count($getParametersArr) > 1) {
            $to_be_parsed = $getParametersArr[1];
            $to_be_parsed = urldecode($to_be_parsed);

            parse_str($to_be_parsed, $parsedUrl);
            unset($parsedUrl['page']);
        }

        if (isset($parsedUrl['grand_total'])) {
            foreach ($parsedUrl['grand_total'] as $key => $value) {
                $parsedUrl['grand_total'][$key] = str_replace(',', '.', $parsedUrl['grand_total'][$key]);
            }
        }
        return $parsedUrl;
    }

     /**
     * @param \Illuminate\Support\Collection $collection
     * @param array                          $parseInfo
     *
     * @return \Illuminate\Support\Collection
     */
    public function sortOrFilterCollection($collection, $parseInfo,$searchFields)
    {
        foreach ($parseInfo as $key => $info) {
            foreach ($searchFields as $column) {
                if ($column['key'] == $key) {
                    $columnType = $column['columnType'];
                    $columnName = $column['value'];
                }
            }
            if ($key === "sort") {
            } elseif ($key === "search") {
            } else {
                if (array_keys($info)[0] === "like" || array_keys($info)[0] === "nlike") {
                    foreach ($info as $condition => $filter_value) {
                            $collection->where(
                                $columnName,
                                $this->operators[$condition],
                                '%' . $filter_value . '%'
                            );
                        
                    }
                } else {
                    foreach ($info as $condition => $filter_value) {
                        
                        if ($condition === 'undefined') {
                            $condition = '=';
                        }

                        if ($columnType === 'datetime') {
                           
                                $collection->whereDate(
                                    $columnName,
                                    $this->operators[$condition],
                                    $filter_value
                                );
                            
                        } elseif ($columnType === 'boolean') {
                                if ($this->operators[$condition] == '=') {
                                    if ($filter_value == 1) {
                                        $collection->Where(function($query) use($columnName, $condition, $filter_value) {
                                            $query->where(
                                            $columnName,
                                            $this->operators[$condition],
                                            $filter_value
                                            )->orWhereNotNull($columnName);
                                        });
                                    } else {
                                        $collection->Where(function($query) use($columnName, $condition, $filter_value) {
                                            $query->where(
                                            $columnName,
                                            $this->operators[$condition],
                                            $filter_value
                                            )->orWhereNull($columnName);
                                        });
                                    }
                                } elseif ($this->operators[$condition] == '<>') {
                                    if ($filter_value == 1) {
                                        $collection->Where(function($query) use($columnName, $condition, $filter_value) {
                                            $query->where(
                                            $columnName,
                                            $this->operators[$condition],
                                            $filter_value
                                            )->orWhereNull($columnName);
                                        });
                                    } else {
                                        $collection->Where(function($query) use($columnName, $condition, $filter_value) {
                                            $query->where(
                                            $columnName,
                                            $this->operators[$condition],
                                            $filter_value
                                            )->orWhereNotNull($columnName);
                                        });
                                    }
                                } else {
                                    $collection->Where(function($query) use($columnName, $condition, $filter_value) {
                                        $query->where(
                                        $columnName,
                                        $this->operators[$condition],
                                        $filter_value
                                        );
                                    });
                                }
                            
                        } else {
                                $collection->where(
                                    $columnName,
                                    $this->operators[$condition],
                                    $filter_value
                                );
                        }
                    }
                }
            }
        }

        return $collection;
    }

}

?>