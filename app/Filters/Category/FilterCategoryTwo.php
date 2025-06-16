<?php
namespace App\Filters\Category;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

use function PHPUnit\Framework\isNull;

class FilterCategoryTwo implements Filter
{
    public function __invoke(Builder $query, $value, string $property ): Builder
    {
        $parentId = request()->get('parentId');
        return $query->where(function ($q) use ($value, $parentId) {
            $q->where('name', 'like', '%' . $value . '%');

            if (!is_null($parentId) && $parentId !== '') {
                $q->where('parent_id', $parentId);
            }else{
                $q->where('parent_id', null);
            }
        });
        // ->orWhereHas('subCategories', function ($subQuery) use ($value) {
        //     $subQuery->where('name', 'like', '%' . $value . '%');
        // });
        
    }
}
?>