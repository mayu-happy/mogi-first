<?php
namespace App\Http\Controllers;
use App\Models\Item;
class ItemController extends Controller
{
    public function index(){
        $q = Item::query()->latest();
        if ($kw = request('keyword')) {
            $q->where(function($qq) use ($kw){
                $like = '%'.$kw.'%';
                $qq->where('name','like',$like)->orWhere('brand','like',$like)->orWhere('description','like',$like);
            });
        }
        $items = $q->paginate(12)->withQueryString();
        return view('items.index', compact('items','kw'));
    }
    public function show(Item $item){ return view('items.show', compact('item')); }
}
